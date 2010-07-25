<?php
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/login.class.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/crawling.class.php');

if($_GET['section']=="insert_interface") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);

	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		$last_crawl_cycle = Crawling::getActualCrawlCycle();

		try {
			$sql = "SELECT *
	        		FROM  crawl_interfaces
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
				DB::getInstance()->exec("INSERT INTO crawl_interfaces (router_id, crawl_cycle_id, crawl_date, name, mac_addr, ipv4_addr, ipv6_addr, ipv6_link_local_addr, traffic_rx, traffic_tx, wlan_mode, wlan_frequency, wlan_essid, wlan_bssid, wlan_tx_power)
							 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', NOW(), '$_GET[name]', '$_GET[mac_addr]', '$_GET[ipv4_addr]', '$_GET[ipv6_addr]', '$_GET[ipv6_link_local_addr]', '$_GET[traffic_rx]', '$_GET[traffic_tx]', '$_GET[wlan_mode]', '$_GET[wlan_frequency]', '$_GET[wlan_essid]', '$_GET[wlan_bssid]', '$_GET[wlan_tx_power]');");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
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
			$originators = serialize($originators);
			
			try {
				DB::getInstance()->exec("INSERT INTO crawl_batman_advanced_originators (router_id, crawl_cycle_id, originators, crawl_date)
							 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', '$originators', NOW());");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
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