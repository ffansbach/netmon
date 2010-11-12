<?php

require_once('lib/classes/core/history.class.php');
require_once('lib/classes/core/router.class.php');

class Crawling {
	public function newCrawlCycle() {
		try {
			DB::getInstance()->exec("INSERT INTO crawl_cycle (crawl_date)
							      VALUES (NOW());");
		}
		catch(PDOException $e) {
			echo $e->getMessage();
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
	}

	public function insertRouterCrawl($router_id, $crawl_data) {
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		$crawl_router = Router::getCrawlRouterByCrawlCycleId($actual_crawl_cycle['id'], $router_id);

		if(empty($crawl_router)) {
			$crawl_data['description'] = rawurldecode($crawl_data['description']);
			$crawl_data['location'] = rawurldecode($crawl_data['location']);

			try {
				DB::getInstance()->exec("INSERT INTO crawl_routers (router_id, crawl_cycle_id, crawl_date, status, ping, hostname, description, location, latitude, longitude, luciname, luciversion, distname, distversion, chipset, cpu, memory_total, memory_caching, memory_buffering, memory_free, loadavg, processes, uptime, idletime, local_time, community_essid, community_nickname, community_email, community_prefix)
							 VALUES ('$router_id', '$actual_crawl_cycle[id]', NOW(), '$crawl_data[status]', '$crawl_data[ping]', '$crawl_data[hostname]', '$crawl_data[description]', '$crawl_data[location]', '$crawl_data[latitude]', '$crawl_data[longitude]', '$crawl_data[luciname]', '$crawl_data[luciversion]', '$crawl_data[distname]', '$crawl_data[distversion]', '$crawl_data[chipset]', '$crawl_data[cpu]', '$crawl_data[memory_total]', '$crawl_data[memory_caching]', '$crawl_data[memory_buffering]', '$crawl_data[memory_free]', '$crawl_data[loadavg]', '$crawl_data[processes]', '$crawl_data[uptime]', '$crawl_data[idletime]', '$crawl_data[local_time]', '$crawl_data[community_essid]', '$crawl_data[community_nickname]', '$crawl_data[community_email]', '$crawl_data[community_prefix]');");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}

		//Make router history
		History::makeRouterHistoryEntry($crawl_data, $router_id);
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
					ORDER BY crawl_date desc
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
			$sql = "SELECT  *
					FROM crawl_cycle
					ORDER BY crawl_date desc
					LIMIT 1";
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