<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	
	class User extends Object {
		private $user_id=0;
		private $session_id='';
		private $nickname='';
		private $password='';
		private $openid='';
		private $api_key='';
		private $firstname='';
		private $lastname='';
		private $street='';
		private $zipcode=0;
		private $city='';
		private $phonenumber='';
		private $email = '';
		private $jabber = '';
		private $website='';
		private $description='';
		private $notification_method = '';
		private $permission=0;
		private $activated=0;
		
		public function __construct($user_id=false, $session_id=false, $nickname=false, $password=false,
									$openid=false, $api_key=false, $firstname=false, $lastname=false, $street=false,
									$zipcode=false, $city=false, $phonenumber=false, $email=false, $jabber=false,
									$website=false, $description=false, $notification_method=false,
									$permission=false, $activated=false,
									$create_date=false, $update_date=false) {
			$this->setUserId($user_id);
			$this->setSessionId($session_id);
			$this->setNickname($nickname);
			$this->setPassword($password);
			$this->setOpenId($openid);
			$this->setApiKey($api_key);
			$this->setFirstName($firstname);
			$this->setLastName($lastname);
			$this->setStreet($street);
			$this->setZipcode($zipcode);
			$this->setCity($city);
			$this->setPhonenumer($phonenumber);
			$this->setEmail($email);
			$this->setJabber($jabber);
			$this->setWebsite($website);
			$this->setDescription($description);
			$this->setNotificationMethod($notification_method);
			$this->setPermission($permission);
			$this->setActivated($activated);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM users
													WHERE
														(id = :user_id OR :user_id=0) AND
														(session_id = :session_id OR :session_id='') AND
														(nickname = :nickname OR :nickname='') AND
														(password = :password OR :password='') AND
														(openid = :openid OR :openid='') AND
														(api_key = :api_key OR :api_key='') AND
														(vorname = :firstname OR :firstname='') AND
														(nachname = :lastname OR :lastname='') AND
														(strasse = :street OR :street='') AND
														(plz = :zipcode OR :zipcode=0) AND
														(ort = :city OR :city='') AND
														(telefon = :phonenumber OR :phonenumber='') AND
														(email = :email OR :email='') AND
														(jabber = :jabber OR :jabber='') AND
														(website = :website OR :website='') AND
														(about = :description OR :description='') AND
														(notification_method = :notification_method OR :notification_method='') AND
														(permission = :permission OR :permission=0) AND
														(activated = :activated OR :activated=0) AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':session_id', $this->getSessionId(), PDO::PARAM_STR);
				$stmt->bindParam(':nickname', $this->getNickname(), PDO::PARAM_STR);
				$stmt->bindParam(':password', $this->getPassword(), PDO::PARAM_STR);
				$stmt->bindParam(':openid', $this->getOpenId(), PDO::PARAM_STR);
				$stmt->bindParam(':api_key', $this->getApiKey(), PDO::PARAM_STR);
				$stmt->bindParam(':firstname', $this->getFirstName(), PDO::PARAM_STR);
				$stmt->bindParam(':lastname', $this->getLastName(), PDO::PARAM_STR);
				$stmt->bindParam(':street', $this->getStreet(), PDO::PARAM_STR);
				$stmt->bindParam(':zipcode', $this->getZipcode(), PDO::PARAM_INT);
				$stmt->bindParam(':city', $this->getCity(), PDO::PARAM_STR);
				$stmt->bindParam(':phonenumber', $this->getPhonenumer(), PDO::PARAM_STR);
				$stmt->bindParam(':email', $this->getEmail(), PDO::PARAM_STR);
				$stmt->bindParam(':jabber', $this->getJabber(), PDO::PARAM_STR);
				$stmt->bindParam(':website', $this->getWebsite(), PDO::PARAM_STR);
				$stmt->bindParam(':description', $this->getDescription(), PDO::PARAM_STR);
				$stmt->bindParam(':notification_method', $this->getNotificationMethod(), PDO::PARAM_STR);
				$stmt->bindParam(':permission', $this->getPermission(), PDO::PARAM_INT);
				$stmt->bindParam(':activated', $this->getActivated(), PDO::PARAM_INT);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setUserId((int)$result['id']);
				$this->setSessionId($result['session_id']);
				$this->setNickname($result['nickname']);
				$this->setPassword($result['password']);
				$this->setOpenId($result['openid']);
				$this->setApiKey($result['api_key']);
				$this->setFirstName($result['vorname']);
				$this->setLastName($result['nachname']);
				$this->setStreet($result['strasse']);
				$this->setZipcode((int)$result['plz']);
				$this->setCity($result['ort']);
				$this->setPhonenumer($result['telefon']);
				$this->setEmail($result['email']);
				$this->setJabber($result['jabber']);
				$this->setWebsite($result['website']);
				$this->setDescription($result['about']);
				$this->setNotificationMethod($result['notification_method']);
				$this->setPermission((int)$result['permission']);
				$this->setActivated((int)$result['activated']);
				$this->setCreateDate($result['update_date']);
				$this->setUpdateDate($result['create_date']);
				return true;
			}
			
			return false;
		}
		
		public function setUserId($user_id) {
			if(is_int($user_id))
				$this->user_id = $user_id;
		}
		
		public function setSessionId($session_id) {
			if(is_string($session_id))
				$this->session_id = $session_id;
		}
		
		public function setNickname($nickname) {
			if(!preg_match('/^([a-zA-Z0-9])+$/i', $nickname)) {
				return false;
			} else {
				$this->nickname = $nickname;
			}
		}
		
		public function setPassword($password) {
			if($password!=false)
				$this->password = $password;
		}
		
		public function setOpenId($openid) {
			if(is_string($openid))
				$this->openid = $openid;
		}
		
		public function setApiKey($api_key) {
			if($api_key!=false)
				$this->api_key = $api_key;
		}
		
		public function setFirstName($firstname) {
			if(is_string($firstname))
				$this->firstname = $firstname;
		}
		
		public function setLastName($lastname) {
			if(is_string($lastname))
				$this->lastname = $lastname;
		}
		
		public function setStreet($street) {
			if(is_string($street))
				$this->street = $street;
		}
		
		public function setZipcode($zipcode) {
			if(is_int($zipcode))
				$this->zipcode = $zipcode;
		}
		
		public function setCity($city) {
			if(is_string($city))
				$this->city = $city;
		}
		
		public function setPhonenumer($phonenumber) {
			if($phonenumber!=false)
				$this->phonenumber = $phonenumber;
		}
		
		public function setEmail($email) {
			if($email!=false)
				$this->email = $email;
		}
		
		public function setJabber($jabber) {
			if($jabber!=false)
				$this->jabber = $jabber;
		}
		
		public function setWebsite($website) {
			if(is_string($website)) {
				$this->website = $website;
			}
		}
		
		public function setDescription($description) {
			if($description!=false)
				$this->description = $description;
		}
		
		public function setNotificationMethod($notification_method) {
			if($notification_method!=false)
				$this->notification_method = $notification_method;
		}
		
		public function setPermission($permission) {
			if(is_int($permission)) {
				$this->permission = $permission;
			}
		}
		
		public function setActivated($activated) {
			if(is_int($activated)) {
				$this->activated = $activated;
			}
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		public function getSessionId() {
			return $this->session_id;
		}
		
		public function getNickname() {
			return $this->nickname;
		}
		
		public function getPassword() {
			return $this->password;
		}
		
		public function getOpenId() {
			return $this->openid;
		}
		
		public function getApiKey() {
			return $this->api_key;
		}
		
		public function getFirstName() {
			return $this->firstname;
		}
		
		public function getLastName() {
			return $this->lastname;
		}
		
		public function getStreet() {
			return $this->street;
		}
		
		public function getZipcode() {
			return $this->zipcode;
		}
		
		public function getCity() {
			return $this->city;
		}
		
		public function getPhonenumer() {
			return $this->phonenumber;
		}
		
		public function getEmail() {
			return $this->email;
		}
		
		public function getJabber() {
			return $this->jabber;
		}
		
		public function getWebsite() {
			return $this->website;
		}
		
		public function getDescription() {
			return $this->description;
		}
		
		public function getNotificationMethod() {
			return $this->notification_method;
		}
		
		public function getPermission() {
			return $this->permission;

		}
		
		public function getActivated() {
			return $this->activated;
		}
	}
?>