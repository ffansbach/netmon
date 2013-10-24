<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	
	class CrawlCycle extends Object {
		private $crawl_cycle_id = 0;
		
		public function __construct($crawl_cycle_id=false, $create_date=false, $update_date=false) {
			$this->setCrawlCycleId($crawl_cycle_id);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM crawl_cycle
													WHERE
														(id = :crawl_cycle_id OR :crawl_cycle_id=0) AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':crawl_cycle_id', $this->getChipsetId(), PDO::PARAM_INT);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setCrawlCycleId((int)$result['id']);
				$this->setCreateDate($result['crawl_date']);
				$this->setUpdateDate($result['crawl_date_end']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			return false;
		}
		
		public function delete() {
			return false;
		}
		
		// Setter methods
		public function setCrawlCycleId($crawl_cycle_id) {
			if(is_int($crawl_cycle_id)) {
				$this->crawl_cycle_id = $crawl_cycle_id;
				return true;
			}
			return false;
		}
		
		// Getter methods
		public function getCrawlCycleId() {
			return $this->crawl_cycle_id;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('crawl_cycle');
			$domxmlelement->appendChild($domdocument->createElement("crawl_cycle_id", $this->getCrawlCycleId()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			return $domxmlelement;
		}
	}
?>