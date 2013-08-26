<?php

class VariableSplash {
	public function getClientByMac($mac_addr) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM variable_splash_clients WHERE mac_addr=?");
			$stmt->execute(array($mac_addr));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		return $rows;
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