<?php
class Olsr {
	public function insertOlsrData($crawl_id, $service_id, $current_crawl_data) {
		try {
			DB::getInstance()->exec("INSERT INTO olsr_crawl_data (crawl_id, olsrd_hna, olsrd_neighbors, olsrd_links, olsrd_mid, olsrd_routes, olsrd_topology)
						VALUES ('$crawl_id', '$current_crawl_data[olsrd_hna]', '$current_crawl_data[olsrd_neighbors]', '$current_crawl_data[olsrd_links]', '$current_crawl_data[olsrd_mid]', '$current_crawl_data[olsrd_routes]', '$current_crawl_data[olsrd_topology]');");
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return true;
	}
	
	public function getOlsrCrawlDataByCrawlId($crawl_id) {
		try {
			$sql = "SELECT * 
				FROM olsr_crawl_data
				WHERE crawl_id='$crawl_id'";
			$result = DB::getInstance()->query($sql);
			$data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $data;
	}

	public function getCurrentlyEstablishedOLSRConnections($crawl_id) {
		$olsrd_data = Olsr::getOlsrCrawlDataByCrawlId($crawl_id);
		$olsrd_links = unserialize($olsrd_data['olsrd_links']);
		
		if(is_array($olsrd_links)) {
		$first = array();
		foreach($olsrd_links as $key=>$olsrd_link) {
			$first[$key] = $olsrd_link['Cost'];
		}
		array_multisort($first, SORT_ASC, $olsrd_links);
		} else {
			$olsrd_links = array();
		}
		return $olsrd_links;
	}

	


}
?>