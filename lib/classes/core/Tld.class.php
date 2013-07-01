<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class Tld extends Object {
		private $tld_id = 0;
		private $user_id = 0;
		private $tld = "";
		
		public function __construct($tld_id=false, $user_id=false, $tld=false, $create_date=false, $update_date=false) {
			$this->setTldId((int)$tld_id);
			$this->setUserId((int)$user_id);
			$this->setTld($tld);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM tlds
													WHERE
														(id = :tld_id OR :tld_id=0) AND
														(user_id = :user_id OR :user_id=0) AND
														(tld = :tld OR :tld='') AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':tld_id', $this->getTldId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':tld', $this->getTld(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setTldId((int)$result['id']);
				$this->setUserId((int)(int)$result['user_id']);
				$this->setTld($result['tld']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->getTldId() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE tlds SET
																user_id = ?,
																tld = ?,
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getUserId(), $this->getTld(), $this->getTldId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getUserId() != 0 AND $this->getTld()!="") {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO tlds (user_id, tld, create_date, update_date)
														VALUES (?, ?, NOW(), NOW())");
					$stmt->execute(array($this->getUserId(), $this->getTld()));
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