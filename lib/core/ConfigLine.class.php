<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	
	class ConfigLine extends Object {
		private $config_id = 0;
		private $name = "";
		private $value = "";
		
		public function __construct($config_id=false, $name=false, $value=false, $create_date=false, $update_date=false) {
			$this->setConfigId($config_id);
			$this->setName($name);
			$this->setValue($value);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM config
													WHERE
														(id = :config_id OR :config_id=0) AND
														(name = :name OR :name='') AND
														(value = :value OR :value='') AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':config_id', $this->getConfigId(), PDO::PARAM_INT);
				$stmt->bindParam(':name', $this->getName(), PDO::PARAM_STR);
				$stmt->bindParam(':value', $this->getValue(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setConfigId((int)$result['id']);
				$this->setName($result['name']);
				$this->setValue($result['value']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function setConfigId($config_id) {
			if(is_int($config_id))
				$this->config_id = $config_id;
		}
		
		public function setName($name) {
			if(is_string($name) AND strlen($name)<=50) {
				$this->name = $name;
				return true;
			}
			return false;
		}
		
		public function setValue($value) {
			$this->value = $value;
		}
		
		public function getConfigId() {
			return $this->config_id;
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getValue() {
			return $this->value;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('config');
			$domxmlelement->appendChild($domdocument->createElement("config_id", $this->getConfigId()));
			$domxmlelement->appendChild($domdocument->createElement("name", $this->getName()));
			$domxmlelement->appendChild($domdocument->createElement("value", $this->getValue()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			return $domxmlelement;
		}
		
		public static function configByName($name) {
			$config_line = new ConfigLine(false, $name);
			if($config_line->fetch()) {
				return $config_line->getValue();
			} else {
				return false;
			}
		}
	}
?>