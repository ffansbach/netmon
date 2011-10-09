<?php

//The URL to your Netmon installation
$GLOBALS['netmon_url'] = "http://netmon.freifunk-ol.de/";

//Login-Data
//This must be a Netmon user with root permissions!
$GLOBALS['nickname'] = "crawler";
$GLOBALS['password'] = "ff26ol";

$GLOBALS['ipv6_interface'] = "batvpn";

class XmlIPv6Crawler {
public function simplexml2array($xml) {
	if (!is_string($xml) AND (get_class($xml) == 'SimpleXMLElement')) {
		$attributes = $xml->attributes();
		foreach($attributes as $k=>$v) {
			if ($v) $a[$k] = (string) $v;
		}
		$x = $xml;
		$xml = get_object_vars($xml);
	}
	if (is_array($xml)) {
		if (count($xml) == 0) return (string) $x; // for CDATA
		foreach($xml as $key=>$value) {
			$r[$key] = XmlIPv6Crawler::simplexml2array($value);
		}
		if (isset($a)) $r['@attributes'] = $a;    // Attributes
		return $r;
	}
	return (string) $xml;
}

	public function crawl() {
		//get routers to crawl
		$api_crawl = new jsonRPCClient($GLOBALS['netmon_url']."api_json_crawl.php");
		try {
			$routers = $api_crawl->getRoutersForCrawl();
		} catch (Exception $e) {
			echo nl2br($e->getMessage());
		}

		foreach($routers as $router) {
			if(!empty($router['interfaces'])) {
				foreach($router['interfaces'] as $interface) {
					if(!empty($interface['ip_addresses'])) {
						foreach($interface['ip_addresses'] as $key=>$ip_address) {
							if($ip_address['ipv']==6) {
								unset($return_string);
								unset($xml_array);
								unset($xml);

								$ipv6_address = explode("/", $ip_address['ip']);
								$return = array();
								$command = "wget_return=`busybox wget -q -O - http://[$ipv6_address[0]%$GLOBALS[ipv6_interface]]/node.data & sleep 10; kill $!`
echo \$wget_return";
echo $command;
								exec($command, $return);
								$return_string = "";
								foreach($return as $string) {
									$return_string .= $string;
								}

								if(!empty($return_string)) {
									$xml = new SimpleXMLElement($return_string);
									$xml_array = XmlIPv6Crawler::simplexml2array($xml);
								} else {
									$xml_array['system_data']['status'] = "offline";
								}
								$xml_array['router_id'] = $router['id'];
								//get routers to crawl
								$api_crawl = new jsonRPCClient($GLOBALS['netmon_url']."api_json_crawl.php");
								try {
									$result = $api_crawl->insertCrawlData($xml_array);
									print_r($result);
								} catch (Exception $e) {
									echo nl2br($e->getMessage());
								}
							}
						}
					}
				}
			}
		}
	}
}

XmlIPv6Crawler::crawl();




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
