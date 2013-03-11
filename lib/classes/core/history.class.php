<?php
require_once($GLOBALS['monitor_root'].'lib/classes/core/router.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/crawling.class.php');

class Event {
	public function addEvent($object, $object_id, $data) {
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		try {
			$stmt = DB::getInstance()->prepare("INSERT INTO history (crawl_cycle_id, object, object_id, create_date, data)
							    VALUES (?, ?, ?, NOW(), ?)");
			$stmt->execute(array($actual_crawl_cycle['id'], $object, $object_id, $data));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	public function getEventsByRouterId($router_id, $countlimit, $hourlimit) {
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		
		if($countlimit AND is_numeric($countlimit) AND is_numeric($router_id))
			$range = "
						WHERE object='router' AND object_id=$router_id AND crawl_cycle_id<'$actual_crawl_cycle[id]'
						ORDER BY history.create_date desc
					  LIMIT 0, $countlimit";
		elseif ($hourlimit AND is_numeric($hourlimit) AND is_numeric($actual_crawl_cycle['id']))
			$range = "WHERE history.create_date>=NOW() - INTERVAL $hourlimit HOUR AND object='service' AND crawl_cycle_id<'$actual_crawl_cycle[id]'
					  ORDER BY history.create_date desc";
		try {
			$sql = "SELECT id, object, object_id, create_date, data
			       FROM history
				   $range";

			$result = DB::getInstance()->query($sql);
			foreach($result as $key=>$row) {
				$history[$key] = $row;
				$history[$key]['data'] = unserialize($history[$key]['data']);
				$history[$key]['additional_data'] = Router::getRouterInfo($row['object_id']);
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $history;
	}

	public function getEventsByUserId($user_id, $countlimit) {
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		$history_cp = array();
		
		try {
			$stmt = DB::getInstance()->prepare("SELECT  * FROM routers WHERE user_id=?");
			$stmt->execute(array($user_id));
			$routers = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}		
		if(!empty($routers)) {
		foreach($routers as $router) {
			try {
				$stmt = DB::getInstance()->prepare("SELECT id, object, object_id, create_date, data
					FROM history
					WHERE object='router' AND object_id=:router_id AND crawl_cycle_id<:crawl_cycle_id
					ORDER BY history.create_date desc
					LIMIT 0, :limit");
					$stmt->bindValue(':limit', (int)$countlimit, PDO::PARAM_INT);
					$stmt->bindValue(':router_id', (int)$router['id'], PDO::PARAM_INT);
					$stmt->bindValue(':crawl_cycle_id', (int)$actual_crawl_cycle['id'], PDO::PARAM_INT);
					$stmt->execute();
					$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			foreach($result as $key=>$row) {
				$history[$key] = $row;
				$history[$key]['data'] = unserialize($history[$key]['data']);
				$history[$key]['additional_data'] = Router::getRouterInfo($row['object_id']);
				$user_history[] = $history[$key];
			}
		}

		$first = array();
		foreach($user_history as $key=>$user_hist) {
			$first[$key] = $user_hist['create_date'];
		}
		array_multisort($first, SORT_DESC, $user_history);

		for($i=0; $i<6; $i++) {
			if(!empty($user_history[$i])) {
				$history_cp[] = $user_history[$i];
			}
		}
		}

		return $history_cp;
	}

	public function getEvents($countlimit, $hourlimit) {
		$history = array();
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		if(isset($actual_crawl_cycle['id']) AND (isset($countlimit) OR isset($hourlimit))) {
			if($countlimit AND is_numeric($countlimit))
				$range = "
						WHERE history.crawl_cycle_id!=$actual_crawl_cycle[id]
						ORDER BY history.create_date desc
						LIMIT 0, $countlimit";
			elseif ($hourlimit AND is_numeric($hourlimit))
				$range = "WHERE history.create_date>=NOW() - INTERVAL $hourlimit HOUR AND history.crawl_cycle_id!=$actual_crawl_cycle[id]
						ORDER BY history.create_date desc";
						
			try {
				$stmt = DB::getInstance()->prepare("SELECT id, object, object_id, create_date, data FROM history $range");
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			foreach($rows as $key=>$row) {
				$history[$key] = $row;
				$history[$key]['data'] = unserialize($history[$key]['data']);
				if($history[$key]['object'] == "router") {
					$history[$key]['additional_data'] = Router::getRouterInfo($row['object_id']);
				}
			}
			return $history;
		}
	}
}
?>