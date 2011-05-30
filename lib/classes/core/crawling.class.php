<?php

require_once('lib/classes/core/history.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/rrdtool.class.php');
require_once('lib/classes/core/clients.class.php');

class Crawling {
	public function organizeCrawlCycles()  {
		//Get actual crawl cycle
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		//Get last ended crawl cycle
		$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();

		if(strtotime($actual_crawl_cycle['crawl_date'])+(($GLOBALS['crawl_cycle']-1)*60)<=time()) {
			//Initialise new crawl cycle before closing old crawl cycle
			Crawling::newCrawlCycle();
			
			//Close old Crawl cycle
			$result = DB::getInstance()->exec("UPDATE crawl_cycle SET
								  crawl_date_end = NOW()
							   WHERE id = '$actual_crawl_cycle[id]'");

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

			$client_count = Clients::countClientsCrawlCycle($actual_crawl_cycle['id']);
			RrdTool::updateNetmonClientCount($client_count);
		}
	}

	public function newCrawlCycle() {
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		if(strtotime($actual_crawl_cycle['crawl_date'])+(($GLOBALS['crawl_cycle']-1)*60)<=time()) {
			try {
				DB::getInstance()->exec("INSERT INTO crawl_cycle (crawl_date)
								      VALUES (NOW());");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
	}
	
	public function getCrawlCycleById($crawl_cycle_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_cycle
					WHERE id=$crawl_cycle_id";
			$result = DB::getInstance()->query($sql);
			$count_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count_data;
	}

	public function getNextSmallerCrawlCycleById($crawl_cycle_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_cycle
					WHERE id<$crawl_cycle_id
					ORDER BY id DESC
					LIMIT 1";
			$result = DB::getInstance()->query($sql);
			$count_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count_data;
	}

	public function getNextBiggerCrawlCycleById($crawl_cycle_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_cycle
					WHERE id>$crawl_cycle_id
					ORDER BY id ASC
					LIMIT 1";
			$result = DB::getInstance()->query($sql);
			$count_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count_data;
	}

	public function deleteOldCrawlData($days) {
		DB::getInstance()->exec("DELETE FROM crawl_cycle WHERE TO_DAYS(crawl_date) < TO_DAYS(NOW())-$days");
		DB::getInstance()->exec("DELETE FROM crawl_routers WHERE TO_DAYS(crawl_date) < TO_DAYS(NOW())-$days");
		DB::getInstance()->exec("DELETE FROM crawl_interfaces WHERE TO_DAYS(crawl_date) < TO_DAYS(NOW())-$days");
		DB::getInstance()->exec("DELETE FROM crawl_batman_advanced_interfaces WHERE TO_DAYS(crawl_date) < TO_DAYS(NOW())-$days");
		DB::getInstance()->exec("DELETE FROM crawl_batman_advanced_originators WHERE TO_DAYS(crawl_date) < TO_DAYS(NOW())-$days");
		DB::getInstance()->exec("DELETE FROM crawl_olsr WHERE TO_DAYS(crawl_date) < TO_DAYS(NOW())-$days");
		DB::getInstance()->exec("DELETE FROM crawl_services WHERE TO_DAYS(crawl_date) < TO_DAYS(NOW())-$days");
		DB::getInstance()->exec("DELETE FROM crawl_clients_count WHERE TO_DAYS(crawl_date) < TO_DAYS(NOW())-$days");
		DB::getInstance()->exec("DELETE FROM history WHERE TO_DAYS(create_date) < TO_DAYS(NOW())-$days");
	}

	//Returns true if router has already been crawled
	public function checkIfRouterHasBeenCrawled($router_id, $crawl_cycle_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE router_id='$router_id' AND crawl_cycle_id='$crawl_cycle_id'";
			$result = DB::getInstance()->query($sql);
			$crawl_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		if(!empty($crawl_data)) {
			return true;
		} else {
			return false;
		}
	}

	public function insertRouterCrawl($router_id, $crawl_data, $actual_crawl_cycle=array(), $last_endet_crawl_cycle=array()) {
		if(empty($actual_crawl_cycle)) {
			$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		}
		$crawl_router = Router::getCrawlRouterByCrawlCycleId($actual_crawl_cycle['id'], $router_id);

		if(empty($crawl_router)) {
			$crawl_data['description'] = rawurldecode($crawl_data['description']);
			$crawl_data['location'] = rawurldecode($crawl_data['location']);

			try {
				DB::getInstance()->exec("INSERT INTO crawl_routers (router_id, crawl_cycle_id, crawl_date, status, ping, hostname, description, location, latitude, longitude, luciname, luciversion, distname, distversion, chipset, cpu, memory_total, memory_caching, memory_buffering, memory_free, loadavg, processes, uptime, idletime, local_time, community_essid, community_nickname, community_email, community_prefix, batman_advanced_version, kernel_version, nodewatcher_version, firmware_version)
							 VALUES ('$router_id', '$actual_crawl_cycle[id]', NOW(), '$crawl_data[status]', '$crawl_data[ping]', '$crawl_data[hostname]', '$crawl_data[description]', '$crawl_data[location]', '$crawl_data[latitude]', '$crawl_data[longitude]', '$crawl_data[luciname]', '$crawl_data[luciversion]', '$crawl_data[distname]', '$crawl_data[distversion]', '$crawl_data[chipset]', '$crawl_data[cpu]', '$crawl_data[memory_total]', '$crawl_data[memory_caching]', '$crawl_data[memory_buffering]', '$crawl_data[memory_free]', '$crawl_data[loadavg]', '$crawl_data[processes]', '$crawl_data[uptime]', '$crawl_data[idletime]', '$crawl_data[local_time]', '$crawl_data[community_essid]', '$crawl_data[community_nickname]', '$crawl_data[community_email]', '$crawl_data[community_prefix]', '$crawl_data[batman_advanced_version]', '$crawl_data[kernel_version]', '$crawl_data[nodewatcher_version]', '$crawl_data[firmware_version]');");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}

		//Make router history
		History::makeRouterHistoryEntry($crawl_data, $router_id, $actual_crawl_cycle, $last_endet_crawl_cycle);
		Router::routerOfflineNotification($router_id, $crawl_data);
	}


	public function getCrawlCycleHistory($history_start, $history_end) {
		try {
			$sql = "SELECT  *
					FROM crawl_cycle
				WHERE crawl_date >= FROM_UNIXTIME($history_start) AND crawl_date <= FROM_UNIXTIME($history_end)
				ORDER BY id desc";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$cycles[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $cycles;
	}

	public function getLastEndedCrawlCycle() {
		try {
			$sql = "SELECT  *
					FROM crawl_cycle
					ORDER BY id desc
					LIMIT 1,1";
			$result = DB::getInstance()->query($sql);
			$count_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count_data;
	}

	public function getActualCrawlCycle() {
		try {
			$sql = "SELECT *
				FROM crawl_cycle AS t1
				WHERE id = (
						SELECT max( id )
						FROM crawl_cycle AS t2
					   )";
			$result = DB::getInstance()->query($sql);
			$count_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count_data;
	}
}

?>