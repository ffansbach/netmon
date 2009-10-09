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
 * This file contains the class to crawl the IP's from the Database
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

/*Dieses Script muss unabhängig vom Monitoring-Framework funktionieren!!!*/

//Informationen siehe: http://luci.freifunk-halle.net/Documentation/JsonRpcHowTo

  /**
  * KONFIGURATION
  */

  //FREIFUNKNETZ
  $GLOBALS['net_prefix'] = "10.18";

  //MYSQL
  $GLOBALS['mysql_host'] = "10.18.0.1";
  $GLOBALS['mysql_db'] = "freifunksql5";
  $GLOBALS['mysql_user'] = "freifunksql5";
  $GLOBALS['mysql_password'] = "blabla";
  $GLOBALS['mysql_enc'] = "utf8";

  mysql_connect($GLOBALS['mysql_host'], $GLOBALS['mysql_user'], $GLOBALS['mysql_password']);
  mysql_select_db($GLOBALS['mysql_db']);
  mysql_query("SET NAMES $GLOBALS[mysql_enc];") or die(mysql_error());

class JsonDataCollector {

  public $crawl_id;

  function initialiseCrawl() {
    mysql_query("INSERT INTO crawls (crawl_time_start) VALUES (NOW())");
    $crawl_id = mysql_insert_id();

    $this->crawl_id = $crawl_id;
    return $crawl_id;
  }

  function endCrawl() {
    mysql_query("UPDATE crawls SET crawl_time_end = NOW() where id=".$this->crawl_id);

    //Löscht Crawl-Daten die älter als 31 Tage sind.
    mysql_query("DELETE FROM crawl_data WHERE TO_DAYS(crawl_time)+31 < TO_DAYS(NOW())");
    mysql_query("DELETE FROM crawls WHERE TO_DAYS(crawl_time_end)+31 < TO_DAYS(NOW())");
  }

