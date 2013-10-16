<?php

require_once('runtime.php');
require_once(ROOT_DIR.'/lib/core/login.class.php');
require_once(ROOT_DIR.'/lib/core/router_old.class.php');
require_once(ROOT_DIR.'/lib/core/routersnotassigned.class.php');
require_once(ROOT_DIR.'/lib/core/rrdtool.class.php');
require_once(ROOT_DIR.'/lib/core/interfaces.class.php');
require_once(ROOT_DIR.'/lib/core/crawling.class.php');
require_once(ROOT_DIR.'/lib/extern/phpass/PasswordHash.php');
require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');
require_once(ROOT_DIR.'/lib/core/Networkinterface.class.php');
require_once(ROOT_DIR.'/lib/core/NetworkinterfaceStatus.class.php');

if($_GET['section']=="get_standart_data") {
	if ($_GET['authentificationmethod']=='hash') {
		$router_data = Router_old::getRouterByAutoAssignHash($_GET['router_auto_update_hash']);
	}
	
	if(!empty($router_data)) {
		echo "success;".$router_data['router_id'].";".$router_data['router_auto_assign_hash'].";".$router_data['hostname'];
	} else {
		echo "error;router_not_found";
	}
}

if($_GET['section']=="test_login_strings") {
	$login_strings = explode(";", $_GET['login_strings']);
	$exist=false;
	foreach($login_strings as $login_string) {
		if(!empty($login_string)) {
			$router_data = Router_old::getRouterByAutoAssignLoginString($login_string);
			if(!empty($router_data)) {
				$exist=true;
				echo "success;$login_string";
				break;
			}
		}
	}
	if(!$exist) {
		echo "error;login_string_not_found";
	}
}

