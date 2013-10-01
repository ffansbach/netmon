<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Ip.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');
	
	$ip = new Ip((int)$_GET['ip_id']);
	if($ip->fetch()) {
		$return = array();
		if($ip->getNetwork()->getIpv()==6)
			$command = "curl -s --max-time 10 -g http://[".$ip->getIp()."%25\$(cat /sys/class/net/".ConfigLine::configByName("network_connection_ipv6_interface")."/ifindex)]/node.data";
		elseif($ip->getNetwork()->getIpv()==4)
			$command = "curl -s --max-time 10 -g http://".$ip->getIp()."/node.data";
		exec($command, $return);
		$return_string = "";
		foreach($return as $string) {
			$return_string .= $string;
		}
		$smarty->assign('crawl_data', htmlentities($return_string));
	}
	
	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.html");
	$smarty->display("show_crawl_data.tpl.html");
	$smarty->display("footer.tpl.html");
?>