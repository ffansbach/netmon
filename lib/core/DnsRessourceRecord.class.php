<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/Ip.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsZone.class.php');
	
	class DnsRessourceRecord extends Object {
		private $dns_ressource_record_id = 0;
		private $dns_zone_id = 0;
		private $user_id = 0;
		private $host = "";
		private $type = "";
		private $pri = 0;
		private $destination_id = 0;
		
		private $user = null;
		private $dns_zone = null;
		
		public function __construct($dns_ressource_record_id=false, $dns_zone_id=false, $user_id=false,
									$host=false, $type=false, $pri=false, $destination_id=false,
									$create_date=false, $update_date=false) {
			$this->setDnsRessourceRecordId($dns_ressource_record_id);
			$this->setDnsZoneId($dns_zone_id);
			$this->setUserId($user_id);
			$this->setHost($host);
			$this->setType($type);
			$this->setPri($pri);
			$this->setDestinationId($destination_id);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM dns_ressource_records
													WHERE
														(id = :dns_ressource_record_id OR :dns_ressource_record_id=0) AND
														(dns_zone_id = :dns_zone_id OR :dns_zone_id=0) AND
														(user_id = :user_id OR :user_id=0) AND
														(host = :host OR :host='') AND
														(type = :type OR :type='') AND
														(pri = :pri OR :pri=0) AND
														(destination_id = :destination_id OR :destination_id=0) AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':dns_ressource_record_id', $this->getDnsRessourceRecordId(), PDO::PARAM_INT);
				$stmt->bindParam(':dns_zone_id', $this->getDnsZoneId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':host', $this->getHost(), PDO::PARAM_STR);
				$stmt->bindParam(':type', $this->getType(), PDO::PARAM_STR);
				$stmt->bindParam(':pri', $this->getPri(), PDO::PARAM_INT);
				$stmt->bindParam(':destination_id', $this->getDestinationId(), PDO::PARAM_INT);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setDnsRessourceRecordId((int)$result['id']);
				$this->setDnsZoneId((int)$result['dns_zone_id']);
				$this->setUserId((int)$result['user_id']);
				$this->setHost($result['host']);
				$this->setType($result['type']);
				$this->setPri((int)$result['pri']);
				$this->setDestinationId((int)$result['destination_id']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				
				$this->setUser($this->getUserId());
				$this->setDnsZone($this->getDnsZoneId());
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->getDnsRessourceRecordId() != 0 AND $this->getDnsZoneId()!=0 AND $this->getUserId()!=0 AND
			   $this->getHost()!="" AND $this->getType()!="" AND $this->getDestinationId()!=0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE dns_ressource_records SET
																dns_zone_id = ?,
																user_id = ?,
																host = ?,
																type = ?,
																pri = ?,
																destination_id = ?,
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getDnsZoneId(), $this->getUserId(), $this->getHost(), $this->getType(),
										 $this->getPri(), $this->getDestinationId(), $this->getDnsRessourceRecordId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getDnsRessourceRecordId() == 0 AND $this->getDnsZoneId()!=0 AND $this->getUserId()!=0 AND
					 $this->getHost()!="" AND $this->getType()!="" AND $this->getDestinationId()!=0) {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO dns_ressource_records (dns_zone_id, user_id, host, type,
																						   pri, destination_id,
																						   create_date, update_date)
														VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
					$stmt->execute(array($this->getDnsZoneId(), $this->getUserId(), $this->getHost(), $this->getType(),
										 $this->getPri(), $this->getDestinationId()));
					return DB::getInstance()->lastInsertId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			return false;
		}
		
		public function delete() {
			// TODO: delete CNAME Records that point to this ressource record
			try {
				$stmt = DB::getInstance()->prepare("DELETE FROM dns_ressource_records WHERE id=?");
				$stmt->execute(array($this->getDnsRessourceRecordId()));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			return true;
		}
		
		// Setter methods
		public function setDnsRessourceRecordId($dns_ressource_record_id) {
			if(is_int($dns_ressource_record_id)) {
				$this->dns_ressource_record_id = $dns_ressource_record_id;
				return true;
			}
			return false;
		}
		
		public function setDnsZoneId($dns_zone_id) {
			if(is_int($dns_zone_id)) {
				$this->dns_zone_id = $dns_zone_id;
				return true;
			}
			return false;
		}
		
		public function setUserId($user_id) {
			if(is_int($user_id)) {
				$this->user_id = $user_id;
				return true;
			}
			return false;
		}
		
		public function setHost($host) {
			if(is_string($host)) {
				$this->host = $host;
				return true;
			}
			return false;
		}
		
		public function setType($type) {
			if(is_string($type)) {
				$this->type = $type;
				return true;
			}
			return false;
		}
		
		public function setPri($pri) {
			if(is_int($pri)) {
				$this->pri = $pri;
				return true;
			}
			return false;
		}
		
		public function setDestinationId($destination_id) {
			if(is_int($destination_id)) {
				$this->destination_id = $destination_id;
				return true;
			}
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
		
		public function setDnsZone($dns_zone) {
			if($dns_zone instanceof DnsZone) {
				$this->dns_zone = $dns_zone;
				return true;
			} elseif(is_int($dns_zone)) {
				$dns_zone = new DnsZone($dns_zone);
				if($dns_zone->fetch()) {
					$this->dns_zone = $dns_zone;
					return true;
				}
			}
			return false;
		}
		
		// Getter methods
		public function getDnsRessourceRecordId() {
			return $this->dns_ressource_record_id;
		}
		
		public function getDnsZoneId() {
			return $this->dns_zone_id;
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		public function getHost() {
			return $this->host;
		}
		
		public function getType() {
			return $this->type;
		}
		
		public function getPri() {
			return $this->pri;
		}
		
		public function getDestinationId() {
			return $this->destination_id;
		}
		
		public function getUser() {
			return $this->user;
		}
		
		public function getDnsZone() {
			return $this->dns_zone;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('dns_ressource_record');
			$domxmlelement->appendChild($domdocument->createElement("dns_ressource_record_id", $this->getDnsRessourceRecordId()));
			$domxmlelement->appendChild($domdocument->createElement("dns_zone_id", $this->getDnsZoneId()));
			$domxmlelement->appendChild($domdocument->createElement("user_id", $this->getUserId()));
			$domxmlelement->appendChild($domdocument->createElement("host", $this->getHost()));
			$domxmlelement->appendChild($domdocument->createElement("type", $this->getType()));
			$domxmlelement->appendChild($domdocument->createElement("pri", $this->getPri()));
			$domxmlelement->appendChild($domdocument->createElement("destination_id", $this->getDestinationId()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			return $domxmlelement;
		}
	}
?>