<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	
	class Chipset extends Object {
		private $chipset_id = 0;
		private $name = "";
		private $hardware_name = "";
		
		public function __construct($chipset_id=false, $name=false, $hardware_name=false,
									$create_date=false, $update_date=false) {
			$this->setChipsetId($chipset_id);
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
														(name = :name OR :name='') AND
														(hardware_name = :hardware_name OR :hardware_name='') AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':chipset_id', $this->getChipsetId(), PDO::PARAM_INT);
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
				$this->setName($result['name']);
				$this->setHardwareName($result['hardware_name']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			//check if a chipset with the same name already exists
			$chipset_test = new Chipset(false, $this->getName());
			$chipset_test->fetch();
			if($this->getChipsetId() != 0 AND !($chipset_test->getChipsetId()!=$this->getChipsetId() AND $chipset_test->getName()==$this->getName())) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE chipsets SET
																name = ?,
																hardware_name = ?,
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getName(), $this->getHardwareName()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($chipset_test->getChipsetId()==0) {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO chipsets (name, hardware_name, create_date, update_date)
														VALUES (?, ?, NOW(), NOW())");
					$stmt->execute(array($this->getName(), $this->getHardwareName()));
					$this->setChipsetId((int)DB::getInstance()->lastInsertId());
					return $this->getChipsetId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			return false;
		}
		
		public function delete() {
			// TODO: Routerliste mit allen routern holen die diesen Chipset verwenden und die Chipset ID auf die ID des Unknown Chipsets setzen.
			//		 Soll der Unknown Chipsatz selbst entfernt werden, muss dies verweigert werden
			//		 Anschließend löschen
			return false;
		}
		
		// Setter methods
		public function setChipsetId($chipset_id) {
			if(is_int($chipset_id)) {
				$this->chipset_id = $chipset_id;
				return true;
			}
			return false;
		}
		
		public function setName($name) {
			if(is_string($name) AND strlen($name)<=30) {
				$this->name = $name;
				return true;
			}
			return false;
		}
		
		public function setHardwareName($hardware_name) {
			if(is_string($hardware_name) AND strlen($hardware_name)<=100) {
				$this->hardware_name = $hardware_name;
				return true;
			}
			return false;
		}
		
		// Getter methods
		public function getChipsetId() {
			return $this->chipset_id;
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
			$domxmlelement->appendChild($domdocument->createElement("name", $this->getName()));
			$domxmlelement->appendChild($domdocument->createElement("hardware_name", $this->getHardwareName()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			return $domxmlelement;
		}
	}
?>