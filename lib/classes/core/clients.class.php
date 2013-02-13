<?php

class Clients {
/*	public function getClientsByRouterAndCrawlCycle($router_id, $crawl_cycle_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_clients
					WHERE crawl_cycle_id='$crawl_cycle_id' AND router_id='$router_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$clients[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $clients;
	}
	public function countClientsByRouterAndCrawlCycle($router_id, $crawl_cycle_id) {
		try {
			$sql = "SELECT  COUNT(*) as count
					FROM crawl_clients
					WHERE crawl_cycle_id='$crawl_cycle_id' AND router_id='$router_id'";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count['count'];
	}
*/
	public function countClientsCrawlCycle($crawl_cycle_id) {
		$client_count = 0;
		try {
			$sql = "SELECT  *
					FROM crawl_clients_count
					WHERE crawl_cycle_id='$crawl_cycle_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$client_count = $client_count+$row['client_count'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $client_count;
	}

	public function getClientsCountByRouterAndCrawlCycle($router_id, $crawl_cycle_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_clients_count
					WHERE crawl_cycle_id='$crawl_cycle_id' AND router_id='$router_id'";
			$result = DB::getInstance()->query($sql);
			$clients = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		if(!empty($clients)) {
			return $clients['client_count'];
		} else {
			return 0;
		}
	}
}

?>
