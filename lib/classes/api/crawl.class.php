<?php

require_once($GLOBALS['monitor_root'].'lib/classes/core/router.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/crawling.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/chipsets.class.php');

class Crawl {
	public function getRoutersForCrawl() {
		return Router::getRoutersForCrawl();
	}

	public function insertCrawlData($data) {
//	$session = login::user_login($_POST['nickname'], $_POST['password']);
	$router_data = Router::getRouterInfo($data['router_id']);
/*
	//If is owning user or if root
	if((($_POST['authentificationmethod']=='login') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_POST['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_POST['router_auto_update_hash']))) {
		echo "success;".$router_data['hostname'].";";
*/

		$last_crawl_cycle = Crawling::getActualCrawlCycle();
		$router_has_been_crawled = Crawling::checkIfRouterHasBeenCrawled($data['router_id'], $last_crawl_cycle['id']);

		if(!$router_has_been_crawled) {
			/**Insert Router System Data*/
			Crawling::insertRouterCrawl($data['router_id'], $data['system_data']);
			//Update router memory rrd hostory
			RrdTool::updateRouterMemoryHistory($data['router_id'], $data['system_data']['memory_free'], $data['system_data']['memory_caching'], $data['system_data']['memory_buffering']);
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
				//Make DB Insert
				try {
					DB::getInstance()->exec("INSERT INTO crawl_interfaces (router_id, crawl_cycle_id, crawl_date, name, mac_addr, ipv4_addr, ipv6_addr, ipv6_link_local_addr, traffic_rx, traffic_tx, wlan_mode, wlan_frequency, wlan_essid, wlan_bssid, wlan_tx_power, mtu)
								 VALUES ('$data[router_id]', '$last_crawl_cycle[id]', NOW(), '$sendet_interface[name]', '$sendet_interface[mac_addr]', '$sendet_interface[ipv4_addr]', '$sendet_interface[ipv6_addr]', '$sendet_interface[ipv6_link_local_addr]', '$sendet_interface[traffic_rx]', '$sendet_interface[traffic_tx]', '$sendet_interface[wlan_mode]', '$sendet_interface[wlan_frequency]', '$sendet_interface[wlan_essid]', '$sendet_interface[wlan_bssid]', '$sendet_interface[wlan_tx_power]', '$sendet_interface[mtu]');");
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
				
				//Update RRD Graph DB
				$rrd_path_traffic_rx = "$GLOBALS[monitor_root]/rrdtool/databases/router_$data[router_id]_interface_$sendet_interface[name]_traffic_rx.rrd";
				if(!file_exists($rrd_path_traffic_rx)) {
					//Create new RRD-Database
					exec("rrdtool create $rrd_path_traffic_rx --step 600 --start ".time()." DS:traffic_rx:GAUGE:700:U:U DS:traffic_tx:GAUGE:900:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
				}
			
				$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
				$interface_last_endet_crawl = Interfaces::getInterfaceCrawlByCrawlCycleAndRouterIdAndInterfaceName($last_endet_crawl_cycle['id'], $data['router_id'], $sendet_interface['name']);
			
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
						DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_originators (router_id, crawl_cycle_id, originator, link_quality, last_seen, crawl_date)
									 VALUES ('$data[router_id]', '$last_crawl_cycle[id]', '$bat_adv_orig[originator]', '$bat_adv_orig[link_quality]', '$bat_adv_orig[last_seen]', NOW());");
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
			
			/**Client Data */
			try {
				DB::getInstance()->exec("INSERT INTO crawl_clients_count (router_id, crawl_cycle_id, crawl_date, client_count)
							 VALUES ('$data[router_id]', '$last_crawl_cycle[id]', NOW(), '$data[client_count]')");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			RrdTool::updateRouterClientCountHistory($data['router_id'], $data['client_count']);
		} else {
			$message[] = "Your router with the id $data[router_id] has already been crawled";
		}
		return(array("status"=>"ok", "error"=>$message));
	}
/*
	public function config() {
		return array('crawler_ping_timeout'=>$GLOBALS['crawler_ping_timeout'], 'crawler_curl_timeout'=>$GLOBALS['crawler_curl_timeout']);
	}

	public function receive($nickname, $password, $service_id, $current_crawl_data) {
		$session = login::user_login($nickname, $password);
		
		$service_data = Helper::getServiceDataByServiceId($service_id);

		//If is owning user or if root
		if(UserManagement::isThisUserOwner($service_data['user_id'], $session['user_id']) OR $session['permission']==120) {
			$last_crawl_data = Helper::getLastCrawlDataByServiceId($service_id);
			if($last_crawl_data['status'] == 'online') {
				$last_online_crawl_data = $last_crawl_data;
			} elseif($last_crawl_data['status'] == 'offline') {
				$last_online_crawl_data = Helper::getLastOnlineCrawlDataByServiceId($service_id);
			}

			$crawl_id = service::insertStatus($current_crawl_data, $service_id);
			Olsr::insertOlsrData($crawl_id, $service_id, $current_crawl_data);
			service::clearCrawlDatabase($service_id);

			if($service_data['crawler']=='json') {
				Ip::insertStatus($current_crawl_data, $service_data['ip_id']);
			}

		try {
			service::makeHistoryEntry($current_crawl_data, $last_crawl_data, $last_online_crawl_data, $service_id);
			service::clearHistory($service_id);
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}

			service::offlineNotification($service_id);
			service::updateNotificationStatus($current_crawl_data['status'], $service_id);
			return true;
		} else {
			return false;
		}
	}*/
}

?>