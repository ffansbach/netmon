<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class Network extends Object {
		private $network_id = 0;
		private $user_id = 0;
		private $ip = "";
		private $netmask = 0;
		private $ipv = 0;
		
		public function __construct($network_id=false, $user_id=false, $ip=false, $netmask=false, $ipv=false,
									$create_date=false, $update_date=false) {
				$this->setNetworkId($network_id);
				$this->setUserId($user_id);
				$this->setIp($ip);
				$this->setNetmask($netmask);
				$this->setIpv($ipv);
				$this->setCreateDate($create_date);
				$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM networks
													WHERE
														(id = :network_id OR :network_id=0) AND
														(user_id = :user_id OR :user_id=0) AND
														(ip = :ip OR :ip='') AND
														(netmask = :netmask OR :netmask=0) AND
														(ipv = :ipv OR :ipv=0) AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':network_id', $this->getNetworkId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':ip', $this->getIp(), PDO::PARAM_STR);
				$stmt->bindParam(':netmask', $this->getNetmask(), PDO::PARAM_INT);
				$stmt->bindParam(':ipv', $this->getIpv(), PDO::PARAM_INT);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setNetworkId((int)$result['id']);
				$this->setUserId((int)$result['user_id']);
				$this->setIp($result['ip']);
				$this->setNetmask((int)$result['netmask']);
				$this->setIpv((int)$result['ipv']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			//TODO: check if the network exists or conflicts with another network
			
			if($this->getNetworkId() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE networks SET
																user_id = ?,
																ip = ?,
																netmask = ?,
																ipv = ?,
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getUserId(), $this->getIp(), $this->getNetmask(), $this->getIpv()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getUserId()!=0 AND $this->getIp()!="" AND $this->getIpv()!=0) {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO networks (user_id, ip, netmask, ipv, create_date, update_date)
														VALUES (?, ?, ?, ?, NOW(), NOW())");
					$stmt->execute(array($this->getUserId(), $this->getIp(), $this->getNetmask(), $this->getIpv()));
					return DB::getInstance()->lastInsertId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			return false;
		}
		
		public function delete() {
			if($this->getNetworkId() != 0) {
				try {
 					$stmt = DB::getInstance()->prepare("DELETE FROM networks WHERE id=?");
					$stmt->execute(array($this->getNetworkId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			return false;
		}
		
		public function setNetworkId($network_id) {
			if(is_int($network_id))
				$this->network_id = $network_id;
		}
		
		public function setUserId($user_id) {
			if(is_int($user_id))
				$this->user_id = $user_id;
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
		
		public function getNetworkId() {
			return $this->network_id;
		}
		
		public function getUserId() {
			return $this->user_id;
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
			$domxmlelement = $domdocument->createElement('network');
			$domxmlelement->appendChild($domdocument->createElement("network_id", $this->getNetworkId()));
			$domxmlelement->appendChild($domdocument->createElement("user_id", $this->getUserId()));
			$domxmlelement->appendChild($domdocument->createElement("ip", $this->getIp()));
			$domxmlelement->appendChild($domdocument->createElement("ipv", $this->getIpv()));
			$domxmlelement->appendChild($domdocument->createElement("netmask", $this->getNetmask()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			return $domxmlelement;
		}
	}
?>