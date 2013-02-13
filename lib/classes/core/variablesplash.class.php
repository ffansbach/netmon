<?php

class VariableSplash {
	public function getClientByMac($mac_addr) {
		try {
			$sql = "SELECT  *
					FROM variable_splash_clients
					WHERE mac_addr='$mac_addr'";
			$result = DB::getInstance()->query($sql);
			$line = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		return $line;
	}

	public function getMacByIp($ip) {
		$lines = file('http://netmon.freifunk-ol.de/dhcp/leases');
		foreach($lines as $line) {
			if(strpos($line, $ip)!==false) {
				$line = explode(" ", $line);
				if(!empty($line[1])) {
					$ip = $line[1];
				}
			}
		}
		return $ip;
	}
}

?>