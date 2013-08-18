<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/classes/core/Ip.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/ConfigLine.class.php');
	
	$ip = new Ip((int)$_GET['ip_id']);
	if($ip->fetch()) {
		if ($ip->getNetwork()->getIpv()==6) {
			$address="[".$ip->getIp()."%".ConfigLine::configByName("network_connection_ipv6_interface")."]";
		} elseif($ip->getNetwork()->getIpv()==4) {
			$address = $ip->getIp();
		}
	} else {
		echo "Could not fetch Info.";
	}
	
	$return = array();
	$command = "wget_return=`busybox wget -q -O - http://$address/node.data & sleep 10; kill $!`
	echo \$wget_return";
	
	exec($command, $return);
	$return_string = "";
	foreach($return as $string) {
		$return_string .= $string;
	}
	
	if(!empty($string)) {
		echo $string;
	} else {
		echo $command;
	}
?>