<?php
	require_once(ROOT_DIR.'/lib/classes/core/Ip.class.php');
	
	class Iplist {
		private $iplist = array();
		
		public function __construct($interface_id=false) {
			$result = array();
			if($interface_id!=false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT ips.id as ip_id
														FROM ips, interface_ips
														WHERE interface_ips.interface_id=? AND interface_ips.ip_id=ips.id");
					$stmt->execute(array($interface_id));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				try {
					$stmt = DB::getInstance()->prepare("SELECT ips.id as ip_id
														FROM ips");
					$stmt->execute(array());
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			foreach($result as $ip) {
				$this->iplist[] = new Ip((int)$ip['ip_id']);
			}
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('iplist');
			foreach($this->iplist as $ip) {
				$domxmlelement->appendChild($ip->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>