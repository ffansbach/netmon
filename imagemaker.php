<?php
  require_once('./config/runtime.inc.php');
  
    if ($_GET['section'] == "new") {
		$ip_data = Helper::getIpInfo($_GET['ip_id']);
		$user_data = Helper::getUserByID($ip_data['user_id']);


		$exploded_dhcp_start = explode(".", $ip_data['zone_start']);
		$exploded_dhcp_end = explode(".", $ip_data['zone_end']);
		$dhcp_limit = $exploded_dhcp_end[1]-$exploded_dhcp_start[1];
		$time = time();
		$build_command = "cd $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/ && $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/mkall 'fon' '$GLOBALS[net_prefix].$ip_data[ip]' '$exploded_dhcp_start[1]' '$dhcp_limit' '$user_data[nickname]' '$user_data[vorname] $user_data[nachname]' '$user_data[email]' '$ip_data[ip_id]_$time'";

		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$smarty->assign('routertyp', "fon");
		$smarty->assign('ip_data', $ip_data);
		$smarty->assign('user_data', $user_data);
		$smarty->assign('dhcp_start', $exploded_dhcp_start[1]);
		$smarty->assign('dhcp_limit', $dhcp_limit);
		$smarty->assign('build_command', $build_command);

		$smarty->display("header.tpl.php");
		$smarty->display("image_new.tpl.php");
		$smarty->display("footer.tpl.php");
} elseif($_GET['section'] == "generate") {
		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$ip_data = Helper::getIpInfo($_GET['ip_id']);
		$smarty->assign('ip_data', $ip_data);

		$time = time();
		$smarty->assign('time', $time);
		
		$build_command = "cd $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/ && $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/mkall 'fon' '$_POST[ip]' '$_POST[dhcp_start]' '$_POST[dhcp_limit]' '$_POST[nickname]' '$_POST[realname]' '$_POST[email]' '$ip_data[ip_id]_$time'";
		
		$last_line = exec($build_command, $retval);
		
		$smarty->assign('build_command', $build_command);
		$smarty->assign('build_prozess_return', $retval);
		
		$smarty->display("header.tpl.php");
		$smarty->display("image_generate.tpl.php");
		$smarty->display("footer.tpl.php");
}

?>