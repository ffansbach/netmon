<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class User extends Object {
		private $user_id;
		private $session_id;
		private $nickname;
		private $password;
		private $salt;
		private $openid;
		private $api_key;
		private $firstname;
		private $lastname;
		private $street;
		private $zipcode;
		private $phonenumber;
		private $email = null;
		private $jabber = array();
		private $notification_method = "";
		private $website;
		private $description;
		private $permission;
		private $activated;
		
		public function __construct($user_id=false, $session_id=false, $nickname=false, $password=false, $salt=false,
									$openid=false, $api_key=false, $firstname=false, $lastname=false, $street=false,
									$zipcode=false, $phonenumber=false, $email=false, $jabber=false,
									$notification_method=false, $website=false, $description=false,
									$permission=false, $activated=false) {
			if($user_id==false) {
				$result = array();
			} else {
				try {
						$stmt = DB::getInstance()->prepare("SELECT *
															FROM users
															WHERE id = ?");
					$stmt->execute(array($user_id));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				
				$this->setUserId((int)$result['id']);
				$this->setNickname($result['nickname']);
				$this->setEmail($result['email']);
				$this->setJabber($result['jabber']);
				$this->setNotificationMethod($result['notification_method']);
				$this->setDescription($result['about']);
				$this->setCreateDate($result['create_date']);
			}
		}

		public function setUserId($user_id) {
			if(is_int($user_id))
				$this->user_id = $user_id;
		}
		
		public function setNickname($nickname) {
			if(!preg_match('/^([a-zA-Z0-9])+$/i', $nickname)) {
				return false;
			} else {
				$this->nickname = $nickname;
			}
		}
		
		public function setDescription($description) {
			$this->description = $description;
		}
		
		public function setEmail($email) {
			$this->email = $email;
		}
		
		public function setJabber($jabber) {
			$this->jabber = $jabber;
		}
		
		public function setNotificationMethod($notification_method) {
			$this->notification_method = $notification_method;
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		public function getNickname() {
			return $this->nickname;
		}
		
		public function getDescription() {
			return $this->description;
		}
		
		public function getEmail() {
			return $this->email;
		}
		
		public function getJabber() {
			return $this->jabber;
		}
		
		public function getNotificationMethod() {
			return $this->notification_method;
		}
	}
?>