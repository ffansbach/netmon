<?php
	require_once('../../lib/classes/core/Object.class.php');
	
	class ObjectStatus extends Object {
		protected $status_id = 0;
		protected $crawl_cycle_id = 0;
		
		public function __construct() {
			// initialize $crawl_cycle_id with id of last endet crawl cycle
			try {
				$stmt = DB::getInstance()->prepare("SELECT crawl_cycle.id as crawl_cycle_id
													FROM crawl_cycle
													ORDER BY id desc
													LIMIT 1,1");
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			$this->setCrawlCycleId((int)$result['id']);
		}
		
		protected function setStatusId($status_id) {
			if(is_int($status_id))
				$this->status_id = $status_id;
		}
		
		protected function setCrawlCycleId($crawl_cycle_id) {
			if(is_int($crawl_cycle_id))
				$this->crawl_cycle_id = $crawl_cycle_id;
		}
		
		protected function getStatusId() {
			return $this->status_id;
		}
		
		protected function getCrawlCycleId() {
			return $this->crawl_cycle_id;
		}
	}
?>