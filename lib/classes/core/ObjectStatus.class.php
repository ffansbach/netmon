<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class ObjectStatus extends Object {
		protected $status_id = 0;
		protected $crawl_cycle_id = 0;
		
		public function __construct() {

		}
		
		protected function setStatusId($status_id) {
			if(is_int($status_id))
				$this->status_id = $status_id;
		}
		
		protected function setCrawlCycleId($crawl_cycle_id=false) {
			if($crawl_cycle_id==false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT max(id) as crawl_cycle_id
														FROM crawl_cycle");
					$stmt->execute();
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->crawl_cycle_id = $result['crawl_cycle_id'];
			} elseif(is_int($crawl_cycle_id)) {
				$this->crawl_cycle_id = $crawl_cycle_id;
			}
			
			echo $this->crawl_cycle_id;
		}
		
		protected function getStatusId() {
			return $this->status_id;
		}
		
		protected function getCrawlCycleId() {
			return $this->crawl_cycle_id;
		}
	}
?>