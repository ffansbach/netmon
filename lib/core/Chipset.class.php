<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	
	class Chipset extends Object {
		private $chipset_id = 0;
		private $user_id = 0;
		private $name = "";
		private $hardware_name = "";
		
		public function __construct($chipset_id=false, $user_id=false, $name=false, $hardware_name=false,
									$create_date=false, $update_date=false) {
			$this->setChipsetId($chipset_id);
			$this->setUserId($user_id);
			$this->setName($name);
			$this->setHardwareName($hardware_name);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM chipsets
													WHERE
														(id = :chipset_id OR :chipset_id=0) AND
														(user_id = :user_id OR :user_id=0) AND
														(name = :name OR :name='') AND
														(hardware_name = :hardware_name OR :hardware_name='') AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':chipset_id', $this->getChipsetId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':name', $this->getName(), PDO::PARAM_STR);
				$stmt->bindParam(':hardware_name', $this->getHardwareName(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setChipsetId((int)$result['id']);
				$this->setUserId((int)$result['user_id']);
				$this->setName($result['name']);
				$this->setHardwareName($result['hardware_name']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			return false; // TODO
		}
		
		public function delete() {
			return false; // TODO
		}
		
		// Setter methods
		public function setChipsetId($chipset_id) {
			if(is_int($chipset_id)) {
				$this->chipset_id = $chipset_id;
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
		
		public function setname($name) {
			if(is_string($name)) {
				$this->name = $name;
				return true;
			}
			return false;
		}
		
		public function setHardwareName($hardware_name) {
			if(is_string($hardware_name)) {
				$this->hardware_name = $hardware_name;
				return true;
			}
			return false;
		}
		
		// Getter methods
		public function getChipsetId() {
			return $this->chipset_id;
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getHardwareName() {
			return $this->hardware_name;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('chipset');
			$domxmlelement->appendChild($domdocument->createElement("chipset_id", $this->getChipsetId()));
			$domxmlelement->appendChild($domdocument->createElement("user_id", $this->getUserId()));
			$domxmlelement->appendChild($domdocument->createElement("name", $this->getName()));
			$domxmlelement->appendChild($domdocument->createElement("hardware_name", $this->getHardwareName()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			return $domxmlelement;
		}
	}
?>