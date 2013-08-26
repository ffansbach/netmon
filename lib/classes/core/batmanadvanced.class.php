<?php

class BatmanAdvanced {
	public function getCrawlBatmanAdvOriginatorsByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
							    FROM crawl_batman_advanced_originators
							    WHERE router_id=? AND crawl_cycle_id=?
							    ORDER BY link_quality ASC");
			$stmt->execute(array($router_id, $crawl_cycle_id));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		foreach($rows as $key=>$row)
			$rows[$key]['originator_file_path'] = str_replace(":","_",$row['originator']);
			
		return $rows;
	}
	
	public function getCrawlBatmanAdvNexthopsByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
							    FROM crawl_batman_advanced_originators
							    WHERE router_id=? AND crawl_cycle_id=?
							    GROUP BY nexthop");
			$stmt->execute(array($router_id, $crawl_cycle_id));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		foreach($rows as $key=>$row)
			$rows[$key]['originator_file_path'] = str_replace(":","_",$row['originator']);
			
		return $rows;
	}
	
	public function getCrawlBatmanAdvInterfacesByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  *
							    FROM crawl_batman_advanced_interfaces
							    WHERE router_id=? AND crawl_cycle_id=?");
			$stmt->execute(array($router_id, $crawl_cycle_id));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $rows;
	}
}

?>