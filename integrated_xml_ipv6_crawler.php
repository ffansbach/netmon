<?php
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
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Routerlist.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterface.class.php');
	require_once(ROOT_DIR.'/lib/core/Iplist.class.php');
	require_once(ROOT_DIR.'/lib/core/interfaces.class.php');
	require_once(ROOT_DIR.'/lib/api/crawl.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');
	
	//get offset and limit options (script parameters)
	$router_offset = getopt("o:l:")['o'];
	$router_limit = getopt("o:l:")['l'];
	
	// get configuration values
	$ping_count = 4; // ping a node X times before fetching data
	$ping_timeout = 1000; // set the timout for each ping to X ms
	$crawl_timeout = 8; // timeout after X seconds on fetching crawldata
	$network_connection_ipv6_interface = ConfigLine::configByName("network_connection_ipv6_interface"); //use this interface to connect to ipv6 linc local hosts
	$interfaces_used_for_crawling = array("br-mesh"); //use the ip adresses of these interfaces for crawling
	
	echo "Crawling routers $router_offset-$router_limit (offset-limit) with the following options:\n";
	echo "	ping_count: $ping_count\n";
	echo "	ping_timeout: $ping_timeout\n";
	echo "	crawl_timeout: $crawl_timeout\n";
	echo "	network_connection_ipv6_interface: $network_connection_ipv6_interface\n";
	echo "	interfaces_used_for_crawling: "; foreach($interfaces_used_for_crawling as $iface) echo $iface; echo "\n";
	
	//fetch all routers that need to be crawled by a crawler. Respect offset and limit!
	$routerlist = new Routerlist(false, false, "crawler", false, false, false, false, false,
								 (int)$router_offset, (int)$router_limit, "router_id", "asc");
	foreach($routerlist->getRouterlist() as $key=>$router) {
		echo ($key+1).". crawling Router ".$router->getHostname()." (".$router->getRouterId().")\n";
		foreach($interfaces_used_for_crawling as $name) {
			echo "	Fetching IP-Addresses of interface ".$name."\n";
			$networkinterface = new Networkinterface(false, $router->getRouterId(), $name);
			if($networkinterface->fetch()) {
				$iplist = new Iplist($networkinterface->getNetworkinterfaceId());
				foreach($iplist->getIplist() as $ip) {
					echo "		Working with ".$ip->getIp()."\n";
					
					$ping=false;
					$return = array();
					if($ip->getNetwork()->getIpv()==6)
						$command = "ping6 -c $ping_count -w $ping_timeout -I $network_connection_ipv6_interface ".$ip->getIp();
					elseif($ip->getNetwork()->getIpv()==4)
						$command = "ping -c $ping_count -w $ping_timeout ".$ip->getIp();
					echo "			".$command."\n";
					exec($command, $return);
					foreach($return as $key=>$line) {
						if(strpos($line, "packet loss")!==false) {
							$ping_result_index=$key;
							break;
						}
					}
					if(trim(explode(",", $return[$ping_result_index])[1])!="0 received") {
						echo "			Ping was successfull trying to crawl\n";
						
						//fetch crawl data from router
						$return = array();
						if($ip->getNetwork()->getIpv()==6)
							$command = "curl -s --max-time $crawl_timeout -g http://[".$ip->getIp()."%25\$(cat /sys/class/net/$network_connection_ipv6_interface/ifindex)]/node.data";
						elseif($ip->getNetwork()->getIpv()==4)
							$command = "curl -s --max-time $crawl_timeout -g http://".$ip->getIp()."/node.data";
						echo "			".$command."\n";
						exec($command, $return);
						$return_string = "";
						foreach($return as $string) {
							$return_string .= $string;
						}
						
						//store the crawl data into the database if the router is not offline
						if(!empty($return_string)) {
							echo "			Craw was successfull, node gets marked as online\n";
							try {
								$xml = new SimpleXMLElement($return_string);
								$xml_array = simplexml2array($xml);
								$xml_array['router_id'] = $router->getRouterId();
								$xml_array['system_data']['status'] = "online";
								$return = Crawl::insertCrawlData($xml_array);
								break;
							} catch (Exception $e) {
								echo nl2br($e->getMessage());
							}
							break 2;
						} else {
							echo "			Craw was not successfull, node gets marked as unknown because ping was possible. Mysterious!\n";
							$xml_array['router_id'] = $router->getRouterId();
							$xml_array['system_data']['status'] = "unknown";
							$return = Crawl::insertCrawlData($xml_array);
							break 2;
						}
					} else {
						echo "			Ping was not successfull trying to ping next address\n";
					}
				}
			}
		}
	}

	function simplexml2array($xml) {
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
				$r[$key] = simplexml2array($value);
			}
			if (isset($a)) $r['@attributes'] = $a;    // Attributes
			return $r;
		}
		return (string) $xml;
	}
?>