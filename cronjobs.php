<?php
	//This script can only be called by the server
	if(!empty($_SERVER['REMOTE_ADDR'])) {
		die("This script can only be run by the server directly.");
	}

	/**
	 * Necessary includes
	 **/
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	require_once(ROOT_DIR.'/lib/core/router_old.class.php');
	require_once(ROOT_DIR.'/lib/core/rrdtool.class.php');
	require_once(ROOT_DIR.'/lib/core/Eventlist.class.php');
	require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterfacelist.class.php');
	require_once(ROOT_DIR.'/lib/core/NetworkinterfaceStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/EventNotificationList.class.php');

	/**
	 * Crawlcycle organisation
	 **/
	echo "Organizing crawl cycles\n";
	//Get crawl cycle data
	$actual_crawl_cycle = Crawling::getActualCrawlCycle();
	$crawl_cycle_start_buffer_in_minutes = ConfigLine::configByName("crawl_cycle_start_buffer_in_minutes");
	if(empty($actual_crawl_cycle) OR (strtotime($actual_crawl_cycle['crawl_date'])+(($GLOBALS['crawl_cycle']-$crawl_cycle_start_buffer_in_minutes)*60))<=time()) {
		echo "Create crawl data for offline routers\n";
		//Set all routers in old crawl cycle that have not been crawled yet to status offline
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
												FROM routers
												WHERE id not in (SELECT router_id
																 FROM crawl_routers
																 WHERE crawl_cycle_id=$actual_crawl_cycle[id])");
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}

		//Use custom multiinsert statement instead of RouterStatus class
		echo "	Prepare insertion of offline data for router\n";
		$placeholders = array();
		$values = array();
		foreach($result as $router) {
			$placeholders[] = "(?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			array_push($values, (int)$router['id'], (int)$actual_crawl_cycle['id'], "offline",
								"", "", "", "",
								"", 0, 0, 0,
								0, "", "", "", "",
								"", "", "",
								"", "",
								"", "",
								"", "",
								"", 0);
		}
		echo "	Insert offline Data for interfaces in one big statement\n";
		try {
			$stmt = DB::getInstance()->prepare("INSERT INTO crawl_routers (router_id, crawl_cycle_id, crawl_date, status,
																		   hostname, distname, distversion, chipset,
																		   cpu, memory_total, memory_caching, memory_buffering,
																		   memory_free, loadavg, processes, uptime, idletime,
																		   local_time, batman_advanced_version, fastd_version,
																		   kernel_version, configurator_version,
																		   nodewatcher_version, firmware_version,
																		   firmware_revision, openwrt_core_revision,
																		   openwrt_feeds_packages_revision, client_count)
												VALUES ".implode(", ", $placeholders));
			$stmt->execute($values);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}

		echo "Create crawl data for offline interfaces\n";
		//store offline crawl for all interfaces of offline router
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
												FROM interfaces
												WHERE id not in (SELECT interface_id
																 FROM crawl_interfaces
																 WHERE crawl_cycle_id=$actual_crawl_cycle[id])");
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}

		//Use custom multiinsert statement instead of InterfaceStatus class
		echo "	Prepare insertion of offline data for interfaces\n";
		$placeholders = array();
		$values = array();
		foreach($result as $interface) {
			$placeholders[] = "(?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			array_push($values, (int)$interface['router_id'], (int)$actual_crawl_cycle['id'], (int)$interface['id'], "", "", 0, 0, 0, 0, "", "", "", "", 0, 0);
		}
		echo "	Insert offline Data for interfaces in one big statement\n";
		$stmt = DB::getInstance()->prepare("INSERT INTO crawl_interfaces (router_id, crawl_cycle_id, interface_id, crawl_date,
																		  name, mac_addr, traffic_rx, traffic_rx_avg,
																		  traffic_tx, traffic_tx_avg,
																		  wlan_mode, wlan_frequency, wlan_essid, wlan_bssid,
																		  wlan_tx_power, mtu)
											VALUES ".implode(", ", $placeholders));
		$stmt->execute($values);


		echo "Close old crawl cycle and create new one\n";
		//Create new crawl cycle and close old crawl cycle
		//Create new crawl cycle
		Crawling::newCrawlCycle();
		//Close old Crawl cycle
		Crawling::closeCrawlCycle($actual_crawl_cycle['id']);

		echo "Create graph statistics\n";
		//Make statistic graphs
		$online = Router_old::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'online');
		$offline = Router_old::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'offline');
		$unknown = Router_old::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'unknown');
		$total = $unknown+$offline+$online;
		RrdTool::updateNetmonHistoryRouterStatus($online, $offline, $unknown, $total);

		$client_count = Router_old::countRoutersByCrawlCycleId($actual_crawl_cycle['id']);
		RrdTool::updateNetmonClientCount($client_count);

		/**
		 * Clean database
		 */
		echo "Clean database\n";

		//Delete old Crawls
		echo "Remove old crawl data\n";
		Crawling::deleteOldCrawlDataExceptLastOnlineCrawl(($GLOBALS['hours_to_keep_mysql_crawl_data']*60*60));

		//Remove old events
		echo "Remove old events\n";
		$secondsToKeepHistoryTable = 60*60*$GLOBALS['hours_to_keep_history_table'];
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM events WHERE UNIX_TIMESTAMP(create_date) < UNIX_TIMESTAMP(NOW())-?");
			$stmt->execute(array($secondsToKeepHistoryTable));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		//Remove old not assigned routers
		echo "Remove not assigned routers that haven´t been updated for a while\n";
		DB::getInstance()->exec("DELETE FROM routers_not_assigned WHERE TO_DAYS(update_date) < TO_DAYS(NOW())-2");

		/**
		* Crawl
		**/
		echo "Do crawling\n";

		//Crawl routers
		echo "Crawl routers\n";

		if (!isset($GLOBALS['crawllog']))
			$GLOBALS['crawllog'] = false;

		if ($GLOBALS['crawllog'] && !is_dir(ROOT_DIR."/logs/")) {
			mkdir(ROOT_DIR."/logs/");
		}

		$range = ConfigLine::configByName("crawl_range");
		$routers_count = Router_old::countRouters();
		for ($i=0; $i<=$routers_count; $i+=$range) {
			//start an independet crawl process for each $range routers to crawl routers simultaniously
			$return = array();
			if ($GLOBALS['crawllog'])
				$logcmd = "> ".ROOT_DIR."/logs/crawler_".$i."-".($i+$range).".txt";
			else
				$logcmd = "> /dev/null";
			$cmd = "php ".ROOT_DIR."/integrated_xml_ipv6_crawler.php -o".$i." -l".$range." ".$logcmd." & echo $!";
			echo "Initializing crawl process to crawl routers ".$i." to ".($i+$range)."\n";
			echo "Running command: $cmd\n";
			exec($cmd, $return);
			echo "The initialized crawl process has the pid $return[0]\n";
		}

		/**
		 * Notifications
		 */

		echo "Sending notifications\n";
		$event_notification_list = new EventNotificationList();
		$event_notification_list->notify();
        } else {
                echo "There is an crawl cycle running actually. Doing nothing.\n";
        }

	echo "Done\n";
?>
