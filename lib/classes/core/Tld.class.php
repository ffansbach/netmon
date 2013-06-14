<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class Tld extends Object {
		private $tld_id = 0;
		private $user_id = 0;
		private $tld = "";
		
		public function __construct($tld_id=false, $user_id=false, $tld=false) {
			if($tld_id==false AND $user_id!=false AND $tld!=false) {
				$this->setUserId((int)$user_id);
				$this->setCreateDate();
				$this->setTld($tld);
			} elseif($tld_id !== false AND is_int((int)$tld_id)) {
				//fetch tld data from database
				$result = array();
				try {
					$stmt = DB::getInstance()->prepare("SELECT *
														FROM tlds
														WHERE id = ?");
					$stmt->execute(array($tld_id));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				
				$this->setTldId((int)$result['id']);
				$this->setUserId((int)$result['user_id']);
				$this->setTld($result['tld']);
				$this->setCreateDate($result['create_date']);
			}
		}
		
		public function store() {
			if($this->getTldId() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE tlds SET
																user_id = ?,
																tld = ?,
																create_date = FROM_UNIXTIME(?)
														WHERE id=?");
					$stmt->execute(array($this->getUserId(), $this->getTld(), $this->getCreateDate(), $this->getTldId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getUserId() != 0 AND $this->getTld()!="" AND $this->getCreateDate() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO tlds (user_id, tld, create_date)
														VALUES (?, ?, FROM_UNIXTIME(?))");
					$stmt->execute(array($this->getUserId(), $this->getTld(), $this->getCreateDate()));
					return DB::getInstance()->lastInsertId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				return false;
			}
		}
		
		public function delete() {
			try {
				$stmt = DB::getInstance()->prepare("DELETE FROM tlds WHERE id=?");
				$stmt->execute(array($this->getTldId()));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
		}
		
		public function setTldId($tld_id) {
			if(is_int($tld_id))
				$this->tld_id = $tld_id;
		}
		
		public function setUserId($user_id) {
			if(is_int($user_id))
				$this->user_id = $user_id;
		}
		
		public function setTld($tld) {
			if(!preg_match('/^([a-z])+$/i', $tld)) {
				return false;
			} else {
				$this->tld = $tld;
			}
		}
		
		public function getTldId() {
			return $this->tld_id;
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		public function getTld() {
			return $this->tld;
		}
	}
?>