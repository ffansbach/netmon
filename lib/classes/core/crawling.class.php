<?php

class Crawling {
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