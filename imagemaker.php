<?php
require_once('./config/runtime.inc.php');
require_once('lib/classes/extern/jsonRPCClient.php');
require_once('./lib/classes/extern/archive.class.php');

require_once($path.'lib/classes/core/subnetcalculator.class.php');
if (!$_GET['section']) {
	$smarty->assign('images', Helper::getImages());
	$smarty->assign('memory_limit', (ini_get('memory_limit')+0));
	$smarty->assign('post_max_size', (ini_get('post_max_size')+0));
	$smarty->assign('upload_max_filesize', (ini_get('upload_max_filesize')+0));

	$smarty->assign('user_ips', Helper::getIpsByUserId($_SESSION['user_id']));

	$smarty->display("header.tpl.php");
	$smarty->display("imagemaker.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif ($_GET['section'] == 'upload_image') {
	$tmp_name = time();

	move_uploaded_file($_FILES['file']['tmp_name'], "tmp/".$tmp_name."_".$_FILES['file']['name']);

	DB::getInstance()->exec("INSERT INTO imagemaker_images (user_id, title, description, create_date)
							VALUES ('$_SESSION[user_id]', '$_POST[title]', '$_POST[description]', NOW());");
	$image_id = DB::getInstance()->lastInsertId();

	mkdir("scripts/imagemaker/images/$image_id", 0777);

	$command = "tar -C ".__DIR__."/scripts/imagemaker/images/$image_id/ -xvzf ".__DIR__."/tmp/$tmp_name"."_".$_FILES['file']['name'];
	exec($command);

	unlink("tmp/".$tmp_name."_".$_FILES['file']['name']);

} elseif ($_GET['section'] == 'upload_config') {
	DB::getInstance()->exec("INSERT INTO imagemaker_configs (image_id, user_id, title, description, create_date)
							VALUES ('$_POST[image_id]', '$_SESSION[user_id]', '$_POST[title]', '$_POST[description]', NOW());");
	$config_id = DB::getInstance()->lastInsertId();

	$tmp_name = time();
	move_uploaded_file($_FILES['file']['tmp_name'], "scripts/imagemaker/configurations/$config_id");

} elseif ($_GET['section'] == "new") {
	$netmon_url = "http://$GLOBALS[url_to_netmon]/";

	$api_main = new jsonRPCClient($netmon_url."api_main.php");
	try {
		$images = $api_main->getImages();
	} catch (Exception $e) {
		echo nl2br($e->getMessage());
	}

	$smarty->assign('images', $images);

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
		$vpn_ip_data = Helper::getIpDataByIpId($_POST['vpn_ip_id']);

		$build_time = time();
		$build_dir = "imagemaker_$_SESSION[user_id]_$build_time";

		mkdir("tmp/$build_dir", 0777);
		mkdir("tmp/$build_dir/preimage", 0777);

		exec("cp -al ".__DIR__."/scripts/imagemaker/images/$_POST[image_id]/* ".__DIR__."/tmp/$build_dir/preimage/");
		
		exec("chmod +x ".__DIR__."/scripts/imagemaker/configurations/$_POST[config_id]");
		exec(__DIR__."/scripts/imagemaker/configurations/$_POST[config_id] '10.18.1.1' '255.255.255.0' '5' '3' 'Hamelmannstraße Ecke Winkelmannstraße auf dem Dachboden' '8.1906080245981' '53.147619716638' 'oldenburg.freifunk.net' '02:CA:FF:EE:BA:BE' '6' 'Floh1111' 'Clemens John' 'clemens-john@gmx.de' '10.18' 'Freifunk Oldenburg' 'http://freifunk-ol.de' 'false' '' '' '' '' '' '' '' '10.18.0.1 10.18.0.254' '".__DIR__."/tmp/$build_dir/preimage/'");

		$last_line = exec(__DIR__."/scripts/imagemaker/mkimg '".__DIR__."/scripts/imagemaker/bin' '".__DIR__."/tmp/$build_dir'", $retval);

		exec("cp -al ".__DIR__."/scripts/imagemaker/src/openwrt-atheros-vmlinux.lzma ".__DIR__."/tmp/$build_dir");

		$smarty->assign('build_command', $build_command);
		$smarty->assign('build_prozess_return', $retval);

		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$smarty->assign('imagepath', $build_dir);		
		
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
        'basedir' => "./tmp/$_GET[imagepath]/preimage/etc/config/",
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