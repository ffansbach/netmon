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
 * This file contains the class to crawl the IP's from the database
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */



//For Informations take a look at: http://luci.freifunk-halle.net/Documentation/JsonRpcHowTo

/**
* Configuration
*/

//The URL to your Netmon installation
$GLOBALS['netmon_url'] = "http://netmon.freifunk-ol.de/";

//Login-Data
//This must be a Netmon user with root permissions!
$GLOBALS['nickname'] = "crawler";
$GLOBALS['password'] = "password";

/**
* Crawl class
*/

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
		//get communkty informations
		$api_router_config = new jsonRPCClient($GLOBALS['netmon_url']."api_router_config.php");
		try {
			$community_info = $api_router_config->getCommunityInfo();
			$GLOBALS['net_prefix'] = $community_info['net_prefix'];
		} catch (Exception $e) {
			echo nl2br($e->getMessage());
		}

		//get services
		$api_main = new jsonRPCClient($GLOBALS['netmon_url']."api_main.php");
		try {
			$services = $api_main->getAllServiceIDsByServiceType($service_typ);
		} catch (Exception $e) {
			echo nl2br($e->getMessage());
		}

		foreach ($services as $service) {
			$ping = $this->pingHost("$GLOBALS[net_prefix].$service[ip]");

			if ($service['crawler']=="json") {
				if ($ping) {
					$json_obj = $this->getJason("http://$GLOBALS[net_prefix].$service[ip]/cgi-bin/luci/freifunk/status.json");
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
					$portcheck =  @fsockopen("$GLOBALS[net_prefix].$service[ip]", $service['crawler'], $errno, $errstr, 2);
				}
				if ($portcheck) {
					$current_crawl_data['status'] = "online";
				} else {
					$current_crawl_data['status'] = "offline";
				}
			}

			//Send data
			$api_crawl = new jsonRPCClient($GLOBALS['netmon_url']."api_crawl.php");
			try {
				$ergebnis = $api_crawl->receive($GLOBALS['nickname'], $GLOBALS['password'], $service['service_id'], $current_crawl_data);
			} catch (Exception $e) {
				echo nl2br($e->getMessage());
			}

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
		$current_crawl_data['uptime'] = $json_obj['system']['uptime'][0];
		$current_crawl_data['idletime'] = $json_obj['system']['uptime'][1];
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

/*
					COPYRIGHT

Copyright 2007 Sergio Vaccaro <sergio@inservibile.org>

This file is part of JSON-RPC PHP.

JSON-RPC PHP is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

JSON-RPC PHP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with JSON-RPC PHP; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * The object of this class are generic jsonRPC 1.0 clients
 * http://json-rpc.org/wiki/specification
 *
 * @author sergio <jsonrpcphp@inservibile.org>
 */
class jsonRPCClient {
	
	/**
	 * Debug state
	 *
	 * @var boolean
	 */
	private $debug;
	
	/**
	 * The server URL
	 *
	 * @var string
	 */
	private $url;
	/**
	 * The request id
	 *
	 * @var integer
	 */
	private $id;
	/**
	 * If true, notifications are performed instead of requests
	 *
	 * @var boolean
	 */
	private $notification = false;
	
	/**
	 * Takes the connection parameters
	 *
	 * @param string $url
	 * @param boolean $debug
	 */
	public function __construct($url,$debug = false) {
		// server URL
		$this->url = $url;
		// proxy
		empty($proxy) ? $this->proxy = '' : $this->proxy = $proxy;
		// debug state
		empty($debug) ? $this->debug = false : $this->debug = true;
		// message id
		$this->id = 1;
	}
	
	/**
	 * Sets the notification state of the object. In this state, notifications are performed, instead of requests.
	 *
	 * @param boolean $notification
	 */
	public function setRPCNotification($notification) {
		empty($notification) ?
							$this->notification = false
							:
							$this->notification = true;
	}
	
	/**
	 * Performs a jsonRCP request and gets the results as an array
	 *
	 * @param string $method
	 * @param array $params
	 * @return array
	 */
	public function __call($method,$params) {
		
		// check
		if (!is_scalar($method)) {
			throw new Exception('Method name has no scalar value');
		}
		
		// check
		if (is_array($params)) {
			// no keys
			$params = array_values($params);
		} else {
			throw new Exception('Params must be given as array');
		}
		
		// sets notification or request task
		if ($this->notification) {
			$currentId = NULL;
		} else {
			$currentId = $this->id;
		}
		
		// prepares the request
		$request = array(
						'method' => $method,
						'params' => $params,
						'id' => $currentId
						);
		$request = json_encode($request);
		$this->debug && $this->debug.='***** Request *****'."\n".$request."\n".'***** End Of request *****'."\n\n";
		
		// performs the HTTP POST
		$opts = array ('http' => array (
							'method'  => 'POST',
							'header'  => 'Content-type: application/json',
							'content' => $request
							));
		$context  = stream_context_create($opts);
		if ($fp = fopen($this->url, 'r', false, $context)) {
			$response = '';
			while($row = fgets($fp)) {
				$response.= trim($row)."\n";
			}
			$this->debug && $this->debug.='***** Server response *****'."\n".$response.'***** End of server response *****'."\n";
			$response = json_decode($response,true);
		} else {
			throw new Exception('Unable to connect to '.$this->url);
		}
		
		// debug output
		if ($this->debug) {
			echo nl2br($debug);
		}
		
		// final checks and return
		if (!$this->notification) {
			// check
			if ($response['id'] != $currentId) {
				throw new Exception('Incorrect response id (request id: '.$currentId.', response id: '.$response['id'].')');
			}
			if (!is_null($response['error'])) {
				throw new Exception('Request error: '.$response['error']);
			}
			
			return $response['result'];
			
		} else {
			return true;
		}
	}
}

?>