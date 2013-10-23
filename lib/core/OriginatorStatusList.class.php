<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/OriginatorStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	
	class OriginatorStatusList extends ObjectList {
		private $originator_status_list = array();
		
		public function __construct($router_id=false, $crawl_cycle_id=false, $offset=false, $limit=false, $sort_by=false, $order=false) {
			$result = array();
			if($offset!==false)
				$this->setOffset((int)$offset);
			if($limit!==false)
				$this->setLimit((int)$limit);
			if($sort_by!==false)
				$this->setSortBy($sort_by);
			if($order!==false)
				$this->SetOrder($order);
				
			// initialize $total_count with the total number of objects in the list (over all pages)
			try {
				$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
													FROM crawl_batman_advanced_originators
													WHERE
														(crawl_batman_advanced_originators.router_id = :router_id OR :router_id=0) AND
														(crawl_batman_advanced_originators.crawl_cycle_id = :crawl_cycle_id OR :crawl_cycle_id=0)");
				$stmt->bindParam(':router_id', $router_id, PDO::PARAM_INT);
				$stmt->bindParam(':crawl_cycle_id', ($crawl_cycle_id) ? $crawl_cycle_id : (int)crawling::getLastEndedCrawlCycle()['id'], PDO::PARAM_INT);
				$stmt->execute();
				$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			$this->setTotalCount((int)$total_count['total_count']);
			//if limit -1 then get all ressource records
			if($this->getLimit()==-1)
				$this->setLimit($this->getTotalCount());
			
			try {
				$stmt = DB::getInstance()->prepare("SELECT  crawl_batman_advanced_originators.id as status_id,
															crawl_batman_advanced_originators.*
													FROM crawl_batman_advanced_originators
													WHERE
														(crawl_batman_advanced_originators.router_id = :router_id OR :router_id=0) AND
														(crawl_batman_advanced_originators.crawl_cycle_id = :crawl_cycle_id OR :crawl_cycle_id=0)
													ORDER BY
														case :sort_by
															when 'status_id' then crawl_batman_advanced_originators.id
															when 'router_id' then crawl_batman_advanced_originators.router_id
															when 'crawl_cycle_id' then crawl_batman_advanced_originators.crawl_cycle_id
															else NULL
														end
													".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':router_id', $router_id, PDO::PARAM_INT);
				$stmt->bindParam(':crawl_cycle_id', ($crawl_cycle_id) ? $crawl_cycle_id : (int)crawling::getLastEndedCrawlCycle()['id'], PDO::PARAM_INT);
				$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
				$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
				$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			foreach($result as $res) {
				$originator_status = new OriginatorStatus((int)$res['status_id'], (int)$res['crawl_cycle_id'],
														  (int)$res['router_id'], $res['originator'], (int)$res['link_quality'],
														  $res['nexthop'], $res['outgoing_interface'], $res['last_seen'], $res['crawl_date']);
				$this->originator_status_list[] = $originator_status;
			}
		}
		
		public function delete() {
			foreach($this->getOriginatorStatusList() as $item) {
				$item->delete();
			}
		}
		
		public function getOriginatorStatusList() {
			return $this->originator_status_list;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('originator_status_list');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->getOriginatorStatusList() as $originator_status) {
				$domxmlelement->appendChild($originator_status->getDomXMLElement($domdocument));
			}
			
			return $domxmlelement;
		}
	}
?>