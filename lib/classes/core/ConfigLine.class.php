<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class ConfigLine extends Object {
		private $config_id = 0;
		private $name = "";
		private $value = null;
		
		public function __construct($config_id=false, $name=false, $value=false) {
			$result = array();
			if($config_id!=false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT *
														FROM config
														WHERE id = ?");
					$stmt->execute(array($config_id));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($config_id==false AND $name !=false AND $value==false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT *
														FROM config
														WHERE name = ?");
					$stmt->execute(array($name));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			$this->setConfigId((int)$result['id']);
			$this->setName($result['name']);
			$this->setValue($result['value']);
			$this->setCreateDate($result['create_date']);
		}
		
		public function setConfigId($config_id) {
			if(is_int($config_id))
				$this->config_id = $config_id;
		}
		
		public function setName($name) {
			if(is_string($name))
				$this->name = $name;
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
		
		public static function configByName($name) {
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM config
													WHERE name = ?");
				$stmt->execute(array($name));
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				return $result['value'];
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
		}
	}
?>