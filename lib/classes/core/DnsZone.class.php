<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class DnsZone extends Object {
		private $dns_zone_id = 0;
		private $user_id = 0;
		private $name = "";
		
		public function __construct($dns_zone_id=false, $user_id=false, $name=false, $create_date=false, $update_date=false) {
			$this->setDnsZoneId((int)$dns_zone_id);
			$this->setUserId((int)$user_id);
			$this->setName($name);
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
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':dns_zone_id', $this->getDnsZoneId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':name', $this->getName(), PDO::PARAM_STR);
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
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->getDnsZoneId() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE dns_zones SET
																user_id = ?,
																name = ?,
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getUserId(), $this->getName(), $this->getDnsZoneId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getUserId() != 0 AND $this->getName()!="") {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO dns_zones (user_id, name, create_date, update_date)
														VALUES (?, ?, NOW(), NOW())");
					$stmt->execute(array($this->getUserId(), $this->getName()));
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
			if(!preg_match('/^([a-z])+$/i', $name)) {
				return false;
			} else {
				$this->name = $name;
			}
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
	}
?>