<?php

class ProjectEditor {

	public function createNewProject() {
		$result = DB::getInstance()->exec("INSERT INTO projects (user_id, create_date, title, description, is_wlan, wlan_essid, wlan_bssid, wlan_channel, is_vpn, vpn_server, vpn_server_port, vpn_server_device, vpn_server_proto, vpn_server_ca_crt, vpn_server_ca_key, vpn_server_pass, is_ccd_ftp_sync, ccd_ftp_folder, ccd_ftp_username, ccd_ftp_password, is_olsr, is_batman_adv, ipv, ipv4_host, ipv4_netmask, ipv4_dhcp_kind, is_geo_specific, geo_polygons, dns_server, website)
										   VALUES ('$_SESSION[user_id]', NOW(), '$_POST[title]', '$_POST[description]', '$_POST[is_wlan]', '$_POST[wlan_essid]', '$_POST[wlan_bssid]', '$_POST[wlan_channel]', '$_POST[is_vpn]', '$_POST[vpn_server]', '$_POST[vpn_server_port]', '$_POST[vpn_server_device]', '$_POST[vpn_server_proto]', '$_POST[vpn_server_ca_crt]', '$_POST[vpn_server_ca_key]', '$_POST[vpn_server_pass]', '$_POST[is_ccd_ftp_sync]', '$_POST[ccd_ftp_folder]', '$_POST[ccd_ftp_username]', '$_POST[ccd_ftp_password]', '$_POST[is_olsr]', '$_POST[is_batman_adv]', '$_POST[ipv]', '$_POST[ipv4_host]', '$_POST[ipv4_netmask]', '$_POST[ipv4_dhcp_kind]', '$_POST[is_geo_specific]', '$_POST[geo_polygons]', '$_POST[dns_server]', '$_POST[website]');");
		$project_id = DB::getInstance()->lastInsertId();

		$message[] = array("Das Subnetz $_POST[title] wurde in die Datenbank eingetragen.", 1);
		Message::setMessage($message);
		return array("result"=>true, "project_id"=>$project_id);
	}
}