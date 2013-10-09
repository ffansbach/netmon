<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/permission.class.php');
	
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
														(email = :email OR :email='') AND
														(jabber = :jabber OR :jabber='') AND
														(activated = :activated OR :activated=0)");
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':session_id', $this->getSessionId(), PDO::PARAM_STR);
				$stmt->bindParam(':nickname', $this->getNickname(), PDO::PARAM_STR);
				$stmt->bindParam(':password', $this->getPassword(), PDO::PARAM_STR);
				$stmt->bindParam(':openid', $this->getOpenId(), PDO::PARAM_STR);
				$stmt->bindParam(':api_key', $this->getApiKey(), PDO::PARAM_STR);
				$stmt->bindParam(':email', $this->getEmail(), PDO::PARAM_STR);
				$stmt->bindParam(':jabber', $this->getJabber(), PDO::PARAM_STR);
				$stmt->bindParam(':activated', $this->getActivated(), PDO::PARAM_INT);
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
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
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
		
		public function setSessionId($session_id) {
			if(is_string($session_id)) {
				$this->session_id = $session_id;
				return true;
			}
			return false;
		}
		
		public function setNickname($nickname) {
			if(preg_match('/^([a-zA-Z0-9])+$/i', $nickname)) {
				$this->nickname = $nickname;
				return true;
			}
			return false;
		}
		
		public function setPassword($password) {
			if(is_string($password))  {
				$this->password = $password;
				return true;
			}
			return false;
		}
		
		public function setOpenId($openid) {
			if(is_string($openid)) { //TODO: check if openid is valid format
				$this->openid = $openid;
				return true;
			}
			return false;
		}
		
		public function setApiKey($api_key) {
			if(is_string($api_key)) {
				$this->api_key = $api_key;
				return true;
			}
			return false;
		}
		
		public function setFirstName($firstname) {
			if(is_string($firstname)) {
				$this->firstname = $firstname;
				return true;
			}
			return false;
		}
		
		public function setLastName($lastname) {
			if(is_string($lastname)) {
				$this->lastname = $lastname;
				return true;
			}
			return false;
		}
		
		public function setStreet($street) {
			if(is_string($street)) {
				$this->street = $street;
				return true;
			}
			return false;
		}
		
		public function setZipcode($zipcode) {
			if(is_int($zipcode)) {
				$this->zipcode = $zipcode;
				return true;
			}
			return false;
		}
		
		public function setCity($city) {
			if(is_string($city)) {
				$this->city = $city;
				return true;
			}
			return false;
		}
		
		public function setPhonenumer($phonenumber) {
			if(is_string($phonenumber) OR is_int($phonenumber)) {
				$this->phonenumber = $phonenumber;
				return true;
			}
			return false;
		}
		
		public function setEmail($email) {
			if(is_string($email)) { //TODO: check if email is in valid format
				$this->email = $email;
				return true;
			}
			return false;
		}
		
		public function setJabber($jabber) {
			if(is_string($jabber)) { //TODO: check if jabber id is in valid format
				$this->jabber = $jabber;
				return true;
			}
			return false;
		}
		
		public function setWebsite($website) {
			if(is_string($website)) { //TODO: check if website is in valid format
				if(!empty($website) AND substr($website, 0, 8)!="https://" AND substr($website, 0, 7)!="https://") {
					$website = "http://".$website;
				}
				$this->website = $website;
				return true;
			}
			return false;
		}
		
		public function setDescription($description) {
			if(is_string($description)) {
				$this->description = $description;
				return true;
			}
			return false;
		}
		
		public function setNotificationMethod($notification_method) {
			if($notification_method=="email" OR $notification_method=="jabber") {
				$this->notification_method = $notification_method;
				return true;
			}
			return false;
		}
		
		public function setPermission($permission) {
			if(is_int($permission)) {
				$this->permission = $permission;
				return true;
			}
			return false;
		}
		
		public function setActivated($activated) {
			if(is_int($activated)) {
				$this->activated = $activated;
				return true;
			}
			return false;
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
		
		public function getRoles() {
			if($this->getUserId()!=0) {
				$roles = Permission::getEditableRoles();
				foreach ($roles as $key=>$role) {
					$roles_edit[$key]['role'] = $role;
					$roles_edit[$key]['dual'] = pow(2,$role);
					$roles_edit[$key]['check'] = Permission::checkPermission($roles_edit[$key]['dual'], $this->getUserId());
				}
				return $roles_edit;
			}
			return array();
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('user');
			$domxmlelement->appendChild($domdocument->createElement("user_id", $this->getUserId()));
//private?			$domxmlelement->appendChild($domdocument->createElement("session_id", $this->getSessionId()));
			$domxmlelement->appendChild($domdocument->createElement("nickname", $this->getNickname()));
//private?			$domxmlelement->appendChild($domdocument->createElement("password", $this->getPassword()));
			$domxmlelement->appendChild($domdocument->createElement("openid", $this->getOpenId()));
//private?			$domxmlelement->appendChild($domdocument->createElement("api_key", $this->getApiKey()));
			$domxmlelement->appendChild($domdocument->createElement("firstname", $this->getFirstName()));
			$domxmlelement->appendChild($domdocument->createElement("lastname", $this->getLastName()));
			$domxmlelement->appendChild($domdocument->createElement("street", $this->getStreet()));
			$domxmlelement->appendChild($domdocument->createElement("zipcode", $this->getZipcode()));
			$domxmlelement->appendChild($domdocument->createElement("city", $this->getCity()));
			$domxmlelement->appendChild($domdocument->createElement("phonenumber", $this->getPhonenumer()));
			$domxmlelement->appendChild($domdocument->createElement("email", $this->getEmail()));
			$domxmlelement->appendChild($domdocument->createElement("jabber", $this->getJabber()));
			$domxmlelement->appendChild($domdocument->createElement("website", $this->getWebsite()));
			$domxmlelement->appendChild($domdocument->createElement("description", $this->getDescription()));
			$domxmlelement->appendChild($domdocument->createElement("notification_method", $this->getNotificationMethod()));
			$domxmlelement->appendChild($domdocument->createElement("permission", $this->getPermission()));
			$domxmlelement->appendChild($domdocument->createElement("activated", $this->getActivated()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			return $domxmlelement;
		}
	}
?>