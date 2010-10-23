<?php

require_once('lib/classes/core/crawling.class.php');

class Router {
	public function getRouterInfo($router_id) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_login_string, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude, routers.chipset_id,
					users.nickname, chipsets.name as chipset_name
					FROM routers
					LEFT JOIN users ON (users.id=routers.user_id)
					LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
					WHERE routers.id='$router_id'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouterByHostname($hostname) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude,
					users.nickname, chipsets.name as chipset_name
					FROM routers
					LEFT JOIN users ON (users.id=routers.user_id)
					LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
					WHERE routers.hostname='$hostname'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouterByAutoAssignLoginString($auto_assign_login_string) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.allow_router_auto_assign, routers.router_auto_assign_hash, routers.description, routers.location, routers.latitude, routers.longitude,
					users.nickname, chipsets.name as chipset_name
					FROM routers
					LEFT JOIN users ON (users.id=routers.user_id)
					LEFT JOIN chipsets ON (chipsets.id=routers.chipset_id)
					WHERE routers.router_auto_assign_login_string='$auto_assign_login_string'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getCrawlRouterByCrawlCycleId($crawl_cycle_id, $router_id) {
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
		return $crawl_data;
	}

	public function getCrawlRoutersByCrawlCycleIdAndStatus($crawl_cycle_id, $status) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE crawl_cycle_id='$crawl_cycle_id' AND status='$status'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getLastOnlineCrawlByRouterId($router_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE router_id='$router_id' AND status='online' ORDER BY id desc
					LIMIT 1";
			$result = DB::getInstance()->query($sql);
			$crawl_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $crawl_data;
	}



	public function getRouters() {
		try {
			$sql = "SELECT  *
					FROM routers";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getRouterList() {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.description, routers.location, routers.latitude, routers.longitude,
					chipsets.name as chipset_name,
					users.nickname

					FROM routers
					LEFT JOIN chipsets on (chipsets.id=routers.chipset_id)
					LEFT JOIN users on (users.id=routers.user_id)";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$text=$row['location'];
				$shorttext=$text;
				$length = 20;
				if(strlen($text)>$length){
					$shorttext = substr($text, 0, $length-1);
					$var= explode(" ",substr($text, $length, strlen($text)));
					$shorttext.= $var[0];
				} 
				$row['short_location'] = $shorttext;

				$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
				$row['actual_crawl_data'] = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $row['router_id']);
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getRouterListByUserId($user_id) {
		try {
			$sql = "SELECT  routers.id as router_id, routers.user_id, routers.create_date, routers.update_date, routers.crawl_method, routers.hostname, routers.description, routers.location, routers.latitude, routers.longitude,
					chipsets.name as chipset_name,
					users.nickname

					FROM routers
					LEFT JOIN chipsets on (chipsets.id=routers.chipset_id)
					LEFT JOIN users on (users.id=routers.user_id)
					WHERE routers.user_id='$user_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$text=$row['location'];
				$shorttext=$text;
				$length = 20;
				if(strlen($text)>$length){
					$shorttext = substr($text, 0, $length-1);
					$var= explode(" ",substr($text, $length, strlen($text)));
					$shorttext.= $var[0];
				} 
				$row['short_location'] = $shorttext;

				$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
				$row['actual_crawl_data'] = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $row['router_id']);
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getRoutersForCrawl() {
		try {
			$sql = "SELECT  *
					FROM routers
				WHERE crawl_method='crawler'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function getCrawlRouterHistoryExceptActualCrawlCycle($router_id, $actual_crawl_cycle_id, $limit) {
		try {
			$sql = "SELECT  *
					FROM crawl_routers
					WHERE router_id='$router_id' AND crawl_cycle_id!='$actual_crawl_cycle_id'
					ORDER BY crawl_date desc
					LIMIT $limit";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}

	public function countRoutersByCrawlCycleIdAndStatus($crawl_cycle_id, $status) {
		try {
			$sql = "SELECT  COUNT(*) as count
					FROM crawl_routers
					WHERE crawl_cycle_id='$crawl_cycle_id' AND status='$status'";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count['count'];
	}

	public function countRouters() {
		try {
			$sql = "SELECT  COUNT(*) as count
					FROM routers";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count['count'];
	}

	public function countRoutersByChipsetId($chipset_id) {
		try {
			$sql = "SELECT  COUNT(*) as count
					FROM routers
					WHERE chipset_id='$chipset_id'";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count['count'];
	}

	public function countRoutersByTime($timestamp) {
		try {
			$sql = "SELECT  COUNT(*) as count
					FROM routers
					WHERE create_date<=FROM_UNIXTIME($timestamp)";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $count['count'];
	}

	public function getRouterByMacAndCrawlCycleId($mac_addr, $crawl_cycle_id) {
		$interfaces = array();
		try {
			$sql = "SELECT  crawl_interfaces.router_id, crawl_interfaces.mac_addr,
					routers.hostname, routers.latitude, routers.longitude
					FROM crawl_interfaces
					LEFT JOIN routers on (routers.id=crawl_interfaces.router_id)
				WHERE crawl_interfaces.crawl_cycle_id='$crawl_cycle_id' AND crawl_interfaces.mac_addr='$mac_addr'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouterByIPv4Addr($ipv4_addr) {
		$exploded_addr = explode(".", $ipv4_addr);
		if(count($exploded_addr)>3) {
			$ipv4_addr = $exploded_addr[2].".".$exploded_addr[3];
		}
		$interfaces = array();
		try {
			$sql = "SELECT  interfaces.router_id, interfaces.ipv4_addr,
					routers.hostname, routers.latitude, routers.longitude
					FROM interfaces
					LEFT JOIN routers on (routers.id=interfaces.router_id)
				WHERE interfaces.ipv4_addr='$ipv4_addr'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}



}