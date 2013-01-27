<?php

/**
* IF Netmon is called by the server/cronjob
*/
if (empty($_SERVER["REQUEST_URI"])) {
	$path = dirname(__FILE__)."/";
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	$GLOBALS['netmon_root_path'] = $path."/";
}

if(!empty($_SERVER['REMOTE_ADDR'])) {
	die("This script can only be run by the server directly.");
}

require_once('runtime.php');
require_once('lib/classes/core/helper.class.php');
require_once('lib/classes/core/editinghelper.class.php');
require_once('lib/classes/core/interfaces.class.php');
require_once('lib/classes/core/project.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/api/crawl.class.php');

require_once('lib/classes/core/service.class.php');
require_once('lib/classes/core/crawling.class.php');
require_once('lib/classes/core/rrdtool.class.php');

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
	
	public function crawl($from, $to) {
		$routers = Router::getRoutersForCrawl($from, $to);
		
		foreach($routers as $router) {
			if(!empty($router['interfaces'])) {
				foreach($router['interfaces'] as $interface) {
					if(!empty($interface['ip_addresses'])) {
						foreach($interface['ip_addresses'] as $key=>$ip_address) {
							echo "Crawle $router[hostname]\n";
							echo "Nutze $ip_address[ip] auf Interface $interface[name] als ip\n";
							if($ip_address['ipv']==6) {
								unset($return_string);
								unset($xml_array);
								unset($xml);
								
								$ipv6_address = explode("/", $ip_address['ip']);
								$return = array();
								echo "Pinge...\n";
								$command = "ping6 -c 6 -I $GLOBALS[netmon_ipv6_interface] $ipv6_address[0]";
//								echo $command."\n";
								exec($command, $return);

								foreach($return as $key=>$line) {
									if(strpos($line, "packet loss")!==false) {
										$ping_result_index=$key;
										break;
									}
								}

								$exploded_ping_result = explode(",", $return[$ping_result_index]);

								$ping=false;
								if(trim($exploded_ping_result[1])!="0 received") {
									$ping=true;
									$exploded_ping_result = explode("=", $return[$ping_result_index+1]);
									$exploded_ping_result = explode("/", trim($exploded_ping_result[1]));
									
									$ip_data[0]['ip_id'] = $ip_address['ip_id'];
									$ip_data[0]['ping_avg'] = $exploded_ping_result[1];
								}


								if($ping) {
									echo "Ping war erfolgreich, avergage time: ".$ip_data[0]['ping_avg']."\n";
									echo "Hole Crawl daten...";
									//Fetch crawl data from node
									$return = array();
									$command = "curl -s --connect-timeout 4 -m8 -g http://[$ipv6_address[0]%25\$(cat /sys/class/net/$GLOBALS[netmon_ipv6_interface]/ifindex)]/node.data";
//echo $command;

									exec($command, $return);
									$return_string = "";
									foreach($return as $string) {
										$return_string .= $string;
									}

									if(!empty($return_string)) {
										echo "Crawl Daten bekommen, node wird als online markiert\n";
										try {
											$xml = new SimpleXMLElement($return_string);
											$xml_array = IntegratedXmlIPv6Crawler::simplexml2array($xml);
											$xml_array['ip_data'] = $ip_data;
											$xml_array['router_id'] = $router['id'];
											echo "Speichere Crawl daten\n";
											$return = Crawl::insertCrawlData($xml_array);
										} catch (Exception $e) {
											echo nl2br($e->getMessage());
										}
									} else {
										echo "keine Crawl Daten bekommen, node wird als offline markiert\n";
										$xml_array['system_data']['status'] = "offline";
									}
								} else {
									echo "Ping war nicht erfolgreich, crawle nicht, speichere als offline\n";
									$xml_array['system_data']['status'] = "offline";
								}
								
/*								$xml_array['ip_data'] = $ip_data;
								
								$xml_array['router_id'] = $router['id'];
//								print_r($xml_array);
								echo "Speichere Crawl daten\n";
								$return = Crawl::insertCrawlData($xml_array);*/

//echo "HOSTNAME: ".$xml_array['system_data']['hostname']."\n";
//echo "status: ".$xml_array['system_data']['status']."\n";
//								print_r($return);
/*								//get routers to crawl
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

$opts="";
$opts .= "f:";
$opts .= "t:";
$options = getopt($opts);

/*echo $options['f'];
echo $options['t'];*/

	IntegratedXmlIPv6Crawler::crawl($options['f'], $options['t']);

?>
