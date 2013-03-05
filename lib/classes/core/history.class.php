<?php
require_once($GLOBALS['monitor_root'].'lib/classes/core/router.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/crawling.class.php');

class History {
	public function makeRouterHistoryEntry($current_crawl_data, $router_id, $actual_crawl_cycle=array(), $last_endet_crawl_cycle=array()){
		if(empty($actual_crawl_cycle)) {
			$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		}
		if(empty($last_endet_crawl_cycle)) {
			$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		}

		$last_crawl_data = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $router_id);

		//erstelle online/offline Benachrichtigung aus aktuellem und letztem crawl Eintrag
		if (!empty($current_crawl_data['status']) AND $current_crawl_data['status']!=$last_crawl_data['status']) {
			$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'status', 'from'=>$last_crawl_data['status'], 'to'=>$current_crawl_data['status']));
		}
		
		//erstelle reboot Benachrichtigung aus aktuellem und letztem crawl Eintrag
		if($current_crawl_data['status']=='online' AND $last_crawl_data['status']=='online') {
			if ((!empty($current_crawl_data['uptime']) AND !empty($last_crawl_data['uptime'])) AND ($current_crawl_data['uptime']<$last_crawl_data['uptime'])) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'reboot'));
			}
		}

		if($last_crawl_data['status']!="online") {
			$last_online_crawl_data = Router::getLastOnlineCrawlByRouterId($router_id);
		} else {
			$last_online_crawl_data = $last_crawl_data;
		}

		if($current_crawl_data['status']=='online') {
			if (!empty($current_crawl_data['luciname']) AND !empty($last_online_crawl_data['luciname']) AND $current_crawl_data['luciname']!=$last_online_crawl_data['luciname']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'luciname', 'from'=>$last_crawl_data['luciname'], 'to'=>$current_crawl_data['luciname']));
			}
			if (!empty($current_crawl_data['luciversion']) AND !empty($last_online_crawl_data['luciversion']) AND $current_crawl_data['luciversion']!=$last_online_crawl_data['luciversion']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'luciversion', 'from'=>$last_crawl_data['luciversion'], 'to'=>$current_crawl_data['luciversion']));
			}
			if (!empty($current_crawl_data['distname']) AND !empty($last_online_crawl_data['distname']) AND $current_crawl_data['distname']!=$last_online_crawl_data['distname']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'distname', 'from'=>$last_crawl_data['distname'], 'to'=>$current_crawl_data['distname']));
			}
			if (!empty($current_crawl_data['distversion']) AND !empty($last_online_crawl_data['distversion']) AND $current_crawl_data['distversion']!=$last_online_crawl_data['distversion']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'distversion', 'from'=>$last_crawl_data['distversion'], 'to'=>$current_crawl_data['distversion']));
			}
			if (!empty($current_crawl_data['batman_advanced_version']) AND !empty($last_online_crawl_data['batman_advanced_version']) AND $current_crawl_data['batman_advanced_version']!=$last_online_crawl_data['batman_advanced_version']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'batman_advanced_version', 'from'=>$last_crawl_data['batman_advanced_version'], 'to'=>$current_crawl_data['batman_advanced_version']));
			}
			if (!empty($current_crawl_data['firmware_version']) AND !empty($last_online_crawl_data['firmware_version']) AND $current_crawl_data['firmware_version']!=$last_online_crawl_data['firmware_version']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'firmware_version', 'from'=>$last_crawl_data['firmware_version'], 'to'=>$current_crawl_data['firmware_version']));
			}
			if (!empty($current_crawl_data['nodewatcher_version']) AND !empty($last_online_crawl_data['nodewatcher_version']) AND $current_crawl_data['nodewatcher_version']!=$last_online_crawl_data['nodewatcher_version']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'nodewatcher_version', 'from'=>$last_crawl_data['nodewatcher_version'], 'to'=>$current_crawl_data['nodewatcher_version']));
			}
			if (!empty($current_crawl_data['hostname']) AND !empty($last_online_crawl_data['hostname']) AND $current_crawl_data['hostname']!=$last_online_crawl_data['hostname']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'hostname', 'from'=>$last_crawl_data['hostname'], 'to'=>$current_crawl_data['hostname']));
			}
			if (!empty($current_crawl_data['chipset']) AND !empty($last_online_crawl_data['chipset']) AND $current_crawl_data['chipset']!=$last_online_crawl_data['chipset']) {
				$history_data[] = serialize(array('router_id'=>$router_id, 'action'=>'chipset', 'from'=>$last_crawl_data['chipset'], 'to'=>$current_crawl_data['chipset']));
			}
		}

		if (isset($history_data) AND is_array($history_data)) {
			foreach ($history_data as $hist_data) {
				History::addHistoryEntry('router', $router_id, $hist_data);
			}
		}
	}

	public function addHistoryEntry($object, $object_id, $data) {
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

	public function getLastRegisteredUsers($limit, $daylimit) {
		if($limit)
			$range = "ORDER BY create_date desc
					  LIMIT 0, $limit";
		elseif ($daylimit)
			$range = "WHERE users.create_date>=NOW() - INTERVAL $daylimit DAY
					  ORDER BY create_date desc";
		try {
			$sql = "SELECT users.id as object_id, users.nickname as object_name_1, users.create_date
					FROM users
					$range";
			$result = DB::getInstance()->query($sql);
			$users = array();
			foreach($result as $key=>$row) {
				$ident = md5(uniqid(rand(), true));
				$index = $row['create_date']."_".$ident;

				$users[$index] = $row;
				$users[$index]['type'] = "user";
				$users[$index]['create_date'] = Helper::makeSmoothIplistTime(strtotime($users[$index]['create_date']));
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $users;
	}

	public function getLastRegisteredIps($limit, $daylimit) {
		if($limit)
			$range = "ORDER BY ips.create_date desc
					  LIMIT 0, $limit";
		elseif ($daylimit)
			$range = "WHERE ips.create_date>=NOW() - INTERVAL $daylimit DAY
					  ORDER BY ips.create_date desc";
		try {
			$sql = "SELECT ips.id as object_id, ips.ip as object_name_2, ips.create_date,
						   subnets.subnet_ip as object_name_1
					FROM ips
					LEFT JOIN subnets ON (subnets.id=ips.subnet_id)
					$range";
			$result = DB::getInstance()->query($sql);
			$ips = array();
			foreach($result as $key=>$row) {
				$ident = md5(uniqid(rand(), true));
				$index = $row['create_date']."_".$ident;

				$ips[$index] = $row;
				$ips[$index]['type'] = "ip";
				$ips[$index]['create_date'] = Helper::makeSmoothIplistTime(strtotime($ips[$index]['create_date']));
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ips;
	}

	public function getLastRegisteredSubnets($limit, $daylimit) {
		if($limit)
			$range = "ORDER BY subnets.create_date desc
					  LIMIT 0, $limit";
		elseif ($daylimit)
			$range = "WHERE subnets.create_date>=NOW() - INTERVAL $daylimit DAY
					  ORDER BY subnets.create_date desc";
		try {
			$sql = "SELECT subnets.id as object_id, subnets.title as object_name_1, subnets.create_date
					FROM subnets
					$range";
			$result = DB::getInstance()->query($sql);
			$subnets = array();
			foreach($result as $key=>$row) {
				$ident = md5(uniqid(rand(), true));
				$index = $row['create_date']."_".$ident;

				$subnets[$index] = $row;
				$subnets[$index]['type'] = "subnet";
				$subnets[$index]['create_date'] = Helper::makeSmoothIplistTime(strtotime($subnets[$index]['create_date']));
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $subnets;
	}

	public function getLastRegisteredServices($limit, $daylimit) {
		if($limit)
			$range = "ORDER BY services.create_date desc
					  LIMIT 0, $limit";
		elseif ($daylimit)
			$range = "WHERE services.create_date>=NOW() - INTERVAL $daylimit DAY
					  ORDER BY services.create_date desc";
		try {
			$sql = "SELECT services.id as service_id, services.title as services_title, services.typ, services.crawler, services.create_date,
				      ips.user_id, ips.ip, ips.id as ip_id, ips.subnet_id,
				      subnets.subnet_ip, subnets.title
			       FROM services
			       LEFT JOIN ips ON (ips.id = services.ip_id)
			       LEFT JOIN subnets ON (subnets.id = ips.subnet_id)
				   $range";
			$result = DB::getInstance()->query($sql);
			$services = array();
			foreach($result as $key=>$row) {
				$ident = md5(uniqid(rand(), true));
				$index = $row['create_date']."_".$ident;

				$services[$index] = $row;
				$services[$index]['type'] = "service";
				$services[$index]['create_date'] = Helper::makeSmoothIplistTime(strtotime($services[$index]['create_date']));
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $services;
	}


	public function getServiceHistory($countlimit, $hourlimit) {
		if($countlimit)
			$range = "
						WHERE object='service'
						ORDER BY history.create_date desc
					  LIMIT 0, $limit";
		elseif ($hourlimit)
			$range = "WHERE history.create_date>=NOW() - INTERVAL $hourlimit HOUR AND object='service'
					  ORDER BY history.create_date desc";
		try {
			$sql = "SELECT id, object, object_id, create_date, data
			       FROM history
				   $range";
			$result = DB::getInstance()->query($sql);
			foreach($result as $key=>$row) {
				$history[$key] = $row;
				$history[$key]['data'] = unserialize($history[$key]['data']);
				$history[$key]['additional_data'] = Helper::getServiceDataByServiceId($row['object_id']);
				$history[$key]['create_date'] = Helper::makeSmoothIplistTime(strtotime($history[$key]['create_date']));
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $history;
	}
	
	public function getServiceHistoryByUser($countlimit, $hourlimit, $user_id) {
		if($countlimit)
			$range = "
						WHERE history.object='service' AND ips.user_id=$user_id AND services.ip_id=ips.id AND services.id=history.object_id
						ORDER BY history.create_date desc
					  LIMIT 0, $limit";
		elseif ($hourlimit)
			$range = "WHERE history.create_date>=NOW() - INTERVAL $hourlimit HOUR AND history.object='service'  AND ips.user_id=$user_id AND services.ip_id=ips.id AND services.id=history.object_id
					  ORDER BY history.create_date desc";
		try {
			$sql = "SELECT history.id, history.object, history.object_id, history.create_date, history.data
			       FROM history, services, ips
				   $range";
			$result = DB::getInstance()->query($sql);
			foreach($result as $key=>$row) {
				$history[$key] = $row;
				$history[$key]['data'] = unserialize($history[$key]['data']);
				$history[$key]['additional_data'] = Helper::getServiceDataByServiceId($row['object_id']);
				$history[$key]['create_date'] = Helper::makeSmoothIplistTime(strtotime($history[$key]['create_date']));
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $history;
	}

	public function getRouterHistoryByRouterId($router_id, $countlimit, $hourlimit) {
		if($countlimit)
			$range = "
						WHERE object='router' AND object_id=$router_id
						ORDER BY history.create_date desc
					  LIMIT 0, $countlimit";
		elseif ($hourlimit)
			$range = "WHERE history.create_date>=NOW() - INTERVAL $hourlimit HOUR AND object='service'
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

	public function getRouterHistoryByRouterIdExceptActualCrawlCycle($router_id, $actual_crawl_cycle_id, $countlimit, $hourlimit) {
		if($countlimit)
			$range = "
						WHERE object='router' AND object_id=$router_id AND crawl_cycle_id<'$actual_crawl_cycle_id'
						ORDER BY history.create_date desc
					  LIMIT 0, $countlimit";
		elseif ($hourlimit)
			$range = "WHERE history.create_date>=NOW() - INTERVAL $hourlimit HOUR AND object='service' AND crawl_cycle_id<'$actual_crawl_cycle_id'
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

	public function getUserHistory($user_id, $countlimit) {
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		$history_cp = array();

		try {
			$sql = "SELECT  *
					FROM routers
					WHERE user_id='$user_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		if(!empty($routers)) {
		foreach($routers as $router) {
			try {
				$sql = "SELECT id, object, object_id, create_date, data
					FROM history
					WHERE object='router' AND object_id=$router[id] AND crawl_cycle_id<'$actual_crawl_cycle[id]'
					ORDER BY history.create_date desc
					LIMIT 0, $countlimit";
					
				$result = DB::getInstance()->query($sql);
				foreach($result as $key=>$row) {
					$history[$key] = $row;
					$history[$key]['data'] = unserialize($history[$key]['data']);
					$history[$key]['additional_data'] = Router::getRouterInfo($row['object_id']);
					$user_history[] = $history[$key];
				}
			}
			catch(PDOException $e) {
				echo $e->getMessage();
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

	public function getHistory($countlimit, $hourlimit) {
		$history = array();
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		if(isset($actual_crawl_cycle['id']) AND (isset($countlimit) OR isset($hourlimit))) {
			if($countlimit)
				$range = "
						WHERE history.crawl_cycle_id!=$actual_crawl_cycle[id]
						ORDER BY history.create_date desc
						LIMIT 0, $countlimit";
			elseif ($hourlimit)
				$range = "WHERE history.create_date>=NOW() - INTERVAL $hourlimit HOUR AND history.crawl_cycle_id!=$actual_crawl_cycle[id]
						ORDER BY history.create_date desc";
			try {
				$sql = "SELECT id, object, object_id, create_date, data
					FROM history
					$range";
				$result = DB::getInstance()->query($sql);
				foreach($result as $key=>$row) {
					$history[$key] = $row;
					$history[$key]['data'] = unserialize($history[$key]['data']);
					if($history[$key]['object'] == "router") {
						$history[$key]['additional_data'] = Router::getRouterInfo($row['object_id']);
					}
				}
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			return $history;
		}
	}	
}
?>