  function file_get_contents_curl($url) {
      $curl_handle=curl_init();
      curl_setopt($curl_handle,CURLOPT_URL,$url);
      curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,3);
      curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
      $data = curl_exec($curl_handle);
      curl_close($curl_handle);
      return $data;
  }

  function getJason($url) {
      $string = $this->file_get_contents_curl($url);
      //$string = file_get_contents($src);
      return json_decode($string);
  }

  public function pingHost($host) {
	$online=exec("ping $host -c 1 -w 1");
	if ($online)
		$ping = substr($online, -15, -9);
	else
		$ping =  false;
		
	return $ping;
  }
  
  function getServices($service_typ) {
    $ips = array();
    $sql = "SELECT services.id as service_id, services.crawler, services.typ,
				      ips.ip_ip,
				      subnets.subnet_ip
			       FROM services
			       LEFT JOIN ips on (ips.id=services.ip_id)
			       LEFT JOIN subnets on (subnets.id=ips.subnet_id)
			       WHERE services.typ='$service_typ'
			       ORDER BY services.id ASC";
	$query = mysql_query($sql) OR die(mysql_error());
    while ($row = mysql_fetch_assoc($query)){
      $ips[] = $row;
    }

    return $ips;
  }

  function getLastCrawlDataByServiceId($service_id) {
	$last_crawl_id = $this->crawl_id-1;
    $sql = "SELECT id, service_id, crawl_time, status, prefix, ssid, longitude, latitude, luciname, luciversion, distname, distversion, chipset, cpu, uptime
			FROM crawl_data
			WHERE crawl_id = $last_crawl_id AND service_id = $service_id";
	$query = mysql_query($sql) OR die(mysql_error());
    $crawl = mysql_fetch_assoc($query);

    return $crawl;
  }

  function getLastOnlineCrawlDataByServiceId($service_id) {
    $sql = "SELECT id, service_id, crawl_time, status, prefix, ssid, longitude, latitude, luciname, luciversion, distname, distversion, chipset, cpu, uptime
			FROM crawl_data
			WHERE service_id='$service_id' AND status='online' ORDER BY id DESC LIMIT 1";
	$query = mysql_query($sql) OR die(mysql_error());
    $crawl = mysql_fetch_assoc($query);

    return $crawl;
  }

  public function crawl($service_typ) {
    $services = $this->getServices($service_typ);
    foreach ($services as $service) {
		$ping = $this->pingHost("$GLOBALS[net_prefix].$service[subnet_ip].$service[ip_ip]");
		if ($service['crawler']=="json") {
			if ($ping)
				$json = $this->getJason("http://$GLOBALS[net_prefix].$service[subnet_ip].$service[ip_ip]/cgi-bin/luci/freifunk/status.json");
			$this->insertJsonIntoDB($ping, $json, $service['service_id']);
		} elseif ($service['crawler']=="ping") {
			$this->insertPingIntoDB($ping, $service['service_id']);
		} elseif ($service['typ']=="service" AND is_numeric($service['crawler'])) {
	if ($ping)
		$portcheck =  @fsockopen("$GLOBALS[net_prefix].$service[subnet_ip].$service[ip_ip]", $service['crawler'], $errno, $errstr, 2);
	$this->insertPortsIntoDB($ping, $portcheck, $service['service_id']);
      }
			unset($ping);
			unset($json);
			unset($portcheck);
    }
  }

	public function updateNotificationStatus($status) {
		if ($status = "online") {
			mysql_query("UPDATE services SET
								notified = 0
						 WHERE id = '$service_id'");
		}
	}

	public function insertPingIntoDB($ping, $service_id) {
		$data['crawl_id'] = $this->crawl_id;
		$data['service_id'] = $service_id;

		if ($ping) {
			$data['status'] = "online";
			$data['ping'] = $ping;
		} else {
			$data['status'] = "offline";
			$data['ping'] = 'NULL';
		}

		mysql_query("INSERT INTO crawl_data (crawl_id, service_id, crawl_time, ping, status)
					VALUES ('$data[crawl_id]', '$data[service_id]', NOW(), '$data[ping]', '$data[status]'
					);");

		$this->updateNotificationStatus($data['status']);
	}

	public function insertPortsIntoDB($ping, $port, $service_id) {
		$data['crawl_id'] = $this->crawl_id;
		$data['service_id'] = $service_id;
		$data['ping'] = $ping;

		if ($port) {
			$data['status'] = "online";
		} else {
			$data['status'] = "offline";
		}

		mysql_query("INSERT INTO crawl_data (crawl_id, service_id, crawl_time, ping, status)
					VALUES ('$data[crawl_id]', '$data[service_id]', NOW(), '$data[ping]', '$data[status]'
					);");

		$this->updateNotificationStatus($data['status']);
	}


  public function insertJsonIntoDB($ping, $json_obj, $service_id) {
    $data['crawl_id'] = $this->crawl_id;
    $data['service_id'] = $service_id;
	$data['ping'] = $ping;

    if ($json_obj)
      $data['status'] = "online";
    else
      $data['status'] = "offline";

$data['nickname'] = $json_obj->freifunk->contact->nickname;
$data['hostname'] = $json_obj->system->hostname;
$data['email'] = $json_obj->freifunk->contact->mail;
$data['location'] = $json_obj->freifunk->contact->location;
$data['prefix'] = $json_obj->freifunk->community->prefix;
$data['ssid'] = $json_obj->freifunk->community->ssid;
$data['longitude'] = $json_obj->geo->longitude;
$data['latitude'] = $json_obj->geo->latitude;
$data['luciname'] = $json_obj->firmware->luciname;
$data['luciversion'] = $json_obj->firmware->luciversion;
$data['distname'] = $json_obj->firmware->distname;
$data['distversion'] = $json_obj->firmware->distversion;
$data['chipset'] = $json_obj->system->sysinfo[0];
$data['cpu'] = $json_obj->system->sysinfo[1];
$data['network'] = serialize($json_obj->network);
$data['wireless_interfaces'] = serialize($json_obj->wireless->interfaces);
$data['uptime'] = round(($json_obj->system->uptime[0])/60/60, 2);
$data['idletime'] = round(($json_obj->system->uptime[1])/60/60, 2);
$data['memory_total'] = $json_obj->system->sysinfo[2];
$data['memory_caching'] = $json_obj->system->sysinfo[3];
$data['memory_buffering'] = $json_obj->system->sysinfo[4];
$data['memory_free'] = $json_obj->system->sysinfo[5];
$data['loadavg'] = $json_obj->system->loadavg[0];
$data['processes'] = $json_obj->system->loadavg[3];
$data['olsrd_hna'] = serialize($json_obj->olsrd->HNA);
$data['olsrd_neighbors'] = serialize($json_obj->olsrd->Neighbors);
$data['olsrd_links'] = serialize($json_obj->olsrd->Links);
$data['olsrd_mid'] = serialize($json_obj->olsrd->MID);
$data['olsrd_routes'] = serialize($json_obj->olsrd->Routes);
$data['olsrd_topology'] = serialize($json_obj->olsrd->Topology);

    $data['luciname'] = mysql_real_escape_string($data['luciname']);
    mysql_query("INSERT INTO crawl_data (

crawl_id,
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
processes,
olsrd_hna,
olsrd_neighbors,
olsrd_links,
olsrd_mid,
olsrd_routes,
olsrd_topology
) VALUES (
'$data[crawl_id]',
'$data[service_id]',
NOW(),
'$data[ping]',
'$data[status]',
'$data[nickname]',
'$data[hostname]',
'$data[email]',
'$data[location]',
'$data[prefix]',
'$data[ssid]',
'$data[longitude]',
'$data[latitude]',
'$data[luciname]',
'$data[luciversion]',
'$data[distname]',
'$data[distversion]',
'$data[chipset]',
'$data[cpu]',
'$data[network]',
'$data[wireless_interfaces]',
'$data[uptime]',
'$data[idletime]',
'$data[memory_total]',
'$data[memory_caching]',
'$data[memory_buffering]',
'$data[memory_free]',
'$data[loadavg]',
'$data[processes]',
'$data[olsrd_hna]',
'$data[olsrd_neighbors]',
'$data[olsrd_links]',
'$data[olsrd_mid]',
'$data[olsrd_routes]',
'$data[olsrd_topology]'
);");

		$this->updateNotificationStatus($data['status']);


	$history_data = array();
	$last_crawl_data = $this->getLastCrawlDataByServiceId($service_id);

	if ($data['status']!=$last_crawl_data['status']) {
		$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'status', 'from'=>$last_crawl_data['status'], 'to'=>$data['status']));
	}

/*	if($data['status']=='online') {
		if ($last_crawl_data['status']=='offline') {
			$last_crawl_data = $this->getLastOnlineCrawlDataByServiceId($service_id);
		}

/*		if (!empty($last_crawl_data)) {
			if ($last_crawl_data['distversion']!=$data['distversion']) {
				$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'distversion', 'from'=> $last_crawl_data['distversion'], 'to'=>$data['distversion']));
			}
			if ($last_crawl_data['luciversion']!=$data['luciversion']) {
				$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'luciversion', 'from'=> $last_crawl_data['luciversion'], 'to'=>$data['luciversion']));
			}
			if ($last_crawl_data['uptime']>$data['uptime']) {
				$history_data[] = serialize(array('service_id'=>$service_id, 'action'=>'reboot'));
			}
		}
	}*/

	foreach ($history_data as $hist_data) {
		mysql_query("INSERT INTO history (object, object_id, create_date, data) VALUES ('service', '$service_id', NOW(), '$hist_data');");
	}

/*	unset($history_data);
	unset($hist_data);
	unset($data);
	unset($last_crawl_data);*/

    return true;
  }

}


  $crawler = new JsonDataCollector;

  $crawler->initialiseCrawl();
  $crawler->crawl('node');
  $crawler->crawl('vpn');
  $crawler->crawl('service');
  $crawler->crawl('client');
  $crawler->endCrawl();

?>