<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class UserRememberMe extends Object {
		private $user_remember_me_id = 0;
		private $user_id = 0;
		private $password = "";
		
		public function __construct($user_remember_me_id=false, $user_id=false, $password=false) {
			if($user_remember_me_id!==false) {
				try {
						$stmt = DB::getInstance()->prepare("SELECT *
															FROM user_remember_mes
															WHERE id = ?");
					$stmt->execute(array($user_remember_me_id));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				
				$this->setUserRememberMeId((int)$result['id']);
				$this->setUserId((int)$result['user_id']);
				$this->setPassword($result['password']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
			} elseif($user_remember_me_id===false AND $user_id!==false AND $password!==false) {
				$this->setUserId($user_id);
				$this->setPassword($password);
				$this->setCreateDate();
				$this->setUpdateDate();
			}
		}
		
		public function store() {
			if($this->getUserRememberMeId() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE user_remember_mes SET
																user_id = ?,
																password = ?,
																create_date = FROM_UNIXTIME(?),
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getUserId(), $this->getPassword(), $this->getCreateDate(), $this->getUserRememberMeId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getUserId() != 0 AND $this->getPassword()!="" AND $this->getCreateDate() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO user_remember_mes (user_id, password, create_date, update_date)
														VALUES (?, ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?))");
					$stmt->execute(array($this->getUserId(), $this->getPassword(), $this->getCreateDate(), $this->getUpdateDate()));
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