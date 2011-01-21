<?php

require_once('runtime.php');
require_once('lib/classes/core/login.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/routersnotassigned.class.php');
require_once('lib/classes/core/rrdtool.class.php');
require_once('lib/classes/core/interfaces.class.php');
require_once('lib/classes/core/crawling.class.php');

/**
* Crawl cycles and offline crawls
**/
Crawling::organizeCrawlCycles();

if($_GET['section']=="update") {
	header("Content-Type: text/plain");
	header("Content-Disposition: attachment; filename=nodewatcher.sh");

	echo file_get_contents('./scripts/nodewatcher/nodewatcher.sh');
}

if($_GET['section']=="version") {
	$version=11;
	echo "success;$version";
}

if($_GET['section']=="get_standart_data") {
	if ($_GET['authentificationmethod']=='hash') {
		$router_data = Router::getRouterByAutoAssignHash($_GET['router_auto_update_hash']);
	} elseif ($_GET['authentificationmethod']=='user') {
		$router_data = Router::getRouterInfo($router_id);
	}
	echo "success;".$router_data['router_id'].";".$router_data['router_auto_assign_hash'].";".$router_data['hostname'];
}

if($_GET['section']=="test_login_strings") {
	$login_strings = explode(";", $_GET['login_strings']);
	$exist=false;
	foreach($login_strings as $login_string) {
		$router_data = Router::getRouterByAutoAssignLoginString($login_string);
		if(!empty($router_data)) {
			$exist=true;
			echo "success;$login_string";
			break;
		}
	}
	if(!$exist) {
		echo "error;login_string_not_found";
	}
}


