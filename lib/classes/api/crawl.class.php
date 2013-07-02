<?php

require_once(ROOT_DIR.'/lib/classes/core/router.class.php');
require_once(ROOT_DIR.'/lib/classes/core/crawling.class.php');
require_once(ROOT_DIR.'/lib/classes/core/chipsets.class.php');
require_once(ROOT_DIR.'/lib/classes/core/rrdtool.class.php');
require_once(ROOT_DIR.'/lib/classes/core/RouterStatus.class.php');
require_once(ROOT_DIR.'/lib/classes/core/Networkinterface.class.php');
require_once(ROOT_DIR.'/lib/classes/core/NetworkinterfaceStatus.class.php');

class Crawl {
	public function getRoutersForCrawl() {
		return Router::getRoutersForCrawl();
	}

	public function insertCrawlData($data) {
	$router_data = Router::getRouterInfo($data['router_id']);
/*
	//If is owning user or if root
	if((($_POST['authentificationmethod']=='login') AND (Permission::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_POST['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_POST['router_auto_update_hash']))) {
		echo "success;".$router_data['hostname'].";";
*/
		$last_crawl_cycle = Crawling::getActualCrawlCycle();
		$router_has_been_crawled = Crawling::checkIfRouterHasBeenCrawled($data['router_id'], $last_crawl_cycle['id']);
		
		if(!$router_has_been_crawled) {
			$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
			
			/**Insert Router System Data*/
			$router_status = New RouterStatus(false, false, (int)$data['router_id'],
											  $data['system_data']['status'], false, $data['system_data']['hostname'], (int)$data['client_count'], $data['system_data']['chipset'],
											  $data['system_data']['cpu'], (int)$data['system_data']['memory_total'], (int)$data['system_data']['memory_caching'], (int)$data['system_data']['memory_buffering'],
											  (int)$data['system_data']['memory_free'], $data['system_data']['loadavg'], $data['system_data']['processes'], $data['system_data']['uptime'],
											  $data['system_data']['idletime'], $data['system_data']['local_time'], $data['system_data']['distname'], $data['system_data']['distversion'], $data['system_data']['openwrt_core_revision'], 
											  $data['system_data']['openwrt_feeds_packages_revision'], $data['system_data']['firmware_version'],
											  $data['system_data']['firmware_revision'], $data['system_data']['kernel_version'], $data['system_data']['configurator_version'], 
											  $data['system_data']['nodewatcher_version'], $data['system_data']['fastd_version'], $data['system_data']['batman_advanced_version']);
			$router_status->store();
			
			//make router history
			$router_status_tmp = new RouterStatus(false, (int)$last_endet_crawl_cycle['id'], (int)$data['router_id']);
			$router_status_tmp->fetch();
			$eventlist = $router_status->compare($router_status_tmp);
			$eventlist->store();
			
			//Update router memory rrd hostory
			RrdTool::updateRouterMemoryHistory($data['router_id'], $data['system_data']['memory_free'], $data['system_data']['memory_caching'], $data['system_data']['memory_buffering']);
			$processes = explode("/", $data['system_data']['processes']);
			RrdTool::updateRouterProcessHistory($data['router_id'], $processes[0], $processes[1]);
			
			//Check if Chipset is set right, if not create new chipset and assign to router
			if( $router_data['chipset_name'] != $data['system_data']['chipset'] AND !empty($data['system_data']['chipset'])) {
				$chipset = Chipsets::getChipsetByName($data['system_data']['chipset']);
				if(empty($chipset)) {
					$chipset = Chipsets::newChipset($router_data['user_id'], $data['system_data']['chipset']);
				}
				try{
					DB::getInstance()->exec("UPDATE routers SET
									chipset_id = '$chipset[id]'
								 WHERE id = '$data[router_id]'");
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}

			/**Insert Router Interfaces*/
			foreach($data['interface_data'] as $sendet_interface) {
				//Update RRD Graph DB
				$rrd_path_traffic_rx = ROOT_DIR."/rrdtool/databases/router_$data[router_id]_interface_$sendet_interface[name]_traffic_rx.rrd";
				if(!file_exists($rrd_path_traffic_rx)) {
					//Create new RRD-Database
					exec("rrdtool create $rrd_path_traffic_rx --step 600 --start ".time()." DS:traffic_rx:GAUGE:700:U:U DS:traffic_tx:GAUGE:900:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
				}
				
				$interface_last_endet_crawl = Interfaces::getInterfaceCrawlByCrawlCycleAndRouterIdAndInterfaceName($last_endet_crawl_cycle['id'], $data['router_id'], $sendet_interface['name']);
			
				$interface_crawl_data['traffic_info']['traffic_rx_per_second_byte'] = ($sendet_interface['traffic_rx']-$interface_last_endet_crawl['traffic_rx'])/$GLOBALS['crawl_cycle']/60;
			
				//Set negative values to 0
				if ($interface_crawl_data['traffic_info']['traffic_rx_per_second_byte']<0)
					$interface_crawl_data['traffic_info']['traffic_rx_per_second_byte']=0;
				$interface_crawl_data['traffic_info']['traffic_rx_per_second_kibibyte'] = round($interface_crawl_data['traffic_info']['traffic_rx_per_second_byte']/1024, 2);
				$interface_crawl_data['traffic_info']['traffic_rx_per_second_kilobyte'] = round($interface_crawl_data['traffic_info']['traffic_rx_per_second_byte']/1000, 2);
				$traffic_rx_per_second_byte = round($interface_crawl_data['traffic_info']['traffic_rx_per_second_byte']);
				
				$interface_crawl_data['traffic_info']['traffic_tx_per_second_byte'] = ($sendet_interface['traffic_tx']-$interface_last_endet_crawl['traffic_tx'])/$GLOBALS['crawl_cycle']/60;
				//Set negative values to 0
				if ($interface_crawl_data['traffic_info']['traffic_tx_per_second_byte']<0)
					$interface_crawl_data['traffic_info']['traffic_tx_per_second_byte']=0;
				$interface_crawl_data['traffic_info']['traffic_tx_per_second_kibibyte'] = round($interface_crawl_data['traffic_info']['traffic_tx_per_second_byte']/1024, 2);
				$interface_crawl_data['traffic_info']['traffic_tx_per_second_kilobyte'] = round($interface_crawl_data['traffic_info']['traffic_tx_per_second_byte']/1000, 2);
				$traffic_tx_per_second_byte = round($interface_crawl_data['traffic_info']['traffic_tx_per_second_byte']);
	
				//Update RRDDatabase
				$crawl_time = time();
				exec("rrdtool update $rrd_path_traffic_rx $crawl_time:".$interface_crawl_data['traffic_info']['traffic_rx_per_second_kilobyte'].":".$interface_crawl_data['traffic_info']['traffic_tx_per_second_kilobyte']);
				
				//Set default indizies to prevent from warnings
				if(!isset($sendet_interface['wlan_frequency'])) $sendet_interface['wlan_frequency']="";
								if(!isset($sendet_interface['wlan_frequency']))
					$sendet_interface['wlan_frequency']="";
				else
					$sendet_interface['wlan_frequency']=preg_replace("/([A-Za-z])/","",$sendet_interface['wlan_frequency']); //strip letters from value
				
				
				/**
				 * Interface
				 */
				//check if interface already exists
				$networkinterface_test = new Networkinterface(false, (int)$data['router_id'], $sendet_interface['name']);
				//if interface not exist, create new
				if(!$networkinterface_test->fetch()) {
					$networkinterface_new = new Networkinterface(false, (int)$data['router_id'], $sendet_interface['name']);
					$networkinterface_id = $networkinterface_new->store();
				} else {
					$networkinterface_id = $networkinterface_test->getNetworkinterfaceId();
				}
				
				//save crawl data for interface
				$networkinterface_status = new NetworkinterfaceStatus(false, false, (int)$networkinterface_id, (int)$data['router_id'],
																	  $sendet_interface['name'], $sendet_interface['mac_addr'], (int)$sendet_interface['mtu'],
																	  (int)$sendet_interface['traffic_rx'], (int)$traffic_rx_per_second_byte,
																	  (int)$sendet_interface['traffic_tx'], (int)$traffic_tx_per_second_byte,
																	  $sendet_interface['wlan_mode'], $sendet_interface['wlan_frequency'], $sendet_interface['wlan_essid'], $sendet_interface['wlan_bssid'],
																	  (int)$sendet_interface['wlan_tx_power'], false);
				$networkinterface_status->store();
				
				//TODO: Remove networkinterfaces that are not linked to any status, service, habe no ip addresses and are not marked as protected
			}

			/**Insert IP crawl data*/
			foreach($data['ip_data'] as $ip_data) {
				try {
					DB::getInstance()->exec("INSERT INTO crawl_ips (ip_id, crawl_cycle_id, crawl_date, ping_avg)
								 VALUES ('$ip_data[ip_id]', '$last_crawl_cycle[id]', NOW(), '$ip_data[ping_avg]');");
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
			}

			/**Insert Batman advanced Interfaces*/
			foreach($data['batman_adv_interfaces'] as $bat_adv_int) {
				try {
					DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_interfaces (router_id, crawl_cycle_id, name, status, crawl_date)
								 VALUES ('$data[router_id]', '$last_crawl_cycle[id]', '$bat_adv_int[name]', '$bat_adv_int[status]', NOW());");
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
			}

			/**Insert Batman Advanced Originators*/
			if(!empty($data['batman_adv_originators'])) {
				foreach($data['batman_adv_originators'] as $bat_adv_orig) {
					try {
						DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_originators (router_id, crawl_cycle_id, originator, link_quality, nexthop, outgoing_interface, last_seen, crawl_date)
									 VALUES ('$data[router_id]', '$last_crawl_cycle[id]', '$bat_adv_orig[originator]', '$bat_adv_orig[link_quality]', '$bat_adv_orig[nexthop]', '$bat_adv_orig[outgoing_interface]', '$bat_adv_orig[last_seen]', NOW());");
					}
					catch(PDOException $e) {
						echo $e->getMessage();
					}
					
					RrdTool::updateRouterBatmanAdvOriginatorLinkQuality($data['router_id'], $bat_adv_orig['originator'], $bat_adv_orig['link_quality'], time());
				}
			}
			
			$originator_count=count($data['batman_adv_originators']);
			RrdTool::updateRouterBatmanAdvOriginatorsCountHistory($data['router_id'], $originator_count);
			
			$average_link_quality = 0;
			foreach($data['batman_adv_originators'] as $originator) {
				$average_link_quality=$average_link_quality+$originator['link_quality'];
			}
			
			$average_link_quality=($average_link_quality/$originator_count);
			RrdTool::updateRouterBatmanAdvOriginatorLinkQuality($data['router_id'], "average", $average_link_quality, time());
			
			RrdTool::updateRouterClientCountHistory($data['router_id'], $data['client_count']);
		} else {
			$message[] = "Your router with the id $data[router_id] has already been crawled";
		}
		return(array("status"=>"ok", "error"=>$message));
	}
}

?>