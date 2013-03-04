<?php

/**
 * This class is used as a container for static methods that deal with clients (hardware
 * connected to the routers)
 *
 * @package	Netmon
 */
class Clients {
	/**
	* Fetches the number of clients that where connected to all routers summed up.
	* @author  Clemens John <clemens-john@gmx.de>
	* @param int $crawl_cycle_id id of the crawl cycle you want to get the client number of
	* @return int number of connected clients
	*/
	public function countClientsCrawlCycle($crawl_cycle_id) {
		$client_count = 0;
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM crawl_clients_count WHERE crawl_cycle_id=?");
			$stmt->execute(array($crawl_cycle_id));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		foreach($rows as $row) {
			$client_count += $row['client_count'];
		}

		return $client_count;
	}

	/**
	* Fetches the number of clients that where connected to a router during a special crawl cycle
	* @author  Clemens John <clemens-john@gmx.de>
	* @param int $router_id router id
	* @param int $crawl_cycle_id id of the crawl cycle you want to get the client number of
	* @return int number of connected clients
	*/
	public function getClientsCountByRouterAndCrawlCycle($router_id, $crawl_cycle_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM crawl_clients_count WHERE crawl_cycle_id=? AND router_id=?");
			$stmt->execute(array($crawl_cycle_id, $router_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		if(!empty($rows))
			return $rows['client_count'];
		else
			return 0;
	}
}

?>