if($_GET['section']=="router_auto_assign") {
	$router_data = Router::getRouterByAutoAssignLoginString($_GET['router_auto_assign_login_string']);
	if(empty($router_data)) {
		$router = RoutersNotAssigned::getRouterByAutoAssignLoginString($_GET['router_auto_assign_login_string']);
		if (empty($router)) {
			//Make DB Insert
			try {
				DB::getInstance()->exec("INSERT INTO routers_not_assigned (create_date, update_date, hostname, router_auto_assign_login_string, interface)
							 VALUES (NOW(), NOW(), '$_GET[hostname]', '$_GET[router_auto_assign_login_string]', '$_GET[interface]');");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			echo "error;new_not_assigned;;$_GET[router_auto_assign_login_string]";
		} else {
			try {
				$result = DB::getInstance()->exec("UPDATE routers_not_assigned SET
									  update_date = NOW()
								   WHERE id = '$router[id]'");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			echo "error;updated_not_assigned;;$_GET[router_auto_assign_login_string]";
		}
	} elseif ($router_data['allow_router_auto_assign']==0) {
		echo "error;autoassign_not_allowed;$_GET[router_auto_assign_login_string]";
	} elseif(!empty($router_data['router_auto_assign_hash'])) {
		echo "error;already_assigned;$_GET[router_auto_assign_login_string]";
	} else {

		//generate random string
		$hash = md5(
                          uniqid(
                                  (string)microtime(true)
                                  +sha1(
                                        (string)rand(0,10000)  //100% Zufall
                                        +$thumb_tmp_name
                                        )
                                )
                          +md5($orig_name)
                          );

		//Save hash to DB
		$result = DB::getInstance()->exec("UPDATE routers SET
							router_auto_assign_hash = '$hash'
						WHERE id = '$router_data[router_id]'");

		//Make output
		echo "success;".$router_data['router_id'].";".$hash.";".$router_data['hostname'];
	}
}

if($_GET['section']=="insert_crawl_interfaces_data") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);
	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		echo "success;";

		$last_crawl_cycle = Crawling::getActualCrawlCycle();

		/**Insert Router Interfaces*/
		foreach($_GET['int'] as $sendet_interface) {
			//Check if interface has already been crawled in current crawl cycle
			try {
				$sql = "SELECT *
	        			FROM  crawl_interfaces
					WHERE router_id='$_GET[router_id]' AND crawl_cycle_id='$last_crawl_cycle[id]' AND name='$sendet_interface[name]'";
				$result = DB::getInstance()->query($sql);
				foreach($result as $row) {
					$crawl_interface[] = $row;
				}
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			//Make DB insert if interface has not been crawled in current crawl cycle
			if(empty($crawl_interface)) {
				unset($crawl_interface);
				//Make DB Insert
				try {
					DB::getInstance()->exec("INSERT INTO crawl_interfaces (router_id, crawl_cycle_id, crawl_date, name, mac_addr, ipv4_addr, ipv6_addr, ipv6_link_local_addr, traffic_rx, traffic_tx, wlan_mode, wlan_frequency, wlan_essid, wlan_bssid, wlan_tx_power, mtu)
								 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', NOW(), '$sendet_interface[name]', '$sendet_interface[mac_addr]', '$sendet_interface[ipv4_addr]', '$sendet_interface[ipv6_addr]', '$sendet_interface[ipv6_link_local_addr]', '$sendet_interface[traffic_rx]', '$sendet_interface[traffic_tx]', '$sendet_interface[wlan_mode]', '$sendet_interface[wlan_frequency]', '$sendet_interface[wlan_essid]', '$sendet_interface[wlan_bssid]', '$sendet_interface[wlan_tx_power]', '$sendet_interface[mtu]');");
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
				
				//Update RRD Graph DB
				$rrd_path_traffic_rx = __DIR__."/rrdtool/databases/router_$_GET[router_id]_interface_$sendet_interface[name]_traffic_rx.rrd";
				if(!file_exists($rrd_path_traffic_rx)) {
					//Create new RRD-Database
					exec("rrdtool create $rrd_path_traffic_rx --step 600 --start ".time()." DS:traffic_rx:GAUGE:700:U:U DS:traffic_tx:GAUGE:900:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
				}
			
				$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
				$interface_last_endet_crawl = Interfaces::getInterfaceCrawlByCrawlCycleAndRouterIdAndInterfaceName($last_endet_crawl_cycle['id'], $_GET['router_id'], $sendet_interface['name']);
			
				$interface_crawl_data['traffic_info']['traffic_rx_per_second_byte'] = ($sendet_interface['traffic_rx']-$interface_last_endet_crawl['traffic_rx'])/$GLOBALS['crawl_cycle']/60;
			
				//Set negative values to 0
				if ($interface_crawl_data['traffic_info']['traffic_rx_per_second_byte']<0)
					$interface_crawl_data['traffic_info']['traffic_rx_per_second_byte']=0;
				$interface_crawl_data['traffic_info']['traffic_rx_per_second_kibibyte'] = round($interface_crawl_data['traffic_info']['traffic_rx_per_second_byte']/1024, 2);
				$interface_crawl_data['traffic_info']['traffic_rx_per_second_kilobyte'] = round($interface_crawl_data['traffic_info']['traffic_rx_per_second_byte']/1000, 2);
				
				$interface_crawl_data['traffic_info']['traffic_tx_per_second_byte'] = ($sendet_interface['traffic_tx']-$interface_last_endet_crawl['traffic_tx'])/$GLOBALS['crawl_cycle']/60;
				//Set negative values to 0
				if ($interface_crawl_data['traffic_info']['traffic_tx_per_second_byte']<0)
					$interface_crawl_data['traffic_info']['traffic_tx_per_second_byte']=0;
				$interface_crawl_data['traffic_info']['traffic_tx_per_second_kibibyte'] = round($interface_crawl_data['traffic_info']['traffic_tx_per_second_byte']/1024, 2);
				$interface_crawl_data['traffic_info']['traffic_tx_per_second_kilobyte'] = round($interface_crawl_data['traffic_info']['traffic_tx_per_second_byte']/1000, 2);
	
				//Update Database
				$crawl_time = time();
				exec("rrdtool update $rrd_path_traffic_rx $crawl_time:".$interface_crawl_data['traffic_info']['traffic_rx_per_second_kilobyte'].":".$interface_crawl_data['traffic_info']['traffic_tx_per_second_kilobyte']);
			} else {
				echo "The Interface $sendet_interface[name] has already been crawled\n";
			}
		}
	} else {
		echo "error;";
		echo "You FAILED! to authenticated at netmon api nodewatcher section insert_crawl_data\n";
		echo "Your router_id is: ".$_GET['router_id'];
		echo "Your authentificationmethod is: ".$_GET['authentificationmethod'];
		echo "Your netmon router_auto_assign_hash is: ".$router_data['router_auto_assign_hash'];
		echo "Your router_auto_update_hash is: ".$_GET['router_auto_update_hash'];
	}
}
if($_GET['section']=="insert_crawl_system_data") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);
	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		echo "success;";

		/**Insert Router System Data*/
		$last_crawl_cycle = Crawling::getActualCrawlCycle();
		$crawl_router = Router::getCrawlRouterByCrawlCycleId($last_crawl_cycle['id'], $_GET['router_id']);

		if(empty($crawl_router) AND !empty($_GET['status'])) {
			Crawling::insertRouterCrawl($_GET['router_id'], $_GET);
			//Update router memory rrd hostory
			RrdTool::updateRouterMemoryHistory($_GET['router_id'], $_GET['memory_free'], $_GET['memory_caching'], $_GET['memory_buffering']);
		} else {
			echo "Router System Data has already been crawled\n";
		}
	} else {
		echo "error;";
		echo "You FAILED! to authenticated at netmon api nodewatcher section insert_crawl_data\n";
		echo "Your router_id is: ".$_GET['router_id'];
		echo "Your authentificationmethod is: ".$_GET['authentificationmethod'];
		echo "Your netmon router_auto_assign_hash is: ".$router_data['router_auto_assign_hash'];
		echo "Your router_auto_update_hash is: ".$_GET['router_auto_update_hash'];
	}
}

if($_GET['section']=="insert_batman_adv_interfaces") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);
	$router_data = Router::getRouterInfo($_GET['router_id']);
	$last_crawl_cycle = Crawling::getActualCrawlCycle();

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		echo "success;";
		/**Insert Batman advanced Interfaces*/
		foreach($_GET['bat_adv_int'] as $bat_adv_int) {
			try {
				$sql = "SELECT *
	        			FROM  crawl_batman_advanced_interfaces
					WHERE router_id='$_GET[router_id]' AND crawl_cycle_id='$last_crawl_cycle[id]' AND name='$bat_adv_int[name]'";
				$result = DB::getInstance()->query($sql);
				foreach($result as $row) {
					$crawl_batman_adv_interface[] = $row;
				}
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			if(empty($crawl_batman_adv_interface)) {
				unset($crawl_batman_adv_interface);
				try {
					DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_interfaces (router_id, crawl_cycle_id, name, status, crawl_date)
								 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', '$bat_adv_int[name]', '$bat_adv_int[status]', NOW());");
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
 			} else {
				echo "The Batman Advanced Interface $bat_adv_int[name] has already been crawled\n";
			}
		}
	} else {
		echo "error;";
		echo "You FAILED! to authenticated at netmon api nodewatcher section insert_crawl_data\n";
		echo "Your router_id is: ".$_GET['router_id'];
		echo "Your authentificationmethod is: ".$_GET['authentificationmethod'];
		echo "Your netmon router_auto_assign_hash is: ".$router_data['router_auto_assign_hash'];
		echo "Your router_auto_update_hash is: ".$_GET['router_auto_update_hash'];
	}
}

if($_GET['section']=="insert_batman_adv_originators") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);
	$router_data = Router::getRouterInfo($_GET['router_id']);
	$last_crawl_cycle = Crawling::getActualCrawlCycle();

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		echo "success;";
		/**Insert Batman Advanced Originators*/
		foreach($_GET['bat_adv_orig'] as $bat_adv_orig) {
			try {
				$sql = "SELECT *
        				FROM  crawl_batman_advanced_originators
					WHERE router_id='$_GET[router_id]' AND crawl_cycle_id='$last_crawl_cycle[id]' AND originator='$bat_adv_orig[originator]'";
				$result = DB::getInstance()->query($sql);
				foreach($result as $row) {
					$crawl_originators[] = $row;
				}
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}

			if(empty($crawl_originators)) {
				try {
					DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_originators (router_id, crawl_cycle_id, originator, link_quality, last_seen, crawl_date)
								 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', '$bat_adv_orig[originator]', '$bat_adv_orig[link_quality]', '$bat_adv_orig[last_seen]', NOW());");
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}

				RrdTool::updateRouterBatmanAdvOriginatorLinkQuality($_GET['router_id'], $bat_adv_orig['originator'], $bat_adv_orig['link_quality'], time());
			} else {
				echo "The Batman Advanced Originator $bat_adv_orig[originator] has already been crawled\n";
			}
		}

		$originator_count=count($_GET['bat_adv_orig']);
		RrdTool::updateRouterBatmanAdvOriginatorsCountHistory($_GET['router_id'], $originator_count);

		$average_link_quality = 0;
		foreach($_GET['bat_adv_orig'] as $originator) {
			$average_link_quality=$average_link_quality+$originator['link_quality'];
		}

		$average_link_quality=($average_link_quality/$originator_count);
		RrdTool::updateRouterBatmanAdvOriginatorLinkQuality($_GET['router_id'], "average", $average_link_quality, time());

	} else {
		echo "error;";
		echo "You FAILED! to authenticated at netmon api nodewatcher section insert_crawl_data\n";
		echo "Your router_id is: ".$_GET['router_id'];
		echo "Your authentificationmethod is: ".$_GET['authentificationmethod'];
		echo "Your netmon router_auto_assign_hash is: ".$router_data['router_auto_assign_hash'];
		echo "Your router_auto_update_hash is: ".$_GET['router_auto_update_hash'];
	}
}

