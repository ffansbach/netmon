<?php

require_once('./lib/classes/core/subnetcalculator.class.php');

class Project  {
	public function getProjects() {
		try {
			$sql = "SELECT  *
					FROM projects";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$projects[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $projects;
	}

	public function getProjectList() {
		try {
			$sql = "SELECT  projects.id as project_id, projects.user_id, projects.create_date, projects.title, projects.description, projects.is_wlan, projects.wlan_essid, projects.wlan_bssid, projects.wlan_channel, projects.is_vpn, projects.vpn_server, projects.vpn_server_port, projects.vpn_server_device, projects.vpn_server_proto, projects.vpn_server_ca_crt, projects.vpn_server_ca_key, projects.vpn_server_pass, projects.vpn_client_config, projects.vpn_client_config_needs_script, projects.vpn_client_config_script, projects.is_ccd_ftp_sync, projects.ccd_ftp_folder, projects.ccd_ftp_username, projects.ccd_ftp_password, projects.is_olsr, projects.is_batman_adv, projects.is_ipv4, projects.ipv4_host, projects.ipv4_netmask, projects.ipv4_dhcp_kind, projects.is_ipv6,  projects.is_geo_specific, projects.geo_polygons, projects.dns_server, projects.website,
					users.id as user_id, users.nickname
					FROM projects
					LEFT JOIN users on (users.id=projects.user_id)";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$projects[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $projects;
	}

	public function getProjectData($project_id) {
		try {
			$sql = "SELECT projects.id as project_id, projects.user_id, projects.create_date, projects.title, projects.description, projects.is_wlan, projects.wlan_essid, projects.wlan_bssid, projects.wlan_channel, projects.is_vpn, projects.vpn_server, projects.vpn_server_port, projects.vpn_server_device, projects.vpn_server_proto, projects.vpn_server_ca_crt, projects.vpn_server_ca_key, projects.vpn_server_pass, projects.vpn_client_config, projects.vpn_client_config_needs_script, projects.vpn_client_config_script, projects.is_ccd_ftp_sync, projects.ccd_ftp_folder, projects.ccd_ftp_username, projects.ccd_ftp_password, projects.is_olsr, projects.is_batman_adv, projects.is_ipv4, projects.ipv4_host, projects.ipv4_netmask, projects.ipv4_dhcp_kind, projects.is_ipv6, projects.is_geo_specific, projects.geo_polygons, projects.dns_server, projects.website,
				       users.id as user_id, users.nickname
				       FROM projects
				       LEFT JOIN users on (users.id=projects.user_id)
				       WHERE projects.id='$project_id'";
			$result = DB::getInstance()->query($sql);
			$project = $result->fetch(PDO::FETCH_ASSOC);
			if($project['is_ipv4']=='1') {
				$project['ipv4_netmask_dot'] = SubnetCalculator::getNmask($project['ipv4_netmask']);
				$project['ipv4_bcast'] = SubnetCalculator::getDqBcast($project['ipv4_host'], $project['ipv4_netmask']);
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $project;
	}
}

?>