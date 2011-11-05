<?php

	require_once('runtime.php');
	require_once('lib/classes/core/helper.class.php');
	require_once('lib/classes/core/editinghelper.class.php');
	require_once('lib/classes/core/interfaces.class.php');
	require_once('lib/classes/core/project.class.php');
	require_once('lib/classes/core/router.class.php');
	require_once('lib/classes/api/crawl.class.php');

	class IntegratedXmlIPv6Crawler {
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
					$r[$key] = IntegratedXmlIPv6Crawler::simplexml2array($value);
				}
				if (isset($a)) $r['@attributes'] = $a;    // Attributes
				return $r;
			}
			return (string) $xml;
		}
		
		public function crawl() {
			$routers = Router::getRoutersForCrawl();
			
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
									$command = "ping6 -c 4 -I $GLOBALS[netmon_ipv6_interface] $ipv6_address[0]";
									echo $command;

									exec($command, $return);
									$array_length = count($return);
									$exploded_ping_result = explode(",", $return[$array_length-2]);
									$ping=false;
									if(trim($exploded_ping_result[1]!="0 received")) {
										$ping=true;
										$exploded_ping_result = explode("=", $return[$array_length-1]);
										$exploded_ping_result = explode("/", trim($exploded_ping_result[1]));
										
										$ip_data[0]['ip_id'] = $ip_address['ip_id'];
										$ip_data[0]['ping_avg'] = $exploded_ping_result[1];
									}

									if($ping) {
										//Fetch crawl data from node
										$return = array();
										$command = "wget_return=`busybox wget -q -O - http://[$ipv6_address[0]%$GLOBALS[netmon_ipv6_interface]]/node.data & sleep 15; kill $!`
echo \$wget_return";
echo $command;
										exec($command, $return);
										$return_string = "";
										foreach($return as $string) {
											$return_string .= $string;
										}
									}

									if(!empty($return_string)) {
										$xml = new SimpleXMLElement($return_string);
										$xml_array = IntegratedXmlIPv6Crawler::simplexml2array($xml);
									} else {
										$xml_array['system_data']['status'] = "offline";
									}
									
									$xml_array['ip_data'] = $ip_data;
									
									$xml_array['router_id'] = $router['id'];
									print_r($xml_array);
									$return = Crawl::insertCrawlData($xml_array);
echo "HOSTNAME: ".$xml_array['system_data']['hostname']."\n";
echo "status: ".$xml_array['system_data']['status']."\n";
									print_r($return);
/*									//get routers to crawl
									$api_crawl = new jsonRPCClient($GLOBALS['netmon_url']."api_json_crawl.php");
									try {
										$result = $api_crawl->insertCrawlData($xml_array);
										print_r($result);
									} catch (Exception $e) {
										echo nl2br($e->getMessage());
									}*/
								}
							}
						}
					}
				}
			}
		}
	}

	IntegratedXmlIPv6Crawler::crawl();

?>
