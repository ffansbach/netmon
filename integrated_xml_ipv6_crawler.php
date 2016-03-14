<?php
	$starttime = time();
	//deny if script is not called by the server directly
	if(!empty($_SERVER['REMOTE_ADDR'])) {
		die("This script can only be run by the server directly.");
	}

	//set include paths
	if (empty($_SERVER["REQUEST_URI"])) {
		$path = dirname(__FILE__)."/";
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	}

	//get depencies
	$crawler = true;
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Routerlist.class.php');
	require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterface.class.php');
	require_once(ROOT_DIR.'/lib/core/Iplist.class.php');
	require_once(ROOT_DIR.'/lib/api/crawl.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');
	require_once(ROOT_DIR.'/lib/core/OriginatorStatus.class.php');
	require_once(ROOT_DIR.'/lib/extern/pstools.inc');

	//get offset and limit options (script parameters)
	$router_offset = getopt("o:l:")['o'];
	$router_limit = getopt("o:l:")['l'];

	// get configuration values
	$ping_count = 3; // ping a node X times before fetching data
	$ping_timeout = 1; // set the timout for each ping to X s
	$ping_hard_timeout = 3; // set the timout for each ping command to X s
	$crawl_timeout = 10; // timeout after X seconds on fetching crawldata
	$network_connection_ipv6_interface = ConfigLine::configByName("network_connection_ipv6_interface"); //use this interface to connect to ipv6 linc local hosts
	$crawl_interfaces = explode(",", ConfigLine::configByName("crawl_interfaces"));
	//array("br-mesh", "br-client", "floh_fix", "tata_fix"); //use the ip adresses of these interfaces for crawling

	$actual_crawl_cycle = Crawling::getActualCrawlCycle()['id'];

	echo "Crawling routers $router_offset-$router_limit (offset-limit) with the following options:\n";
	echo "	ping_count: $ping_count\n";
	echo "	ping_timeout: $ping_timeout\n";
	echo "	crawl_timeout: $crawl_timeout\n";
	echo "	network_connection_ipv6_interface: $network_connection_ipv6_interface\n";
	echo "	crawl_interfaces: "; foreach($crawl_interfaces as $iface) echo $iface." "; echo "\n";
	echo "	actual_crawl_cycle: ".$actual_crawl_cycle."\n";

	//fetch all routers that need to be crawled by a crawler. Respect offset and limit!
	$routerlist = new Routerlist(false, false, "crawler", false, false, false, false, false,
								 (int)$router_offset, (int)$router_limit, "router_id", "asc");
	foreach($routerlist->getRouterlist() as $key=>$router) {
		echo ($key+1).". crawling Router ".$router->getHostname()." (".$router->getRouterId().")\n";
		foreach($crawl_interfaces as $name) {
			echo "	Fetching IP-Addresses of interface ".$name."\n";
			$networkinterface = new Networkinterface(false, $router->getRouterId(), $name);
			if($networkinterface->fetch()) {
				$iplist = new Iplist($networkinterface->getNetworkinterfaceId());
				foreach($iplist->getIplist() as $ip) {
					echo "		Working with ".$ip->getIp()."\n";
					$xml_array = array();
					$ping=false;
					$return = array();

					if($ip->getNetwork()->getIpv()==6)
						$command = "ping6 -c $ping_count -w ".($ping_count+1)*$ping_timeout." -W $ping_timeout -I $network_connection_ipv6_interface ".$ip->getIp();
					elseif($ip->getNetwork()->getIpv()==4)
						$command = "ping -c $ping_count -w ".($ping_count+1)*$ping_timeout." -W $ping_timeout ".$ip->getIp();
					echo "			".$command."\n";

					PsExecute($command, $ping_hard_timeout, 1);

					//fetch crawl data from router
					$return = array();
					if($ip->getNetwork()->getIpv()==6)
						$command = "curl -s --max-time $crawl_timeout -g http://[".$ip->getIp()."%25\$(cat /sys/class/net/$network_connection_ipv6_interface/ifindex)]/node.data | zcat -f";
					elseif($ip->getNetwork()->getIpv()==4)
						$command = "curl -s --max-time $crawl_timeout -g http://".$ip->getIp()."/node.data | zcat -f";
					echo "			".$command."\n";
					exec($command, $return);
					$return_string = "";
					foreach($return as $string) {
						$return_string .= $string;
					}

					//store the crawl data into the database if the router is not offline
					if(!empty($return_string)) {
						echo "			Crawl was successful, online\n";
						try {
							$xml = new SimpleXMLElement($return_string);
							$data = simplexml2array($xml);
							$data['router_id'] = $router->getRouterId();
							$data['system_data']['status'] = "online";
						} catch (Exception $e) {
							echo nl2br($e->getMessage());
							echo "			There was an error parsing the crawled XML\n";
							$data = array();
							$data['router_id'] = $router->getRouterId();
							$data['system_data']['status'] = "unknown";
						}

						/**Insert Router System Data*/
						echo "			Inserting RouterStatus into DB\n";
						$router_status = New RouterStatus(false, (int)$actual_crawl_cycle, $router->getRouterId(),
                            $data['system_data']['status'],
                            false,
                            isset($data['system_data']['hostname']) ? $data['system_data']['hostname'] : "",
                            isset($data['client_count']) ? (int)$data['client_count'] : 0,
                            isset($data['system_data']['chipset']) ? $data['system_data']['chipset'] : "",
                            isset($data['system_data']['cpu']) ? $data['system_data']['cpu'] : "",
                            isset($data['system_data']['memory_total']) ? (int)$data['system_data']['memory_total'] : 0,
                            isset($data['system_data']['memory_caching']) ? (int)$data['system_data']['memory_caching'] : 0,
                            isset($data['system_data']['memory_buffering']) ? (int)$data['system_data']['memory_buffering'] : 0,
                            isset($data['system_data']['memory_free']) ? (int)$data['system_data']['memory_free'] : 0,
                            isset($data['system_data']['loadavg']) ? $data['system_data']['loadavg'] : "",
                            isset($data['system_data']['processes']) ? $data['system_data']['processes'] : "",
                            isset($data['system_data']['uptime']) ? $data['system_data']['uptime'] : "",
                            isset($data['system_data']['idletime']) ? $data['system_data']['idletime'] : "",
                            isset($data['system_data']['local_time']) ? $data['system_data']['local_time'] : "",
                            isset($data['system_data']['distname']) ? $data['system_data']['distname'] : "",
                            isset($data['system_data']['distversion']) ? $data['system_data']['distversion'] : "",
                            isset($data['system_data']['openwrt_core_revision']) ? $data['system_data']['openwrt_core_revision'] : "",
                            isset($data['system_data']['openwrt_feeds_packages_revision']) ? $data['system_data']['openwrt_feeds_packages_revision'] : "",
                            isset($data['system_data']['firmware_version']) ? $data['system_data']['firmware_version'] : "",
                            isset($data['system_data']['firmware_revision']) ? $data['system_data']['firmware_revision'] : "",
                            isset($data['system_data']['kernel_version']) ? $data['system_data']['kernel_version'] : "",
                            isset($data['system_data']['configurator_version']) ? $data['system_data']['configurator_version'] : "",
                            isset($data['system_data']['nodewatcher_version']) ? $data['system_data']['nodewatcher_version'] : "",
                            isset($data['system_data']['fastd_version']) ? $data['system_data']['fastd_version'] : "",
                            isset($data['system_data']['batman_advanced_version']) ? $data['system_data']['batman_advanced_version'] : "");
						if($router_status->store()) {
							echo "			Inserting Batman advanced interfaces into DB\n";
							/**Insert Batman advanced Interfaces*/
							foreach($data['batman_adv_interfaces'] as $bat_adv_int) {
								try {
									$stmt = DB::getInstance()->prepare("INSERT INTO crawl_batman_advanced_interfaces (router_id, crawl_cycle_id, name, status, crawl_date)
															 VALUES (:router_id, :actual_crawl_cycle, :name, :status, NOW());");
									$stmt->execute(array(
										':router_id' => $data['router_id'],
										':actual_crawl_cycle' => $actual_crawl_cycle,
										':name' => isset($bat_adv_int['name']) ? $bat_adv_int['name'] : "",
										':status' => isset($bat_adv_int['status']) ? $bat_adv_int['status'] : ""
									));
								} catch(PDOException $e) {
									echo $e->getMessage();
								}
							}

							echo "			Inserting Batman advanced originators into DB\n";
							/**Insert Batman Advanced Originators*/
							$originator_count=count($data['batman_adv_originators']);
							RrdTool::updateRouterBatmanAdvOriginatorsCountHistory($data['router_id'], $originator_count);

							$average_link_quality = 0;
							if(!empty($data['batman_adv_originators'])) {
								foreach($data['batman_adv_originators'] as $originator) {
									if(ConfigLine::configByName('crawl_direct_originators_only')=='true' AND
										$originator['originator'] == $originator['nexthop']) {
										$originator_status = new OriginatorStatus(false, (int)$actual_crawl_cycle, (int)$data['router_id'], $originator['originator'],
																				  (int)$originator['link_quality'], $originator['nexthop'], $originator['outgoing_interface'],
																					$originator['last_seen']);
										$originator_status->store();
										RrdTool::updateRouterBatmanAdvOriginatorLinkQuality($data['router_id'], $originator['originator'], $originator['link_quality'], time());
										$average_link_quality=$average_link_quality+$originator['link_quality'];
									}
								}
							}
							$average_link_quality=($average_link_quality/$originator_count);
							RrdTool::updateRouterBatmanAdvOriginatorLinkQuality($data['router_id'], "average", $average_link_quality, time());


							echo "			Inserting all other Data into DB\n";
							Crawl::insertCrawlData($data);
						} else {
							echo "			RouterStatus could not be inserted into DB\n";
						}
						break 2;
					} else {
						echo "			Crawl was not successful trying to ping next address\n";
					}
				}
			}
		}
	}

	echo "The process took ".(time()-$starttime)." seconds\n";

	function simplexml2array($xml) {
		if (!is_string($xml) AND !is_array($xml) AND (get_class($xml) == 'SimpleXMLElement')) {
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
				$r[$key] = simplexml2array($value);
			}
			if (isset($a)) $r['@attributes'] = $a;    // Attributes
			return $r;
		}
		return (string) $xml;
	}
?>
