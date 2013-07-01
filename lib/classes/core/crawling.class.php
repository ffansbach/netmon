<?php

class Crawling {
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