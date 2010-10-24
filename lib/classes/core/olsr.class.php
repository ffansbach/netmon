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

//NEW
	public function getOlsrInterfacesByRouterId($router_id) {
		$interfaces = array();
		try {
			$sql = "SELECT  interfaces.id as interface_id, interfaces.router_id, interfaces.project_id, interfaces.create_date, interfaces.name, interfaces.mac_addr, interfaces.ipv4_addr, interfaces.ipv6_addr,
					projects.title, projects.is_wlan, projects.wlan_essid, projects.wlan_bssid, projects.wlan_channel, projects.is_vpn, projects.vpn_server, projects.vpn_server_port, projects.vpn_server_device, projects.vpn_server_proto, projects.vpn_server_ca_crt, projects.vpn_server_ca_key, projects.vpn_server_pass, projects.is_ccd_ftp_sync, projects.ccd_ftp_folder, projects.ccd_ftp_username, projects.ccd_ftp_password, projects.is_olsr, projects.is_batman_adv, projects.is_ipv4, projects.ipv4_host, projects.ipv4_netmask, projects.ipv4_dhcp_kind
					FROM interfaces
					LEFT JOIN projects on (projects.id=interfaces.project_id)
				WHERE interfaces.router_id='$router_id' AND projects.is_olsr='1'
				ORDER BY interfaces.name asc";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				if($row['ipv']=='ipv4') {
					$row['ipv4_netmask_dot'] = SubnetCalculator::getNmask($row['ipv4_netmask']);
					$row['ipv4_bcast'] = SubnetCalculator::getDqBcast($GLOBALS['net_prefix'].".".$row['ipv4_host'], $row['ipv4_netmask']);
				 }
				$interfaces[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $interfaces;
	}

	public function getCrawlOlsrDataByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_olsr
					WHERE router_id='$router_id' AND crawl_cycle_id='$crawl_cycle_id'";
			$result = DB::getInstance()->query($sql);
			$olsr_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $olsr_data;
	}

	public function getCrawlOlsrHistoryExceptActualCrawlCycle($router_id, $actual_crawl_cycle_id, $limit) {
		try {
			$sql = "SELECT  *
					FROM crawl_olsr
					WHERE router_id='$router_id' AND crawl_cycle_id!='$actual_crawl_cycle_id'
					ORDER BY crawl_date desc
					LIMIT $limit";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$history[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $history;
	}
}
?>