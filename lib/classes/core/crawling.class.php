<?php

require_once(ROOT_DIR.'/lib/classes/core/event.class.php');
require_once(ROOT_DIR.'/lib/classes/core/router.class.php');
require_once(ROOT_DIR.'/lib/classes/core/rrdtool.class.php');

class Crawling {
	public function organizeCrawlCycles()  {
		//Get actual crawl cycle
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		//Get last ended crawl cycle
		$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();

		//if no crawl cycle has been created until now or
		//if its time to create a new crawl cycle
		if(empty($actual_crawl_cycle) OR strtotime($actual_crawl_cycle['crawl_date'])+(($GLOBALS['crawl_cycle']-1)*60)<=time()) {
			//Initialise new crawl cycle
			Crawling::newCrawlCycle();
		}
		
		if(!empty($actual_crawl_cycle) AND strtotime($actual_crawl_cycle['crawl_date'])+(($GLOBALS['crawl_cycle']-1)*60)<=time()) {
			//Close old Crawl cycle
			Crawling::closeCrawlCycle($actual_crawl_cycle['id']);

			//Set all routers in old crawl cycle that have not been crawled yet to status offline
			$routers = Router::getRouters();
			foreach ($routers as $router) {
				$crawl = Router::getCrawlRouterByCrawlCycleId($actual_crawl_cycle['id'], $router['id']);
				if(empty($crawl)) {
					$crawl_data['status'] = "offline";
					Crawling::insertRouterCrawl($router['id'], $crawl_data, $actual_crawl_cycle, $last_endet_crawl_cycle);
				}
			}

			//Make statistic graphs
			$online = Router::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'online');
			$offline = Router::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'offline');
			$unknown = Router::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'unknown');
			$total = $unknown+$offline+$online;
			RrdTool::updateNetmonHistoryRouterStatus($online, $offline, $unknown, $total);

			$client_count = Router::countRoutersByCrawlCycleId($actual_crawl_cycle['id']);
			RrdTool::updateNetmonClientCount($client_count);
		}
	}

	public function newCrawlCycle($minuteOffset=0) {
		try {
			$stmt = DB::getInstance()->prepare("INSERT INTO crawl_cycle (crawl_date) VALUES (NOW()-INTERVAL ? MINUTE)");
			$stmt->execute(array($minuteOffset));
			return DB::getInstance()->lastInsertId();
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function closeCrawlCycle($crawl_cycle_id) {
		try {
			$stmt = DB::getInstance()->prepare("UPDATE crawl_cycle SET crawl_date_end = NOW() WHERE id=?");
			$stmt->execute(array($crawl_cycle_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function getCrawlCycleById($crawl_cycle_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM crawl_cycle WHERE id=?");
			$stmt->execute(array($crawl_cycle_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
	}

	public function getNextSmallerCrawlCycleById($crawl_cycle_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM crawl_cycle WHERE id<? ORDER BY id DESC LIMIT 1");
			$stmt->execute(array($crawl_cycle_id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	public function getNextBiggerCrawlCycleById($crawl_cycle_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM crawl_cycle WHERE id>? ORDER BY id ASC LIMIT 1");
			$stmt->execute(array($crawl_cycle_id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function deleteOldCrawlDataExceptLastOnlineCrawl($seconds) {
		//Get last online CrawlCycleId of every router
		try {
			$stmt = DB::getInstance()->prepare("SELECT crawl_cycle_id, router_id FROM 
							      (SELECT * FROM crawl_routers
							       WHERE crawl_routers.status='online'
							       ORDER BY crawl_cycle_id DESC)
							     AS s
							     GROUP BY router_id");
			$stmt->execute();
			$last_online_crawl_cycles = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}

		//Make an Where string that excludes the last online crawl cycles from query
		$except = "";
		$except_crawl_cycle_ids = "";
		foreach ($last_online_crawl_cycles as $key=>$last_online_crawl_cycle) {
//			$except .= " AND (router_id!=$last_online_crawl_cycle[router_id] AND crawl_cycle_id!=$last_online_crawl_cycle[crawl_cycle_id])";
			$except .= " AND crawl_cycle_id!=$last_online_crawl_cycle[crawl_cycle_id]";
			$except_crawl_cycle_ids .= " AND id!=$last_online_crawl_cycle[crawl_cycle_id]";
		}
		
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_cycle WHERE UNIX_TIMESTAMP(crawl_date) < UNIX_TIMESTAMP(NOW())-? $except_crawl_cycle_ids");
			$stmt->execute(array($seconds));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_routers WHERE UNIX_TIMESTAMP(crawl_date) < UNIX_TIMESTAMP(NOW())-? $except");
			$stmt->execute(array($seconds));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_interfaces WHERE UNIX_TIMESTAMP(crawl_date) < UNIX_TIMESTAMP(NOW())-? $except");
			$stmt->execute(array($seconds));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_ips WHERE UNIX_TIMESTAMP(crawl_date) < UNIX_TIMESTAMP(NOW())-? $except");
			$stmt->execute(array($seconds));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_batman_advanced_interfaces WHERE UNIX_TIMESTAMP(crawl_date) < UNIX_TIMESTAMP(NOW())-? $except");
			$stmt->execute(array($seconds));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_batman_advanced_originators WHERE UNIX_TIMESTAMP(crawl_date) < UNIX_TIMESTAMP(NOW())-? $except");
			$stmt->execute(array($seconds));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_olsr WHERE UNIX_TIMESTAMP(crawl_date) < UNIX_TIMESTAMP(NOW())-? $except");
			$stmt->execute(array($seconds));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		//Normal delete
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_services WHERE UNIX_TIMESTAMP(crawl_date) < UNIX_TIMESTAMP(NOW())-?");
			$stmt->execute(array($seconds));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	//Returns true if router has already been crawled
	public function checkIfRouterHasBeenCrawled($router_id, $crawl_cycle_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  *
							    FROM crawl_routers
							    WHERE router_id=? AND crawl_cycle_id=?");
			$stmt->execute(array($router_id, $crawl_cycle_id));
			return $stmt->rowCount();
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function insertRouterCrawl($router_id, $crawl_data, $actual_crawl_cycle=array(), $last_endet_crawl_cycle=array()) {
		if(empty($actual_crawl_cycle)) {
			$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		}
		$crawl_router = Router::getCrawlRouterByCrawlCycleId($actual_crawl_cycle['id'], $router_id);

		if(empty($crawl_router)) {
			//set default values if nothing is set
			if ($crawl_data['status'] != "online" AND $crawl_data['status'] != "offline" AND $crawl_data['status'] != "unknown") $crawl_data['status'] = "unknown";
			
			$crawl_data['description'] = (isset($crawl_data['description']) ? trim(rawurldecode($crawl_data['description'])) : "");
			$crawl_data['location'] = (isset($crawl_data['location']) ? trim(rawurldecode($crawl_data['location'])) : "");
			$crawl_data['latitude'] = (isset($crawl_data['latitude']) ? trim($crawl_data['latitude']) : "");
			$crawl_data['longitude'] = (isset($crawl_data['longitude']) ? trim($crawl_data['longitude']) : "");
			$crawl_data['luciname'] = (isset($crawl_data['luciname']) ? trim($crawl_data['luciname']) : "");
			$crawl_data['luciversion'] = (isset($crawl_data['luciversion']) ? trim($crawl_data['luciversion']) : "");
			$crawl_data['distname'] = (isset($crawl_data['distname']) ? trim($crawl_data['distname']) : "");
			$crawl_data['distversion'] = (isset($crawl_data['distversion']) ? trim($crawl_data['distversion']) : "");
			$crawl_data['chipset'] = (isset($crawl_data['chipset']) ? trim($crawl_data['chipset']) : "");
			$crawl_data['cpu'] = (isset($crawl_data['cpu']) ? trim($crawl_data['cpu']) : "");
			$crawl_data['memory_total'] = (isset($crawl_data['memory_total']) ? trim($crawl_data['memory_total']) : "");
			$crawl_data['memory_caching'] = (isset($crawl_data['memory_caching']) ? trim($crawl_data['memory_caching']) : "");
			$crawl_data['memory_buffering'] = (isset($crawl_data['memory_buffering']) ? trim($crawl_data['memory_buffering']) : "");
			$crawl_data['memory_free'] = (isset($crawl_data['memory_free']) ? trim($crawl_data['memory_free']) : "");
			$crawl_data['loadavg'] = (isset($crawl_data['loadavg']) ? trim($crawl_data['loadavg']) : "");
			$crawl_data['processes'] = (isset($crawl_data['processes']) ? trim($crawl_data['processes']) : "");
			$crawl_data['uptime'] = (isset($crawl_data['uptime']) ? trim($crawl_data['uptime']) : "");
			$crawl_data['idletime'] = (isset($crawl_data['idletime']) ? trim($crawl_data['idletime']) : "");
			$crawl_data['local_time'] = (isset($crawl_data['local_time']) ? trim($crawl_data['local_time']) : "");
			$crawl_data['community_essid'] = (isset($crawl_data['community_essid']) ? trim($crawl_data['community_essid']) : "");
			$crawl_data['community_nickname'] = (isset($crawl_data['community_nickname']) ? trim($crawl_data['community_nickname']) : "");
			$crawl_data['community_email'] = (isset($crawl_data['community_email']) ? trim($crawl_data['community_email']) : "");
			$crawl_data['community_prefix'] = (isset($crawl_data['community_prefix']) ? trim($crawl_data['community_prefix']) : "");
			$crawl_data['batman_advanced_version'] = (isset($crawl_data['batman_advanced_version']) ? trim($crawl_data['batman_advanced_version']) : "");
			$crawl_data['kernel_version'] = (isset($crawl_data['kernel_version']) ? trim($crawl_data['kernel_version']) : "");
			$crawl_data['nodewatcher_version'] = (isset($crawl_data['nodewatcher_version']) ? trim($crawl_data['nodewatcher_version']) : "");
			$crawl_data['firmware_version'] = (isset($crawl_data['firmware_version']) ? trim($crawl_data['firmware_version']) : "");
			$crawl_data['firmware_revision'] = (isset($crawl_data['firmware_revision']) ? trim($crawl_data['firmware_revision']) : "");
			$crawl_data['openwrt_core_revision'] = (isset($crawl_data['openwrt_core_revision']) ? trim($crawl_data['openwrt_core_revision']) : "");
			$crawl_data['openwrt_feeds_packages_revision'] = (isset($crawl_data['openwrt_feeds_packages_revision']) ? trim($crawl_data['openwrt_feeds_packages_revision']) : "");
			$crawl_data['hostname'] = (isset($crawl_data['hostname']) ? trim($crawl_data['hostname']) : "");

			//insert data into the database
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO crawl_routers (router_id, crawl_cycle_id, crawl_date, status, hostname, description, location, latitude, longitude, luciname, luciversion, distname, distversion, chipset, cpu, memory_total, memory_caching, memory_buffering, memory_free, loadavg, processes, uptime, idletime, local_time, community_essid, community_nickname, community_email, community_prefix, batman_advanced_version, kernel_version, nodewatcher_version, firmware_version, firmware_revision, openwrt_core_revision, 	openwrt_feeds_packages_revision)
								    VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$stmt->execute(array($router_id, $actual_crawl_cycle['id'], $crawl_data['status'], $crawl_data['hostname'], $crawl_data['description'], $crawl_data['location'],
						     $crawl_data['latitude'], $crawl_data['longitude'], $crawl_data['luciname'], $crawl_data['luciversion'], $crawl_data['distname'], $crawl_data['distversion'], $crawl_data['chipset'],
						     $crawl_data['cpu'], $crawl_data['memory_total'], $crawl_data['memory_caching'], $crawl_data['memory_buffering'], $crawl_data['memory_free'], $crawl_data['loadavg'],
						     $crawl_data['processes'], $crawl_data['uptime'], $crawl_data['idletime'], $crawl_data['local_time'], $crawl_data['community_essid'], $crawl_data['community_nickname'], $crawl_data['community_email'], $crawl_data['community_prefix'], $crawl_data['batman_advanced_version'],
						     $crawl_data['kernel_version'], $crawl_data['nodewatcher_version'], $crawl_data['firmware_version'], $crawl_data['firmware_revision'], $crawl_data['openwrt_core_revision'], $crawl_data['openwrt_feeds_packages_revision']));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
		}

		//Make router history
		Crawling::makeRouterHistoryEntry($crawl_data, $router_id, $actual_crawl_cycle, $last_endet_crawl_cycle);
	}
	
	public function makeRouterHistoryEntry($current_crawl_data, $router_id, $actual_crawl_cycle=array(), $last_endet_crawl_cycle=array()){
		if(empty($actual_crawl_cycle)) {
			$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		}
		if(empty($last_endet_crawl_cycle)) {
			$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		}

		$last_crawl_data = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $router_id);

		//erstelle online/offline Benachrichtigung aus aktuellem und letztem crawl Eintrag
		if (!empty($current_crawl_data['status']) AND $current_crawl_data['status']!=$last_crawl_data['status']) {
			$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'status', 'from'=>$last_crawl_data['status'], 'to'=>$current_crawl_data['status']));
		}
		
		//erstelle reboot Benachrichtigung aus aktuellem und letztem crawl Eintrag
		if($current_crawl_data['status']=='online' AND $last_crawl_data['status']=='online') {
			if ((!empty($current_crawl_data['uptime']) AND !empty($last_crawl_data['uptime'])) AND ($current_crawl_data['uptime']<$last_crawl_data['uptime'])) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'reboot'));
			}
		}

		if($last_crawl_data['status']!="online") {
			$last_online_crawl_data = Router::getLastOnlineCrawlByRouterId($router_id);
		} else {
			$last_online_crawl_data = $last_crawl_data;
		}

		if($current_crawl_data['status']=='online') {
			if (!empty($current_crawl_data['luciname']) AND !empty($last_online_crawl_data['luciname']) AND $current_crawl_data['luciname']!=$last_online_crawl_data['luciname']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'luciname', 'from'=>$last_crawl_data['luciname'], 'to'=>$current_crawl_data['luciname']));
			}
			if (!empty($current_crawl_data['luciversion']) AND !empty($last_online_crawl_data['luciversion']) AND $current_crawl_data['luciversion']!=$last_online_crawl_data['luciversion']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'luciversion', 'from'=>$last_crawl_data['luciversion'], 'to'=>$current_crawl_data['luciversion']));
			}
			if (!empty($current_crawl_data['distname']) AND !empty($last_online_crawl_data['distname']) AND $current_crawl_data['distname']!=$last_online_crawl_data['distname']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'distname', 'from'=>$last_crawl_data['distname'], 'to'=>$current_crawl_data['distname']));
			}
			if (!empty($current_crawl_data['distversion']) AND !empty($last_online_crawl_data['distversion']) AND $current_crawl_data['distversion']!=$last_online_crawl_data['distversion']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'distversion', 'from'=>$last_crawl_data['distversion'], 'to'=>$current_crawl_data['distversion']));
			}
			if (!empty($current_crawl_data['batman_advanced_version']) AND !empty($last_online_crawl_data['batman_advanced_version']) AND $current_crawl_data['batman_advanced_version']!=$last_online_crawl_data['batman_advanced_version']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'batman_advanced_version', 'from'=>$last_crawl_data['batman_advanced_version'], 'to'=>$current_crawl_data['batman_advanced_version']));
			}
			if (!empty($current_crawl_data['firmware_version']) AND !empty($last_online_crawl_data['firmware_version']) AND $current_crawl_data['firmware_version']!=$last_online_crawl_data['firmware_version']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'firmware_version', 'from'=>$last_crawl_data['firmware_version'], 'to'=>$current_crawl_data['firmware_version']));
			}
			if (!empty($current_crawl_data['nodewatcher_version']) AND !empty($last_online_crawl_data['nodewatcher_version']) AND $current_crawl_data['nodewatcher_version']!=$last_online_crawl_data['nodewatcher_version']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'nodewatcher_version', 'from'=>$last_crawl_data['nodewatcher_version'], 'to'=>$current_crawl_data['nodewatcher_version']));
			}
			if (!empty($current_crawl_data['hostname']) AND !empty($last_online_crawl_data['hostname']) AND $current_crawl_data['hostname']!=$last_online_crawl_data['hostname']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'hostname', 'from'=>$last_crawl_data['hostname'], 'to'=>$current_crawl_data['hostname']));
			}
			if (!empty($current_crawl_data['chipset']) AND !empty($last_online_crawl_data['chipset']) AND $current_crawl_data['chipset']!=$last_online_crawl_data['chipset']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'chipset', 'from'=>$last_crawl_data['chipset'], 'to'=>$current_crawl_data['chipset']));
			}
		}

		if (isset($history_data) AND is_array($history_data)) {
			foreach ($history_data as $hist_data) {
				Event::addEvent('router', $router_id, $hist_data);
			}
		}
	}

	public function getCrawlCycleHistory($history_start, $history_end) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  *
							    FROM crawl_cycle
							    WHERE crawl_date >= FROM_UNIXTIME(?) AND crawl_date <= FROM_UNIXTIME(?)
							    ORDER BY id desc");
			$stmt->execute(array($history_start, $history_end));
			return $stmt->fetchAll();
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	public function getLastEndedCrawlCycle() {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM crawl_cycle ORDER BY id desc LIMIT 1,1");
			$stmt->execute();
			return $stmt->fetch();
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	public function getActualCrawlCycle() {
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
							    FROM crawl_cycle AS t1
							    WHERE id = (
								SELECT max( id )
								FROM crawl_cycle AS t2
							    )");
			$stmt->execute();
			return $stmt->fetch();
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
}

?>