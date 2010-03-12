<?php
require_once('./config/runtime.inc.php');
require_once('lib/classes/extern/jsonRPCClient.php');
require_once('./lib/classes/extern/archive.class.php');

require_once($path.'lib/classes/core/subnetcalculator.class.php');
if ($_GET['section'] == "new") {
	$netmon_url = "http://netmon.freifunk-ol.de/";
	$api_router_config = new jsonRPCClient($netmon_url."api_router_config.php");
	try {
		$ip_data = $api_router_config->getIpDataByIpId($_GET['ip_id']);
		$subnet_data = $api_router_config->getSubnetById($ip_data['subnet_id']);
		$subnet_netmask = $api_router_config->getDqNetmaskByCdr($subnet_data['netmask']);
		$user_data = $api_router_config->getPlublicUserInfoByID($ip_data['user_id']);
		$community_info = $api_router_config->getCommunityInfo();
	} catch (Exception $e) {
		echo nl2br($e->getMessage());
	}

	$configdata['chipset'] = $ip_data['chipset'];
	$configdata['ip'] = $GLOBALS['net_prefix'].".".$ip_data['ip'];
	$configdata['subnetmask'] = $subnet_netmask;

	$exploded_zone_start = explode(".", $ip_data['zone_start']);
	$exploded_zone_end = explode(".", $ip_data['zone_end']);
	$configdata['dhcp_start'] = $exploded_zone_start[1];
	$configdata['dhcp_limit'] = $exploded_zone_end[1]-$exploded_zone_start[1]-1;

	$configdata['location'] = $ip_data['location'];
	$configdata['longitude'] =$ip_data['longitude'];
	$configdata['latitude'] = $ip_data['latitude'];

	$configdata['essid'] = $subnet_data['essid'];
	$configdata['bssid'] = $subnet_data['bssid'];
	$configdata['channel'] = $subnet_data['channel'];

	$configdata['nickname'] = $user_data['nickname'];
	$configdata['vorname'] = $user_data['vorname'];
	$configdata['nachname'] = $user_data['nachname'];
	$configdata['email'] = $user_data['email'];

	$configdata['prefix'] = $community_info['net_prefix'];
	$configdata['community_name'] = $community_info['community_name'];
	$configdata['community_website'] = $community_info['community_website'];

	$time = time();	
	$configdata['imagepath'] = "$_GET[ip_id]_$time";

	$build_command = "cd $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/ && $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/mkall '$configdata[chipset]' '$configdata[ip]' '$configdata[subnetmask]' '$configdata[dhcp_start]' '$configdata[dhcp_limit]' '$configdata[location]' '$configdata[longitude]' '$configdata[latitude]' '$configdata[essid]' '$configdata[bssid]' '$configdata[channel]' '$configdata[nickname]' '$configdata[vorname] $configdata[nachname]' '$configdata[email]' '$configdata[prefix]' '$configdata[community_name]' '$configdata[community_website]' '$configdata[imagepath]'";
	$smarty->assign('vpn_ips', Helper::getIpsByUserIDThatCanVPN($_SESSION['user_id']));
	$smarty->assign('configdata', $configdata);
	$smarty->assign('build_command', $build_command);

	$smarty->display("header.tpl.php");
	$smarty->display("image_new.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif($_GET['section'] == "generate") {
		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$smarty->assign('imagepath', $_POST['imagepath']);

		$vpn_ip_data = Helper::getIpDataByIpId($_POST['vpn_ip_id']);

		$build_command = "cd $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/ && $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/mkall '$_POST[chipset]' '$_POST[ip]' '$_POST[subnetmask]' '$_POST[dhcp_start]' '$_POST[dhcp_limit]' '$_POST[location]' '$_POST[longitude]' '$_POST[latitude]' '$_POST[essid]' '$_POST[bssid]' '$_POST[channel]' '$_POST[nickname]' '$_POST[vorname] $_POST[nachname]' '$_POST[email]' '$_POST[prefix]' '$_POST[community_name]' '$_POST[community_website]' '$_POST[vpn_ip_id]' '$vpn_ip_data[vpn_server]' '$vpn_ip_data[vpn_server_port]' '$vpn_ip_data[vpn_server_device]' '$vpn_ip_data[vpn_server_proto]' '$vpn_ip_data[vpn_server_ca]' '$vpn_ip_data[vpn_client_cert]' '$vpn_ip_data[vpn_client_key]' '$_POST[imagepath]'";

		$last_line = exec($build_command, $retval);
		
		$smarty->assign('build_command', $build_command);
		$smarty->assign('build_prozess_return', $retval);
		
		$smarty->display("header.tpl.php");
		$smarty->display("image_generate.tpl.php");
		$smarty->display("footer.tpl.php");
}
 elseif($_GET['section'] == "download_config") {
      $ip_data = Helper::getIpDataByIpId($_GET['ip_id']);
      // Objekt erzeugen. Das Argument bezeichnet den Dateinamen
      $zipfile= new zip_file($GLOBALS['net_prefix'].".".$ip_data['ip']."_config.zip");

      // Die Optionen
      $zipfile->set_options(array (
        'basedir' => "./scripts/imgbuild/dest/$_GET[imagepath]/root-atheros/etc/config/",
        'followlinks' => 0, // (Symlinks)
        'inmemory' => 1, // Make the File in RAM
        'level' => 6, // Level 1 = fast, Level 9 = good
        'recurse' => 1, // Recursive
        'maxsize' => 12*1024*1024 // Zip only data that is <= 12 MB big becuse og php memory limit
      ));

      $zipfile->add_files(array("*"));

      // Make zip
      $zipfile->create_archive();

      // download zip
      $zipfile->download_file();
}

?>