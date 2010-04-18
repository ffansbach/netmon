<?php

// +---------------------------------------------------------------------------+
// index.php
// Netmon, Freifunk Netzverwaltung und Monitoring Software
//
// Copyright (c) 2009 Clemens John <clemens-john@gmx.de>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 3
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+/

/**
 * This file contains the class for the service site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

require_once $path.'lib/classes/extern/xmpphp/XMPP.php';

class Service {
	public function getCrawlHistory($service_id, $count) {
		$last_crawl = array();
		try {
			$sql = "SELECT * FROM crawl_data
					WHERE service_id='$service_id' ORDER BY id DESC LIMIT $count";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$last_crawl[] = $row;
			}
		 }
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $last_crawl;
	}

	public function emailIfDown($service_data, $user_data, $history) {
		$text = "Hallo $user_data[nickname],

der Service $GLOBALS[net_prefix].$service_data[ip]:$service_data[typ] ist seit dem ".date("d.m H:i", strtotime($history[$service_data['notification_wait']-1]['crawl_time']))." uhr offline.
Siehe http://$GLOBALS[domain]/$GLOBALS[subfolder]/service.php?service_id=$service_data[service_id]

Bitte stelle den Service zur erhaltung des Meshnetzwerkes wieder zur Verfügung oder entferne den Service.

Mit freundlichen Gruessen
$GLOBALS[community_name]";

if ($GLOBALS['mail_sending_type']=='smtp') {
	$config = array('username' => $GLOBALS['mail_smtp_username'],
			'password' => $GLOBALS['mail_smtp_password']);

	if(!empty($GLOBALS['mail_smtp_ssl']))
		$config['ssl'] = $GLOBALS['mail_smtp_ssl'];
	if(!empty($GLOBALS['mail_smtp_login_auth']))
		$config['auth'] = $GLOBALS['mail_smtp_login_auth'];

	$transport = new Zend_Mail_Transport_Smtp($GLOBALS['mail_smtp_server'], $config);
}

$mail = new Zend_Mail();

$mail->setFrom($GLOBALS['mail_sender_adress'], $GLOBALS['mail_sender_name']);
$mail->addTo($user_data['email']);
$mail->setSubject("Service offline $GLOBALS[community_name]");
$mail->setBodyText($text);

$mail->send($transport);
	}

	public function jabberIfDown($service_data, $user_data, $history) {
		$conn = new XMPPHP_XMPP($GLOBALS['jabber_server'], 5222, $GLOBALS['jabber_username'], $GLOBALS['jabber_password'], 'xmpphp', $server=null, $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
		
		try {
			$conn->connect();
			$conn->processUntil('session_start');
			$conn->presence();
			$message = "Hallo $user_data[nickname],

der Service $GLOBALS[net_prefix].$service_data[ip]:$service_data[typ] ist seit dem ".date("d.m H:i", strtotime($history[$service_data['notification_wait']-1]['crawl_time']))." uhr offline.
Siehe http://$GLOBALS[url_to_netmon]/service.php?service_id=$service_data[service_id]

Bitte stelle den Service zur Erhaltung des Meshnetzwerkes wieder zur Verfuegung oder entferne den Service.

Mit freundlichen Gruessen
$GLOBALS[community_name]";
			$conn->message($user_data['jabber'], $message);
			$conn->disconnect();
		} catch(XMPPHP_Exception $e) {
			die($e->getMessage());
		}
	}

	public function offlineNotification($service_id) {
		$service_data = Helper::getServiceDataByServiceId($service_id);
		if($service_data['notify']==1) {
			$user_data = Helper::getUserByID($service_data['user_id']);
			$history = Service::getCrawlHistory($service_id, $service_data['notification_wait']);
			if (!empty($history)) {
				if(count($history)>=$service_data['notifiaction_wait']) {
					//Wenn der Serivice in der notification_wait einmal online gecrawlt wurde, beende.
					$online = false;
					foreach($history as $hist) {
						if ($hist['status']=="online") {
							$online = true;
							break;
						}
					}
					
					//Wenn offline 
					if (!$online AND $service_data['notified']!=1) {
						if($user_data['notification_method']=="email") {
							Service::emailIfDown($service_data, $user_data, $history);
						} elseif ($user_data['notification_method']=="jabber") {
							Service::jabberIfDown($service_data, $user_data, $history);
						}
						DB::getInstance()->exec("UPDATE services SET
														notified = 1,
														last_notification = NOW()
												WHERE id = '$service_id'");
					}
				}
			}
		}
	}

  public function insertStatus($current_crawl_data, $service_id) {
		$current_crawl_data['luciname'] = addslashes($current_crawl_data['luciname']);

    DB::getInstance()->exec("INSERT INTO crawl_data (
service_id,
crawl_time,
ping,
status,
nickname,
hostname,
email,
location,
prefix,
ssid,
longitude,
latitude,
luciname,
luciversion,
distname,
distversion,
chipset,
cpu,
network,
wireless_interfaces,
uptime,
idletime,
memory_total,
memory_caching,
memory_buffering,
memory_free,
loadavg,
processes
) VALUES (
'$service_id',
NOW(),
'$current_crawl_data[ping]',
'$current_crawl_data[status]',
'$current_crawl_data[nickname]',
'$current_crawl_data[hostname]',
'$current_crawl_data[email]',
'$current_crawl_data[location]',
'$current_crawl_data[prefix]',
'$current_crawl_data[ssid]',
'$current_crawl_data[longitude]',
'$current_crawl_data[latitude]',
'$current_crawl_data[luciname]',
'$current_crawl_data[luciversion]',
'$current_crawl_data[distname]',
'$current_crawl_data[distversion]',
'$current_crawl_data[chipset]',
'$current_crawl_data[cpu]',
'$current_crawl_data[network]',
'$current_crawl_data[wireless_interfaces]',
'$current_crawl_data[uptime]',
'$current_crawl_data[idletime]',
'$current_crawl_data[memory_total]',
'$current_crawl_data[memory_caching]',
'$current_crawl_data[memory_buffering]',
'$current_crawl_data[memory_free]',
'$current_crawl_data[loadavg]',
'$current_crawl_data[processes]'
);");
    return DB::getInstance()->lastInsertId();
  }

	public function clearCrawlDatabase($service_id) {
		DB::getInstance()->exec("DELETE FROM crawl_data WHERE TO_DAYS(crawl_time)+31 < TO_DAYS(NOW()) AND service_id='$service_id'");
	}

	public function makeHistoryEntry($current_crawl_data, $last_crawl_data, $last_online_crawl_data, $service_id){
		if (!empty($current_crawl_data['status']) AND $current_crawl_data['status']!=$last_crawl_data['status']) {
			$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'status', 'from'=>$last_crawl_data['status'], 'to'=>$current_crawl_data['status']));
		}
		if($current_crawl_data['status']=='online') {
			if (!empty($current_crawl_data['luciname']) AND !empty($last_online_crawl_data['luciname']) AND $current_crawl_data['luciname']!=$last_online_crawl_data['luciname']) {
				$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'luciname', 'from'=>$last_crawl_data['luciname'], 'to'=>$current_crawl_data['luciname']));
			}
			if (!empty($current_crawl_data['luciversion']) AND !empty($last_online_crawl_data['luciversion']) AND $current_crawl_data['luciversion']!=$last_online_crawl_data['luciversion']) {
				$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'luciversion', 'from'=>$last_crawl_data['luciversion'], 'to'=>$current_crawl_data['luciversion']));
			}
			if (!empty($current_crawl_data['distname']) AND !empty($last_online_crawl_data['distname']) AND $current_crawl_data['distname']!=$last_online_crawl_data['distname']) {
				$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'distname', 'from'=>$last_crawl_data['distname'], 'to'=>$current_crawl_data['distname']));
			}
			if (!empty($current_crawl_data['distversion']) AND !empty($last_online_crawl_data['distversion']) AND $current_crawl_data['distversion']!=$last_online_crawl_data['distversion']) {
				$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'distversion', 'from'=>$last_crawl_data['distversion'], 'to'=>$current_crawl_data['distversion']));
			}
		}

		if($current_crawl_data['status']=='online' AND $last_crawl_data['status']=='online') {
			if (!empty($current_crawl_data['uptime']) AND !empty($last_online_crawl_data['uptime']) AND $current_crawl_data['uptime']<$last_crawl_data['uptime']) {
				$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'reboot'));
			}
		}

		if (is_array($history_data)) {
			foreach ($history_data as $hist_data) {
				DB::getInstance()->exec("INSERT INTO history (object, object_id, create_date, data) VALUES ('service', '$service_id', NOW(), '$hist_data');");
			}
		}
	}

	public function clearHistory($service_id) {
		DB::getInstance()->exec("DELETE FROM history WHERE TO_DAYS(create_date)+$GLOBALS[days_to_keep_portal_history] < TO_DAYS(NOW()) AND object_id='$service_id'");
	}

	public function updateNotificationStatus($status, $service_id) {
		if ($status=="online") {
			DB::getInstance()->exec("UPDATE services SET
								notified = 0
						 WHERE id = '$service_id'");
		}
	}
}
?>