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

include 'lib/classes/extern/XMPPHP/XMPP.php';

class service {
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

der Service $GLOBALS[net_prefix].$service_data[subnet_ip].$service_data[node_ip]:$service_data[typ] ist seit dem ".date("d.m H:i", strtotime($history[$service_data['notification_wait']-1]['crawl_time']))." uhr offline.
Siehe http://$GLOBALS[domain]/$GLOBALS[subfolder]/service.php?service_id=49

Bitte stelle den Service zur erhaltung des Meshnetzwerkes wieder zur Verfügung oder entferne den Service.
Dein Freifunkteam $GLOBALS[city_name]";
		$ergebniss = mail($user_data['email'], "Freifunk Oldenburg: Service Down", $text, "From: $GLOBALS[mail_sender]");
	}

	public function jabberIfDown($service_data, $user_data, $history) {
		$conn = new XMPPHP_XMPP($GLOBALS['jabber_server'], 5222, $GLOBALS['jabber_username'], $GLOBALS['jabber_password'], 'xmpphp', $server=null, $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
		
		try {
			$conn->connect();
			$conn->processUntil('session_start');
			$conn->presence();
			$message = "Hallo $user_data[nickname],

der Service $GLOBALS[net_prefix].$service_data[subnet_ip].$service_data[node_ip]:$service_data[typ] ist seit dem ".date("d.m H:i", strtotime($history[$service_data['notification_wait']-1]['crawl_time']))." uhr offline.
Siehe http://$GLOBALS[domain]/$GLOBALS[subfolder]/service.php?service_id=49

Bitte stelle den Service zur Erhaltung des Meshnetzwerkes wieder zur Verfuegung oder entferne den Service.
Dein Freifunkteam $GLOBALS[city_name]";
			$conn->message($user_data['jabber'], $message);
			$conn->disconnect();
		} catch(XMPPHP_Exception $e) {
			die($e->getMessage());
		}
	}

	public function offlineNotification($service_id) {
	echo $service_id;
		$service_data = Helper::getServiceDataByServiceId($service_id);
		$user_data = Helper::getUserByID($service_data['user_id']);
		$history = service::getCrawlHistory($service_id, $service_data['notification_wait']);

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
				service::emailIfDown($service_data, $user_data, $history);
			} elseif ($user_data['notification_method']=="jabber") {
				service::jabberIfDown($service_data, $user_data, $history);
			}
			DB::getInstance()->exec("UPDATE services SET
										notified = 1,
										last_notification = NOW()
									 WHERE id = '$service_id'");
		}
	}
}
?>