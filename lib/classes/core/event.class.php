<?php
require_once($GLOBALS['monitor_root'].'lib/classes/core/router.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/crawling.class.php');

class Event {
	public function addEvent($object, $object_id, $data) {
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		try {
			$stmt = DB::getInstance()->prepare("INSERT INTO events (crawl_cycle_id, object, object_id, create_date, data)
							    VALUES (?, ?, ?, NOW(), ?)");
			$stmt->execute(array($actual_crawl_cycle['id'], $object, $object_id, $data));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function deleteEvent($event_id) {
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM events WHERE id=?)");
			$stmt->execute($event_id);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function deleteEventsByObjectAndObjectId($object, $object_id) {
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM events WHERE object=? AND object_id=?");
			$stmt->execute(array($object, $object_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function cleanEventsTable($seconds) {
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM events WHERE UNIX_TIMESTAMP(create_date) < UNIX_TIMESTAMP(NOW())-?");
			$stmt->execute(array($seconds));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function getEvent($event_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
												FROM events
												WHERE id = $event_id");
			$stmt->execute(array($event_id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}
	
	public function getEventsByRouterId($router_id, $countlimit, $hourlimit) {
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		
		if($countlimit AND is_numeric($countlimit) AND is_numeric($router_id))
			$range = "
						WHERE object='router' AND object_id=$router_id AND crawl_cycle_id<'$actual_crawl_cycle[id]'
						ORDER BY events.create_date desc
					  LIMIT 0, $countlimit";
		elseif ($hourlimit AND is_numeric($hourlimit) AND is_numeric($actual_crawl_cycle['id']))
			$range = "WHERE events.create_date>=NOW() - INTERVAL $hourlimit HOUR AND object='service' AND crawl_cycle_id<'$actual_crawl_cycle[id]'
					  ORDER BY events.create_date desc";
		try {
			$sql = "SELECT *
			       FROM events
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
				$stmt = DB::getInstance()->prepare("SELECT *
					FROM events
					WHERE object='router' AND object_id=:router_id AND crawl_cycle_id<:crawl_cycle_id
					ORDER BY events.create_date desc
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
			foreach($rows as $key=>$row) {
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
						WHERE events.crawl_cycle_id!=$actual_crawl_cycle[id]
						ORDER BY events.create_date desc
						LIMIT 0, $countlimit";
			elseif ($hourlimit AND is_numeric($hourlimit))
				$range = "WHERE events.create_date>=NOW() - INTERVAL $hourlimit HOUR AND events.crawl_cycle_id!=$actual_crawl_cycle[id]
						ORDER BY events.create_date desc";
						
			try {
				$stmt = DB::getInstance()->prepare("SELECT * FROM events $range");
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