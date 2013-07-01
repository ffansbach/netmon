<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class Ip extends Object {
		private $ip_id = 0;
		private $interface_id = 0;
		private $ip = "";
		private $ipv = 0;
		private $netmask = 0;
		private $statusdata = array();
		private $statusdata_history = array();
		
		public function __construct($ip_id=false, $interface_id=false, $ip=false, $ipv=false, $netmask=false,
									$create_date=false, $update_date=false) {
				$this->setInterfaceId($interface_id);
				$this->setIp($ip);
				$this->setIpv($ipv);
				$this->setNetmask($netmask);
				$this->setCreateDate($create_date);
				$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM ips
													WHERE
														(id = :ip_id OR :ip_id=0) AND
														(interface_id = :interface_id OR :interface_id=0) AND
														(ip = :ip OR :ip='') AND
														(ipv = :ipv OR :ipv=0) AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':ip_id', $this->getIpId(), PDO::PARAM_INT);
				$stmt->bindParam(':interface_id', $this->getInterfaceId(), PDO::PARAM_INT);
				$stmt->bindParam(':ipv', $this->getIpv(), PDO::PARAM_INT);
				$stmt->bindParam(':ip', $this->getIp(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setIpId((int)$result['id']);
				$this->setInterfaceId((int)$result['interface_id']);
				$this->setIp($result['ip']);
				$this->setIpv((int)$result['ipv']);
				//TODO: $this->setNetmask((int)$result['netmask']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
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