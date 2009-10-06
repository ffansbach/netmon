<?php
class History {
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
			$sql = "SELECT ips.id as object_id, ips.ip_ip as object_name_2, ips.create_date,
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
				      ips.user_id, ips.ip_ip, ips.id as ip_id, ips.subnet_id,
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


	public function getNetworkHistory($countlimit, $hourlimit) {
		if($countlimit)
			$range = "ORDER BY history.create_date desc
					  LIMIT 0, $limit";
		elseif ($hourlimit)
			$range = "WHERE history.create_date>=NOW() - INTERVAL $hourlimit HOUR
					  ORDER BY history.create_date desc";
		try {
			$sql = "SELECT id, type, create_date, data
			       FROM history
				   $range";
			$result = DB::getInstance()->query($sql);
			foreach($result as $key=>$row) {
				$history[$key] = $row;
				$history[$key]['data'] = unserialize($history[$key]['data']);
				$history[$key]['create_date'] = Helper::makeSmoothIplistTime(strtotime($history[$key]['create_date']));
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $history;
	}

	
}
?>