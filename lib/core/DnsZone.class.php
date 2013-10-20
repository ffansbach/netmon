<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	
	class DnsZone extends Object {
		private $dns_zone_id = 0;
		private $user_id = 0;
		private $name = "";
		private $pri_dns = "";
		private $sec_dns = "";
		private $serial = 0;
		private $refresh = 0;
		private $retry = 0;
		private $expire = 0;
		private $ttl = 0;
		
		private $dns_ressource_record_list = null;
		private $user = null;
		
		public function __construct($dns_zone_id=false, $user_id=false, $name=false, $pri_dns=false,
									$sec_dns=false, $serial=false, $refresh=false, $retry=false,
									$expire=false, $ttl=false, $create_date=false, $update_date=false) {
			$this->setDnsZoneId($dns_zone_id);
			$this->setUserId($user_id);
			$this->setName($name);
			$this->setPriDns($pri_dns);
			$this->setSecDns($sec_dns);
			$this->setSerial($serial);
			$this->setRefresh($refresh);
			$this->setRetry($retry);
			$this->setExpire($expire);
			$this->setTtl($ttl);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM dns_zones
													WHERE
														(id = :dns_zone_id OR :dns_zone_id=0) AND
														(user_id = :user_id OR :user_id=0) AND
														(name = :name OR :name='') AND
														(pri_dns = :pri_dns OR :pri_dns='') AND
														(sec_dns = :sec_dns OR :sec_dns='') AND
														(serial = :serial OR :serial=0) AND
														(refresh = :refresh OR :refresh=0) AND
														(retry = :retry OR :retry=0) AND
														(expire = :expire OR :expire=0) AND
														(ttl = :ttl OR :ttl=0) AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':dns_zone_id', $this->getDnsZoneId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':name', $this->getName(), PDO::PARAM_STR);
				$stmt->bindParam(':pri_dns', $this->getPriDns(), PDO::PARAM_STR);
				$stmt->bindParam(':sec_dns', $this->getSecDns(), PDO::PARAM_STR);
				$stmt->bindParam(':serial', $this->getSerial(), PDO::PARAM_INT);
				$stmt->bindParam(':refresh', $this->getRefresh(), PDO::PARAM_INT);
				$stmt->bindParam(':retry', $this->getRetry(), PDO::PARAM_INT);
				$stmt->bindParam(':expire', $this->getExpire(), PDO::PARAM_INT);
				$stmt->bindParam(':ttl', $this->getTtl(), PDO::PARAM_INT);				
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setDnsZoneId((int)$result['id']);
				$this->setUserId((int)(int)$result['user_id']);
				$this->setName($result['name']);
				$this->setPriDns($result['pri_dns']);
				$this->setSecDns($result['sec_dns']);
				$this->setSerial((int)$result['serial']);
				$this->setRefresh((int)$result['refresh']);
				$this->setRetry((int)$result['retry']);
				$this->setExpire((int)$result['expire']);
				$this->setTtl((int)$result['ttl']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				
				$this->setUser($this->getUserId());
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->getDnsZoneId() != 0) {
				//update 
				//check if we need to update the serial number
				$tmp_dns_zone = new DnsZone($this->getDnsZoneId());
				$tmp_dns_zone->fetch();
				if($tmp_dns_zone->getSerial()==$this->getSerial()) {
					$tmd_serial_date = substr($tmp_dns_zone->getSerial(), 0, 8);
					$today_serial_date = date("Ymd", time());
					if($tmd_serial_date==$today_serial_date) {
						$serial_inc = str_pad(substr($this->getSerial, -2)+1, 2, "0", STR_PAD_LEFT);
					} else {
						$serial_inc = "00";
					}
					$this->setSerial(intval($today_serial_date.$serial_inc));
				}
				
				try {
					$stmt = DB::getInstance()->prepare("UPDATE dns_zones SET
																user_id = ?,
																name = ?,
																pri_dns = ?,
																sec_dns = ?,
																serial = ?,
																refresh = ?,
																retry = ?,
																expire = ?,
																ttl = ?,
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getUserId(), $this->getName(), $this->getPriDns(),
										 $this->getSecDns(), $this->getSerial(), $this->getRefresh(),
										 $this->getRetry(), $this->getExpire(), $this->getTtl(), $this->getDnsZoneId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getUserId() != 0 AND $this->getName()!="") {
				if($this->getSerial()==0) {
					$this->setSerial(intval($today_serial_date = date("Ymd", time())."00"));
				}
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO dns_zones (user_id, name, pri_dns, sec_dns, serial, refresh, retry, expire, ttl, create_date, update_date)
														VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
					$stmt->execute(array($this->getUserId(), $this->getName(), $this->getPriDns(),
										 $this->getSecDns(), $this->getSerial(), $this->getRefresh(),
										 $this->getRetry(), $this->getExpire(), $this->getTtl()));
					return DB::getInstance()->lastInsertId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			return false;
		}
		
		public function delete() {
			try {
				$stmt = DB::getInstance()->prepare("DELETE FROM dns_zones WHERE id=?");
				$stmt->execute(array($this->getDnsZoneId()));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
		}
		
		public function setDnsZoneId($dns_zone_id) {
			if(is_int($dns_zone_id))
				$this->dns_zone_id = $dns_zone_id;
		}
		
		public function setUserId($user_id) {
			if(is_int($user_id))
				$this->user_id = $user_id;
		}
		
		public function setName($name) {
			if(!preg_match('/^([a-z0-9-])+$/i', $name)) {
				return false;
			} else {
				$this->name = $name;
			}
		}
		
		public function setPriDns($pri_dns) {
			if(is_string($pri_dns))
				$this->pri_dns = $pri_dns;
			else
				return false;
		}
		
		public function setSecDns($sec_dns) {
			if(is_string($sec_dns))
				$this->sec_dns = $sec_dns;
			else
				return false;
		}
		
		public function setSerial($serial) {
			if(is_int($serial))
				$this->serial = $serial;
			else
				return false;
		}
		
		public function setRefresh($refresh) {
			if(is_int($refresh))
				$this->refresh = $refresh;
			else
				return false;
		}
		
		public function setRetry($retry) {
			if(is_int($retry))
				$this->retry = $retry;
			else
				return false;
		}
		
		public function setExpire($expire) {
			if(is_int($expire))
				$this->expire = $expire;
			else
				return false;
		}
		
		public function setTtl($ttl) {
			if(is_int($ttl))
				$this->ttl = $ttl;
			else
				return false;
		}
		
		public function setUser($user) {
			if($user instanceof User) {
				$this->user = $user;
				return true;
			} elseif(is_int($user)) {
				$user = new User($user);
				if($user->fetch()) {
					$this->user = $user;
					return true;
				}
			}
			return false;
		}
		
		public function getDnsZoneId() {
			return $this->dns_zone_id;
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getPriDns() {
			return $this->pri_dns;
		}
		
		public function getSecDns() {
			return $this->sec_dns;
		}
		
		public function getSerial() {
			return $this->serial;
		}
		
		public function getRefresh() {
			return $this->refresh;
		}
		
		public function getRetry() {
			return $this->retry;
		}
		
		public function getExpire() {
			return $this->expire;
		}
		
		public function getTtl() {
			return $this->ttl;
		}
		
		public function getUser() {
			return $this->user;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('dns_zone');
			$domxmlelement->appendChild($domdocument->createElement("dns_zone_id", $this->getDnsZoneId()));
			$domxmlelement->appendChild($domdocument->createElement("user_id", $this->getUserId()));
			$domxmlelement->appendChild($domdocument->createElement("name", $this->getName()));
			$domxmlelement->appendChild($domdocument->createElement("pri_dns", $this->getPriDns()));
			$domxmlelement->appendChild($domdocument->createElement("sec_dns", $this->getSecDns()));
			$domxmlelement->appendChild($domdocument->createElement("serial", $this->getSerial()));
			$domxmlelement->appendChild($domdocument->createElement("refresh", $this->getRefresh()));
			$domxmlelement->appendChild($domdocument->createElement("retry", $this->getRetry()));
			$domxmlelement->appendChild($domdocument->createElement("expire", $this->getExpire()));
			$domxmlelement->appendChild($domdocument->createElement("ttl", $this->getTtl()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			return $domxmlelement;
		}
	}
?>