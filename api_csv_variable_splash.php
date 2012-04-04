<?php

require_once('runtime.php');
require_once('lib/classes/core/variablesplash.class.php');

if($_GET['section']=="insert_client") {
	$entry = array();
	$entry = VariableSplash::getClientByMac($_GET['mac_addr']);

	if(empty($entry)) {
		$lines = file('http://netmon.freifunk-ol.de/dhcp/leases');
		foreach($lines as $line) {
			if(strpos($line, $_GET['mac_addr'])!==false) {
				$line = explode(" ", $line);
				if(!empty($line[2])) {
					$ip = $line[2];
					$ipv = 4;
				}
			}
		}

		try {
			DB::getInstance()->exec("INSERT INTO variable_splash_clients (router_id, mac_addr, ip, ipv, create_date)
						 VALUES ('$_GET[router_id]', '$_GET[mac_addr]', '$ip', '$ipv', NOW());");
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		$id = DB::getInstance()->lastInsertId();
		echo "new_client_created,$id";
	} else {
		echo "client_already_exists,$entry[id]";
	}
}

?>