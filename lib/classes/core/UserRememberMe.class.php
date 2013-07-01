<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class UserRememberMe extends Object {
		private $user_remember_me_id = 0;
		private $user_id = 0;
		private $password = "";
		
		public function __construct($user_remember_me_id=false, $user_id=false, $password=false,
									$create_date=false, $update_date=false) {
			$this->setUserRememberMeId($user_remember_me_id);
			$this->setUserId($user_id);
			$this->setPassword($password);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM user_remember_mes
													WHERE
														(id = :user_remember_me_id OR :user_remember_me_id=0) AND
														(user_id = :user_id OR :user_id=0) AND
														(password = :password OR :password='') AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':user_remember_me_id', $this->getUserRememberMeId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':password', $this->getPassword(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setUserRememberMeId((int)$result['id']);
				$this->setUserId((int)(int)$result['user_id']);
				$this->setPassword($result['password']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->getUserRememberMeId() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE user_remember_mes SET
																user_id = ?,
																password = ?,
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getUserId(), $this->getPassword(), $this->getUserRememberMeId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getUserId() != 0 AND $this->getPassword()!="") {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO user_remember_mes (user_id, password, create_date, update_date)
														VALUES (?, ?, NOW(), NOW())");
					$stmt->execute(array($this->getUserId(), $this->getPassword()));
					return DB::getInstance()->lastInsertId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			return false;
		}
		
		public function delete() {
			if($this->getUserRememberMeId() != 0) {
				try {
 					$stmt = DB::getInstance()->prepare("DELETE FROM user_remember_mes WHERE id=?");
					$stmt->execute(array($this->getUserRememberMeId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				return false;
			}
		}
		
		public function setUserRememberMeId($user_remember_me_id) {
			if(is_int($user_remember_me_id))
				$this->user_remember_me_id = $user_remember_me_id;
		}
		
		public function setUserId($user_id) {
			if(is_int($user_id))
				$this->user_id = $user_id;
		}
		
		public function setPassword($password) {
			if(!empty($password))
				$this->password = $password;
		}
		
		public function getUserRememberMeId() {
			return $this->user_remember_me_id;
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		public function getPassword() {
			return $this->password;
		}
		
		public function toString() {
			return "ID: ".$this->getUserRememberMeId().", User-ID: ".$this->getUserId().", Password: ".$this->getPassword().", Create-Date: ".$this->getCreateDate().", Update-Date: ".$this->getUpdateDate();
		}
	}
?>