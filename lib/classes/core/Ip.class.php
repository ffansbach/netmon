<?php
	require_once('../../lib/classes/core/Object.class.php');
	
	class Ip extends Object {
		private $ip_id = 0;
		private $interface_id = 0;
		private $ip = "";
		private $ipv = 0;
		private $netmask = 0;
		private $statusdata = array();
		private $statusdata_history = array();
		
		public function __construct($ip_id=false, $interface_id=false, $ip=false, $ipv=false, $netmask=false, $create_date=false) {
			if($interface_id != false AND $ip != false AND $ipv != false AND $netmask != false) {
				$this->setInterfaceId((int)$inteface_id);
				$this->setIp($ip);
				$this->setIpv((int)$ipv);
				$this->setNetmask((int)$netmask);
			} else if ($ip_id !== false AND is_int($ip_id)) {
				//fetch ip data from database
				$result = array();
				try {
					$stmt = DB::getInstance()->prepare("SELECT ips.id as ip_id, ips.ip, ips.ipv, ips.create_date, interface_ips.interface_id
														FROM ips, interface_ips
														WHERE ips.id = $ip_id AND interface_ips.ip_id=ips.id");
					$stmt->execute(array($ip_id));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}

				$this->setIpId((int)$result['ip_id']);
				$this->setInterfaceId((int)$result['interface_id']);
				$this->setIp($result['ip']);
				$this->setIpv((int)$result['ipv']);
				//TODO: $this->setNetmask((int)$result['netmask']);
				$this->setCreateDate($result['create_date']);
			}
		}
		
		public function setIpId($ip_id) {
			if(is_int($ip_id))
				$this->ip_id = $ip_id;
		}
		
		public function setInterfaceId($interface_id) {
			if(is_int($interface_id))
				$this->interface_id = $interface_id;
		}
		
		public function setIp($ip) {
			if(is_string($ip)) {
				$ip = explode("/", $ip);
				$this->ip = $ip[0];
				
			}
		}
		
		public function setIpv($ipv) {
			if(is_int($ipv))
				$this->ipv = $ipv;
		}
		
		public function setNetmask($netmask) {
			if(is_int($netmask))
				$this->netmask = $netmask;
		}
		
		public function getIpId() {
			return $this->ip_id;
		}
		
		public function getInterfaceId() {
			return $this->interface_id;
		}
		
		public function getIp() {
			return $this->ip;
		}
		
		public function getIpv() {
			return $this->ipv;
		}
		
		public function getNetmask() {
			return $this->netmask;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('ip');
			$domxmlelement->appendChild($domdocument->createElement("ip_id", $this->getIpId()));
			$domxmlelement->appendChild($domdocument->createElement("interface_id", $this->getInterfaceId()));
			$domxmlelement->appendChild($domdocument->createElement("ip", $this->getIp()));
			$domxmlelement->appendChild($domdocument->createElement("ipv", $this->getIpv()));
			$domxmlelement->appendChild($domdocument->createElement("netmask", $this->getNetmask()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			return $domxmlelement;
		}
	}
?>