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
  
  //Typ und Encoding Festlegen
  header("Content-Type: text/html; charset=UTF-8");

  //Pfad vom Root zu Netmon mit Slash am Ende
  $path_to_netmon = "/home/mynetmon/";

  //Lokale Konfiguration einbinden
  require_once($path_to_netmon.'config/config.local.inc.php');

  /**
  * WICHTIGE KLASSEN
  */

  //Klasse für Mysql-Verbindungen einbinden
  require_once($path_to_netmon.'lib/classes/core/mysql.class.php');
  //Klasse fürs Logging
  require_once($path_to_netmon.'lib/classes/core/logsystem.class.php');
  //Pear Klasse für Ping
  require_once($path_to_netmon.'lib/classes/extern/Ping.php');

class JsonDataCollector {

  public $crawl_id;

  function initialiseCrawl() {
    $db = new mysqlClass;
    $db->mysqlQuery("INSERT INTO crawls (crawl_time_start) VALUES (NOW())");
    $crawl_id = $db->getInsertID();
    unset($db);
    $this->crawl_id = $crawl_id;
    return $crawl_id;
  }

  function endCrawl() {
    $db = new mysqlClass;
    $db->mysqlQuery("UPDATE crawls SET crawl_time_end = NOW() where id=".$this->crawl_id);
    unset($db);
  }

  function file_get_contents_curl($url) {
      $curl_handle=curl_init();
      curl_setopt($curl_handle,CURLOPT_URL,$url);
      curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,1);
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
    // $online=exec("ping $ip -n 1");  // für WINDOZ
    if (!empty($online)) {
      return true;
    } else {
      return false;
    }
  }
  
  function getServices($service_typ) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT services.id as service_id, services.crawler, services.typ,
				      nodes.node_ip,
				      subnets.subnet_ip
			       FROM services
			       LEFT JOIN nodes on (nodes.id=services.node_id)
			       LEFT JOIN subnets on (subnets.id=nodes.subnet_id)
			       WHERE services.typ='$service_typ'
			       ORDER BY services.id ASC");
    while($row = mysql_fetch_assoc($result)) {
      $nodes[] = $row;
    }
    return $nodes;
    unset($db);
  }

  public function crawl($service_typ) {
    $services = $this->getServices($service_typ);
    foreach ($services as $service) {
      if ($service['crawler']=="json") {
	$json = $this->getJason("http://$GLOBALS[net_prefix].$service[subnet_ip].$service[node_ip]/cgi-bin/luci/freifunk/status.json");
	$this->isertIntoDB($json, $service['service_id']);
      } elseif ($service['crawler']=="ping") {
	$ping = $this->pingHost("$GLOBALS[net_prefix].$service[subnet_ip].$service[node_ip]");
	$this->isertIntoDB($ping, $service['service_id']);
      } elseif ($service['typ']=="service" AND is_numeric($service['crawler'])) {
	$portcheck =  @fsockopen("$GLOBALS[net_prefix].$service[subnet_ip].$service[node_ip]", $service['crawler'], $errno, $errstr, 2);
	$this->isertIntoDB($portcheck, $service['service_id']);
      }
    }
  }

  public function isertIntoDB($obj, $service_id) {
    $data['crawl_id'] = $this->crawl_id;
    $data['service_id'] = $service_id;

    if ($obj)
      $data['status'] = "online";
    else
      $data['status'] = "offline";




$data['nickname'] = $obj->freifunk->contact->nickname;
$data['hostname'] = $obj->system->hostname;
$data['email'] = $obj->freifunk->contact->mail;
$data['location'] = $obj->freifunk->contact->location;
$data['prefix'] = $obj->freifunk->community->prefix;
$data['ssid'] = $obj->freifunk->community->ssid;
$data['longitude'] = $obj->geo->longitude;
$data['latitude'] = $obj->geo->latitude;
$data['luciname'] = $obj->firmware->luciname;
$data['luciversion'] = $obj->firmware->luciversion;
$data['distname'] = $obj->firmware->distname;
$data['distversion'] = $obj->firmware->distversion;
$data['chipset'] = $obj->system->sysinfo[0];
$data['cpu'] = $obj->system->sysinfo[1];
$data['network'] = serialize($obj->network);
$data['wireless_interfaces'] = serialize($obj->wireless->interfaces);
$data['uptime'] = round(($obj->system->uptime[0])/60/60, 2);
$data['idletime'] = round(($obj->system->uptime[1])/60/60, 2);
$data['memory_total'] = $obj->system->sysinfo[2];
$data['memory_caching'] = $obj->system->sysinfo[3];
$data['memory_buffering'] = $obj->system->sysinfo[4];
$data['memory_free'] = $obj->system->sysinfo[5];
$data['loadavg'] = $obj->system->loadavg[0];
$data['processes'] = $obj->system->loadavg[3];
$data['olsrd_hna'] = serialize($obj->olsrd->HNA);
$data['olsrd_neighbors'] = serialize($obj->olsrd->Neighbors);
$data['olsrd_links'] = serialize($obj->olsrd->Links);
$data['olsrd_mid'] = serialize($obj->olsrd->MID);
$data['olsrd_routes'] = serialize($obj->olsrd->Routes);
$data['olsrd_topology'] = serialize($obj->olsrd->Topology);



    //Mach DB Eintrag
    $db = new mysqlClass;
    $data['luciname'] = mysql_real_escape_string($data['luciname']);
    $db->mysqlQuery("INSERT INTO crawl_data (

crawl_id,
service_id,
crawl_time,
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
    $ergebniss = $db->mysqlAffectedRows();
    unset($db);
    return true;
  }

}


  $crawler = new JsonDataCollector;

  $crawler->initialiseCrawl();
  $crawler->crawl('node');
  $crawler->crawl('vpn');
  $crawler->crawl('service');
  $crawler->endCrawl();

?>