if($_GET['section']=="router_auto_assign") {
	$router_data = Router_old::getRouterByAutoAssignLoginString($_GET['router_auto_assign_login_string']);
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
				echo $e->getTraceAsString();
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
				echo $e->getTraceAsString();
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

if($_GET['section']=="get_hostnames_and_mac") {
	$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
//echo "<pre>";

	$interfaces = array();
	try {
		$sql = "SELECT DISTINCT crawl_batman_advanced_interfaces.name, routers.hostname, crawl_interfaces.mac_addr
				FROM crawl_batman_advanced_interfaces, routers, crawl_interfaces
				WHERE crawl_batman_advanced_interfaces.crawl_cycle_id='$last_endet_crawl_cycle[id]' AND 
				      routers.id=crawl_batman_advanced_interfaces.router_id AND 
				      crawl_interfaces.crawl_cycle_id='$last_endet_crawl_cycle[id]' AND 
				      crawl_interfaces.router_id=crawl_batman_advanced_interfaces.router_id AND
				      crawl_interfaces.name LIKE crawl_batman_advanced_interfaces.name
				GROUP BY crawl_interfaces.mac_addr";
		$result = DB::getInstance()->query($sql);
		foreach($result as $row) {
			echo $row['mac_addr']." ".$row['hostname']."_".$row['name']."\n";
		}
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
}

/** Nodewatcher Version >18 */

if($_GET['section']=="insert_crawl_data") {
	$router_data = Router_old::getRouterInfo($_POST['router_id']);
	
	//If is owning user or if root
	if((($_POST['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_POST['router_auto_update_hash']))) {
		echo "success;".$router_data['hostname'].";";
		
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		$router_has_been_crawled = Crawling::checkIfRouterHasBeenCrawled($_POST['router_id'], $actual_crawl_cycle['id']);

		if(!$router_has_been_crawled) {
			$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
			
			/**Insert Router System Data*/
			$router_status = New RouterStatus(false, (int)$actual_crawl_cycle['id'], (int)$_POST['router_id'],
											  $_POST['status'], false, $_POST['hostname'], (int)$_POST['client_count'], $_POST['chipset'],
											  $_POST['cpu'], (int)$_POST['memory_total'], (int)$_POST['memory_caching'], (int)$_POST['memory_buffering'],
											  (int)$_POST['memory_free'], $_POST['loadavg'], $_POST['processes'], $_POST['uptime'],
											  $_POST['idletime'], $_POST['local_time'], $_POST['distname'], $_POST['distversion'], $_POST['openwrt_core_revision'], 
											  $_POST['openwrt_feeds_packages_revision'], $_POST['firmware_version'],
											  $_POST['firmware_revision'], $_POST['kernel_version'], $_POST['configurator_version'], 
											  $_POST['nodewatcher_version'], $_POST['fastd_version'], $_POST['batman_advanced_version']);
			$router_status->store();
			
			/**Insert Router Interfaces*/
			foreach($_POST['int'] as $sendet_interface) {
				/**
				 * Interface
				 */
				//check if interface already exists
				$networkinterface_test = new Networkinterface(false, (int)$_POST['router_id'], $sendet_interface['name']);
				//if interface not exist, create new
				if(!$networkinterface_test->fetch()) {
					$networkinterface_new = new Networkinterface(false, (int)$_POST['router_id'], $sendet_interface['name']);
					$networkinterface_id = $networkinterface_new->store();
				} else {
					$networkinterface_id = $networkinterface_test->getNetworkinterfaceId();
				}
				
				//save crawl data for interface
				$networkinterface_status = new NetworkinterfaceStatus(false, false, (int)$networkinterface_id, (int)$_POST[router_id],
																	  $sendet_interface['name'], $sendet_interface['mac_addr'], (int)$sendet_interface['mtu'],
																	  (int)$sendet_interface['traffic_rx'], (int)$traffic_rx_per_second_byte,
																	  (int)$sendet_interface['traffic_tx'], (int)$traffic_tx_per_second_byte,
																	  $sendet_interface['wlan_mode'], $sendet_interface['wlan_frequency'], $sendet_interface['wlan_essid'], $sendet_interface['wlan_bssid'],
																	  (int)$sendet_interface['wlan_tx_power'], false);
				$networkinterface_status->store();
				//TODO: Remove networkinterfaces that are not linked to any status, service, habe no ip addresses and are not marked as protected
				
				//Update RRD Graph DB
				$rrd_path_traffic_rx = __DIR__."/rrdtool/databases/router_$_POST[router_id]_interface_$sendet_interface[name]_traffic_rx.rrd";
				if(!file_exists($rrd_path_traffic_rx)) {
					//Create new RRD-Database
					exec("rrdtool create $rrd_path_traffic_rx --step 600 --start ".time()." DS:traffic_rx:GAUGE:700:U:U DS:traffic_tx:GAUGE:900:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
				}
			
				$interface_last_endet_crawl = Interfaces::getInterfaceCrawlByCrawlCycleAndRouterIdAndInterfaceName($last_endet_crawl_cycle['id'], $_POST['router_id'], $sendet_interface['name']);
			
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
			}

			/**Insert Batman advanced Interfaces*/
			foreach($_POST['bat_adv_int'] as $bat_adv_int) {
				try {
					DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_interfaces (router_id, crawl_cycle_id, name, status, crawl_date)
								 VALUES ('$_POST[router_id]', '$actual_crawl_cycle[id]', '$bat_adv_int[name]', '$bat_adv_int[status]', NOW());");
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
			}

			/**Insert Batman Advanced Originators*/
			if(!empty($_POST['bat_adv_orig'])) {
				foreach($_POST['bat_adv_orig'] as $bat_adv_orig) {
					try {
						DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_originators (router_id, crawl_cycle_id, originator, link_quality, nexthop, outgoing_interface, last_seen, crawl_date)
									 VALUES ('$_POST[router_id]', '$actual_crawl_cycle[id]', '$bat_adv_orig[originator]', '$bat_adv_orig[link_quality]', '$bat_adv_orig[nexthop]', '$bat_adv_orig[outgoing_interface]', '$bat_adv_orig[last_seen]', NOW());");
					}
					catch(PDOException $e) {
						echo $e->getMessage();
						echo $e->getTraceAsString();
					}
					
					RrdTool::updateRouterBatmanAdvOriginatorLinkQuality($_POST['router_id'], $bat_adv_orig['originator'], $bat_adv_orig['link_quality'], time());
				}
			}
			
			$originator_count=count($_POST['bat_adv_orig']);
			RrdTool::updateRouterBatmanAdvOriginatorsCountHistory($_POST['router_id'], $originator_count);
			
			$average_link_quality = 0;
			foreach($_POST['bat_adv_orig'] as $originator) {
				$average_link_quality=$average_link_quality+$originator['link_quality'];
			}
			
			$average_link_quality=($average_link_quality/$originator_count);
			RrdTool::updateRouterBatmanAdvOriginatorLinkQuality($_POST['router_id'], "average", $average_link_quality, time());
			
			RrdTool::updateRouterClientCountHistory($_POST['router_id'], $_POST['client_count']);
		} else {
			echo "Your router with the id $_POST[router_id] has already been crawled";
		}
	} else {
		echo "error;";
		echo "You FAILED! to authenticated at netmon api nodewatcher section insert_crawl_interfaces_data\n";
		echo "Your router_id is: ".$_POST['router_id'];
		echo "Your authentificationmethod is: ".$_POST['authentificationmethod'];
		echo "Your netmon router_auto_assign_hash is: ".$router_data['router_auto_assign_hash'];
		echo "Your router_auto_update_hash is: ".$_POST['router_auto_update_hash'];
	}
}

?>
