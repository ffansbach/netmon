<?php
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/login.class.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/crawling.class.php');
require_once('./lib/classes/core/interfaces.class.php');
require_once('./lib/classes/core/rrdtool.class.php');

if($_GET['section']=="insert_interface") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);

	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		$last_crawl_cycle = Crawling::getActualCrawlCycle();

		foreach($_GET['interfaces'] as $sendet_interface) {
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
				//Make DB Insert
				try {
					DB::getInstance()->exec("INSERT INTO crawl_interfaces (router_id, crawl_cycle_id, crawl_date, name, mac_addr, ipv4_addr, ipv6_addr, ipv6_link_local_addr, traffic_rx, traffic_tx, wlan_mode, wlan_frequency, wlan_essid, wlan_bssid, wlan_tx_power)
								 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', NOW(), '$sendet_interface[name]', '$sendet_interface[mac_addr]', '$sendet_interface[ipv4_addr]', '$sendet_interface[ipv6_addr]', '$sendet_interface[ipv6_link_local_addr]', '$sendet_interface[traffic_rx]', '$sendet_interface[traffic_tx]', '$sendet_interface[wlan_mode]', '$sendet_interface[wlan_frequency]', '$sendet_interface[wlan_essid]', '$sendet_interface[wlan_bssid]', '$sendet_interface[wlan_tx_power]');");
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
			}
		}
	}
}

if($_GET['section']=="insert_batman_advanced_interface") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);

	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		try {
			$sql = "SELECT *
			        FROM  crawl_cycle
	        		ORDER BY crawl_date desc
				LIMIT 1;";
			$result = DB::getInstance()->query($sql);
			$last_crawl_cycle = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
	
		try {
			$sql = "SELECT *
	        		FROM  crawl_batman_advanced_interfaces
				WHERE router_id='$_GET[router_id]' AND crawl_cycle_id='$last_crawl_cycle[id]' AND name='$_GET[name]'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$crawl_interface[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		if(empty($crawl_interface)) {
			try {
				DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_interfaces (router_id, crawl_cycle_id, name, status, crawl_date)
							 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', '$_GET[name]', '$_GET[status]', NOW());");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
 		}
	}
}

if($_GET['section']=="insert_batman_advanced_originators") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);

	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		try {
			$sql = "SELECT *
			        FROM  crawl_cycle
	        		ORDER BY crawl_date desc
				LIMIT 1;";
			$result = DB::getInstance()->query($sql);
			$last_crawl_cycle = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
	
		try {
			$sql = "SELECT *
	        		FROM  crawl_batman_advanced_originators
				WHERE router_id='$_GET[router_id]' AND crawl_cycle_id='$last_crawl_cycle[id]'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$crawl_originators[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		if(empty($crawl_originators)) {
			$originators = explode(",", $_GET['originators']);
			foreach($originators as $key=>$originator) {
				$originator = explode(";" , $originator);
				$originators[$key] = array('originator'=>$originator[0],
							   'link_quality'=>$originator[1]);
			}
			$originators_count = count($originators);
			$originators = serialize($originators);
			
			try {
				DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_originators (router_id, crawl_cycle_id, originators, crawl_date)
							 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', '$originators', NOW());");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			RrdTool::updateRouterBatmanAdvOriginatorsCountHistory($_GET['router_id'], $originators_count);
		}
	}
}

if($_GET['section']=="insert_olsr_data") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);

	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root

	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		try {
			$sql = "SELECT *
			        FROM  crawl_cycle
	        		ORDER BY crawl_date desc
				LIMIT 1;";
			$result = DB::getInstance()->query($sql);
			$last_crawl_cycle = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
	
		try {
			$sql = "SELECT *
	        		FROM  crawl_olsr
				WHERE router_id='$_GET[router_id]' AND crawl_cycle_id='$last_crawl_cycle[id]'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$crawl_olsr[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		if(empty($crawl_olsr)) {
			try {
				DB::getInstance()->exec("INSERT INTO crawl_olsr (router_id, crawl_cycle_id, olsrd_links, crawl_date)
							 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', '$_POST[olsrd_links]', NOW());");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
	}
}


?>