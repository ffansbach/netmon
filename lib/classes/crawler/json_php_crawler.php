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

  //Login-Data
  $GLOBALS['nickname'] = "crawler";
  $GLOBALS['password'] = "test";
  $GLOBALS['netmon_url'] = "http://netmon.freifunk-ol.de/";

class JsonDataCollector {
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

	public function crawl($service_typ) {
		//Hole Services
		$curl_handle=curl_init();
		curl_setopt($curl_handle,CURLOPT_URL, $GLOBALS['netmon_url']."api.php?class=main&section=get_services_by_type&type=$service_typ");
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,3);
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		$services = unserialize(curl_exec($curl_handle));
		curl_close($curl_handle);

		foreach ($services as $service) {
			echo "Service: $service[service_id]\n";
			$ping = $this->pingHost("$GLOBALS[net_prefix].$service[subnet_ip].$service[ip_ip]");

			if ($service['crawler']=="json") {
				if ($ping) {
					$json_obj = $this->getJason("http://$GLOBALS[net_prefix].$service[subnet_ip].$service[ip_ip]/cgi-bin/luci/freifunk/status.json");
				}

				if ($json_obj) {
					$current_crawl_data = $this->makeCurrentJsonCrawlDataArray($json_obj);
					$current_crawl_data['status'] = "online";
				} else {
					$current_crawl_data['status'] = "offline";
				}

				$current_crawl_data['ping'] = $ping;
			} elseif ($service['crawler']=="ping") {
				if ($ping) {
					$current_crawl_data['status'] = "online";
				} else {
					$current_crawl_data['status'] = "offline";
				}
			} elseif ($service['typ']=="service" AND is_numeric($service['crawler'])) {
				if ($ping) {
					$portcheck =  @fsockopen("$GLOBALS[net_prefix].$service[subnet_ip].$service[ip_ip]", $service['crawler'], $errno, $errstr, 2);
				}
				if ($portcheck) {
					$current_crawl_data['status'] = "online";
				} else {
					$current_crawl_data['status'] = "offline";
				}
			}
			//Send Data
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $GLOBALS['netmon_url']."api.php?class=crawl&section=receive");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "nickname=crawler&password=test&service_id=$service[service_id]&crawl_data=".serialize($current_crawl_data));
			curl_exec ($ch);
			curl_close ($ch);

			unset($ping);
			unset($json_obj);
			unset($portcheck);
			unset($current_crawl_data);
		}
	}

	function object2array($object) {
		if (is_object($object) || is_array($object)) {
			foreach ($object as $key => $value) {
				$array[$key] = JsonDataCollector::object2array($value);
			}
		}else {
			$array = $object;
		}
		return $array;
	}

	public function makeCurrentJsonCrawlDataArray($json_obj) {
		$json_obj = JsonDataCollector::object2array($json_obj);
		$current_crawl_data['nickname'] = $json_obj['freifunk']['contact']['nickname'];
		$current_crawl_data['hostname'] = $json_obj['system']['hostname'];
		$current_crawl_data['email'] = $json_obj['freifunk']['contact']['mail'];
		$current_crawl_data['location'] = $json_obj['freifunk']['contact']['location'];
		$current_crawl_data['prefix'] = $json_obj['freifunk']['community']['prefix'];
		$current_crawl_data['ssid'] = $json_obj['freifunk']['community']['ssid'];
		$current_crawl_data['longitude'] = $json_obj['geo']['longitude'];
		$current_crawl_data['latitude'] = $json_obj['geo']['latitude'];
		$current_crawl_data['luciname'] = $json_obj['firmware']['luciname'];
		$current_crawl_data['luciversion'] = $json_obj['firmware']['luciversion'];
		$current_crawl_data['distname'] = $json_obj['firmware']['distname'];
		$current_crawl_data['distversion'] = $json_obj['firmware']['distversion'];
		$current_crawl_data['chipset'] = $json_obj['system']['sysinfo'][0];
		$current_crawl_data['cpu'] = $json_obj['system']['sysinfo'][1];
		$current_crawl_data['network'] = serialize($json_obj['network']);
		$current_crawl_data['wireless_interfaces'] = serialize($json_obj['wireless']['interfaces']);
		$current_crawl_data['uptime'] = round(($json_obj['system']['uptime'][0])/60/60, 2);
		$current_crawl_data['idletime'] = round(($json_obj['system']['uptime'][1])/60/60, 2);
		$current_crawl_data['memory_total'] = $json_obj['system']['sysinfo'][2];
		$current_crawl_data['memory_caching'] = $json_obj['system']['sysinfo'][3];
		$current_crawl_data['memory_buffering'] = $json_obj['system']['sysinfo'][4];
		$current_crawl_data['memory_free'] = $json_obj['system']['sysinfo'][5];
		$current_crawl_data['loadavg'] = $json_obj['system']['loadavg'][0];
		$current_crawl_data['processes'] = $json_obj['system']['loadavg'][3];
		$current_crawl_data['olsrd_hna'] = serialize($json_obj['olsrd']['HNA']);
		$current_crawl_data['olsrd_neighbors'] = serialize($json_obj['olsrd']['Neighbors']);
		$current_crawl_data['olsrd_links'] = serialize($json_obj['olsrd']['Links']);
		$current_crawl_data['olsrd_mid'] = serialize($json_obj['olsrd']['MID']);
		$current_crawl_data['olsrd_routes'] = serialize($json_obj['olsrd']['Routes']);
		$current_crawl_data['olsrd_topology'] = serialize($json_obj['olsrd']['Topology']);

		return $current_crawl_data;
	}
}


  $crawler = new JsonDataCollector;

  $crawler->crawl('node');
  $crawler->crawl('vpn');
  $crawler->crawl('service');
  $crawler->crawl('client');

?>