if($_GET['section']=="insert_clients") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);
	$router_data = Router::getRouterInfo($_GET['router_id']);
	$last_crawl_cycle = Crawling::getActualCrawlCycle();

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		echo "success;";
		/**Insert Client Data*/
		foreach($_GET['clients'] as $client) {
			unset($crawl_clients);
			try {
				$sql = "SELECT *
        				FROM  crawl_clients
					WHERE router_id='$_GET[router_id]' AND crawl_cycle_id='$last_crawl_cycle[id]' AND mac_addr='$client[mac_addr]'";
				$result = DB::getInstance()->query($sql);
				foreach($result as $row) {
					$crawl_clients[] = $row;
				}
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}

			if(empty($crawl_clients)) {
				try {
					DB::getInstance()->exec("INSERT INTO crawl_clients (router_id, crawl_cycle_id, crawl_date, mac_addr)
								 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', NOW(), '$client[mac_addr]')");
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
			} else {
				echo "The Client $client[mac_addr] has already been crawled\n";
			}
		}
	} else {
		echo "error;";
		echo "You FAILED! to authenticated at netmon api nodewatcher section insert_crawl_data\n";
		echo "Your router_id is: ".$_GET['router_id'];
		echo "Your authentificationmethod is: ".$_GET['authentificationmethod'];
		echo "Your netmon router_auto_assign_hash is: ".$router_data['router_auto_assign_hash'];
		echo "Your router_auto_update_hash is: ".$_GET['router_auto_update_hash'];
	}
}

if($_GET['section']=="get_hostnames_and_mac") {
	$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();

	try {
		$sql = "SELECT crawl_interfaces.mac_addr, routers.hostname
       			FROM  crawl_interfaces, routers
			WHERE crawl_cycle_id='$last_endet_crawl_cycle[id]' AND routers.id=crawl_interfaces.router_id";
		$result = DB::getInstance()->query($sql);
		foreach($result as $row) {
			echo $row['mac_addr']." ".$row['hostname']."\n";
		}
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
}

?>