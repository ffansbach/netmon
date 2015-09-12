<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Ip.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');
	
	$ip = new Ip((int)$_GET['ip_id']);
	if($ip->fetch()) {
		if ($ip->getNetwork()->getIpv()==6) {
			$command = "ping6 -c 4 -I ".ConfigLine::configByName("network_connection_ipv6_interface")." ".$ip->getIp();
		} elseif($ip->getNetwork()->getIpv()==4) {
			$command = "ping -c 4 ".$ip->getIp();
		}
	} else {
		echo "Could not fetch Info.";
	}
	
    if (isset($command)) {
        $return = array();
        echo "Command: ".$command."<br><br>";
        exec($command, $return);
        echo "Result:<pre>";
            print_r($return);
        echo "</pre>";
    }
?>
