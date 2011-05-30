<?php

class BatmanAdvanced {
	public function getCrawlBatmanAdvOriginatorsByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_batman_advanced_originators
					WHERE router_id='$router_id' AND crawl_cycle_id='$crawl_cycle_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $key=>$row) {
				$originators[$key] = $row;
				$originators[$key]['originator_file_path'] = str_replace(":","_",$row['originator']);
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $originators;
	}

	public function getCrawlBatmanAdvancedHistoryExceptActualCrawlCycle($router_id, $actual_crawl_cycle_id, $limit) {
		try {
			$sql = "SELECT  *
					FROM crawl_batman_advanced_originators
					WHERE router_id='$router_id' AND crawl_cycle_id!='$actual_crawl_cycle_id'
					ORDER BY id desc
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

	public function getBatmanAdvancedInterfacesByRouterId($router_id) {
		$interfaces = array();
		try {
			$sql = "SELECT  interfaces.id as interface_id, interfaces.router_id, interfaces.project_id, interfaces.create_date, interfaces.name, interfaces.mac_addr, interfaces.ipv4_addr, interfaces.ipv6_addr,
					projects.title, projects.is_wlan, projects.wlan_essid, projects.wlan_bssid, projects.wlan_channel, projects.is_vpn, projects.vpn_server, projects.vpn_server_port, projects.vpn_server_device, projects.vpn_server_proto, projects.vpn_server_ca_crt, projects.vpn_server_ca_key, projects.vpn_server_pass, projects.is_ccd_ftp_sync, projects.ccd_ftp_folder, projects.ccd_ftp_username, projects.ccd_ftp_password, projects.is_olsr, projects.is_batman_adv, projects.is_ipv4, projects.ipv4_host, projects.ipv4_netmask, projects.ipv4_dhcp_kind
					FROM interfaces
					LEFT JOIN projects on (projects.id=interfaces.project_id)
				WHERE interfaces.router_id='$router_id' AND projects.is_batman_adv='1'
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

	public function getCrawlBatmanAdvInterfacesByCrawlCycleId($crawl_cycle_id, $router_id) {
		try {
			$sql = "SELECT  *
					FROM crawl_batman_advanced_interfaces
					WHERE router_id='$router_id' AND crawl_cycle_id='$crawl_cycle_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$interfaces[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $interfaces;
	}
}

?>