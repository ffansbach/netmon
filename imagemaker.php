<?php
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/imagemaker.class.php');
require_once('lib/classes/extern/jsonRPCClient.php');
require_once('./lib/classes/extern/archive.class.php');
require_once('./lib/classes/core/subnetcalculator.class.php');

/** Get and assign global messages **/
$smarty->assign('message', Message::getMessage());

if (!$_GET['section']) {
	$smarty->assign('images', Imagemaker::getImages());
	$smarty->assign('memory_limit', (ini_get('memory_limit')+0));
	$smarty->assign('post_max_size', (ini_get('post_max_size')+0));
	$smarty->assign('upload_max_filesize', (ini_get('upload_max_filesize')+0));

	$smarty->assign('user_ips', Helper::getIpsByUserId($_SESSION['user_id']));

	$smarty->display("header.tpl.php");
	$smarty->display("imagemaker.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif ($_GET['section'] == 'images_edit') {
	$smarty->assign('images', Imagemaker::getImages());

	$smarty->assign('memory_limit', (ini_get('memory_limit')+0));
	$smarty->assign('post_max_size', (ini_get('post_max_size')+0));
	$smarty->assign('upload_max_filesize', (ini_get('upload_max_filesize')+0));


	$smarty->display("header.tpl.php");
	$smarty->display("imagemaker_images_edit.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif ($_GET['section'] == 'image_delete') {
	Imagemaker::deleteImage($_GET['image_id']);

	header('Location: imagemaker.php?section=images_edit');
} elseif ($_GET['section'] == 'upload_image') {
	$tmp_name = time();

	move_uploaded_file($_FILES['file']['tmp_name'], "tmp/".$tmp_name."_".$_FILES['file']['name']);

	DB::getInstance()->exec("INSERT INTO imagemaker_images (user_id, title, description, create_date)
							VALUES ('$_SESSION[user_id]', '$_POST[title]', '$_POST[description]', NOW());");
	$image_id = DB::getInstance()->lastInsertId();

	mkdir("scripts/imagemaker/images/$image_id/", 0777);
//	mkdir("scripts/imagemaker/images/$image_id/image/", 0777);
	mkdir("scripts/imagemaker/images/$image_id/kernel/", 0777);

	move_uploaded_file($_FILES['file2']['tmp_name'], "scripts/imagemaker/images/$image_id/kernel/openwrt-vmlinux.lzma");

	$command = __DIR__."/scripts/imagemaker/bin/squashfs-tools/x86_64/v4/unsquashfs4 -d ".__DIR__."/scripts/imagemaker/images/$image_id/image ".__DIR__."/tmp/$tmp_name"."_".$_FILES['file']['name']; 
//	echo $command;
	$last_line = exec($command, $retval);

	$message[] = array("Der enpackungsprozess wurde mit folgender Ausgabe beendet:", 0);

	foreach($retval as $val) {
		$message[] = array($val, 0);
	}

	Message::setMessage($message);

	unlink("tmp/".$tmp_name."_".$_FILES['file']['name']);
	header('Location: imagemaker.php?section=images_edit');

} elseif ($_GET['section'] == 'configurations_edit') {
	$smarty->assign('images', Imagemaker::getImages());
	$smarty->assign('configs', Imagemaker::getImageConfigs());

	$smarty->assign('memory_limit', (ini_get('memory_limit')+0));
	$smarty->assign('post_max_size', (ini_get('post_max_size')+0));
	$smarty->assign('upload_max_filesize', (ini_get('upload_max_filesize')+0));


	$smarty->display("header.tpl.php");
	$smarty->display("imagemaker_configurations_edit.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif ($_GET['section'] == 'upload_config') {
	DB::getInstance()->exec("INSERT INTO imagemaker_configs (image_id, user_id, title, description, create_date)
							VALUES ('$_POST[image_id]', '$_SESSION[user_id]', '$_POST[title]', '$_POST[description]', NOW());");
	$config_id = DB::getInstance()->lastInsertId();

	$tmp_name = time();
	move_uploaded_file($_FILES['file']['tmp_name'], "scripts/imagemaker/configurations/$config_id");

	header('Location: imagemaker.php');
} elseif ($_GET['section'] == "images_download") {
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
	/*	$ip_data = $api_router_config->getIpDataByIpId($_GET['ip_id']);
		$subnet_data = $api_router_config->getSubnetById($ip_data['subnet_id']);
		$subnet_netmask = $api_router_config->getDqNetmaskByCdr($subnet_data['netmask']);
		$user_data = $api_router_config->getPlublicUserInfoByID($ip_data['user_id']);*/
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

	$smarty->assign('vpn_ips', Helper::getIpsByUserIDThatCanVPN($_SESSION['user_id']));
	$smarty->assign('configdata', $configdata);

	$smarty->display("header.tpl.php");
	$smarty->display("image_new.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif($_GET['section'] == "generate") {
		$vpn_ip_data = Helper::getIpDataByIpId($_POST['vpn_ip_id']);

		$build_time = time();
		$build_dir = "imagemaker_$_SESSION[user_id]_$build_time";

		mkdir("tmp/$build_dir", 0777);
		mkdir("tmp/$build_dir/preimage", 0777);

		exec("cp -al ".__DIR__."/scripts/imagemaker/images/$_POST[image_id]/image/* ".__DIR__."/tmp/$build_dir/preimage/");
		
		if($_POST['config_id']!='false') {
			exec("chmod +x ".__DIR__."/scripts/imagemaker/configurations/$_POST[config_id]");
			$command = __DIR__."/scripts/imagemaker/configurations/$_POST[config_id] ".__DIR__."/tmp/$build_dir/preimage/ '$_POST[ip]' '$_POST[subnetmask]' '$_POST[dhcp_start]' '$_POST[dhcp_limit]' '$_POST[location]' '$_POST[longitude]' '$_POST[latitude]' '$_POST[essid]' '$_POST[bssid]' '$_POST[channel]' '$_POST[nickname]' '$_POST[vorname] $_POST[nachname]' '$_POST[email]' '$_POST[prefix]' '$_POST[community_name]' '$_POST[community_website]' '$_POST[vpn_ip_id]' '$vpn_ip_data[vpn_server]' '$vpn_ip_data[vpn_server_port]' '$vpn_ip_data[vpn_server_device]' '$vpn_ip_data[vpn_server_proto]' '$vpn_ip_data[vpn_server_ca]' '$vpn_ip_data[vpn_client_cert]' '$vpn_ip_data[vpn_client_key]' '208.67.222.222  208.67.220.220'";
			$config_exec = exec($command);
		}
//		$last_line = exec(__DIR__."/scripts/imagemaker/mkimg '".__DIR__."/scripts/imagemaker/bin' '".__DIR__."/tmp/$build_dir'", $retval);
		//Make SquashFS
//		$build_command = __DIR__."/scripts/imagemaker/bin/squashfs-tools/x86_64/v4/mksquashfs4 ".__DIR__."/tmp/$build_dir/preimage/ ".__DIR__."/tmp/$build_dir/openwrt-root.squashfs -nopad -noappend -root-owned -comp lzma";
		$build_command = __DIR__."/scripts/imagemaker/mkimg ".__DIR__."/scripts/imagemaker/bin/squashfs-tools/x86_64/v4/mksquashfs4 ".__DIR__."/tmp/$build_dir '-nopad -noappend -root-owned -comp lzma'";
//echo $build_command;
		exec($build_command, $retval);
		exec("chmod 777 ".__DIR__."/tmp/$build_dir/openwrt-root.squashfs");

		exec("cp -al ".__DIR__."/scripts/imagemaker/images/$_POST[image_id]/kernel/openwrt-vmlinux.lzma ".__DIR__."/tmp/$build_dir");

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