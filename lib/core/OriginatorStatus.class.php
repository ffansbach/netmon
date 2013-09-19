<?php
	require_once(ROOT_DIR.'/lib/core/ObjectStatus.class.php');
	
	class OriginatorStatus extends ObjectStatus {
		private $router_id = 0;
		private $originator = "";
		private $link_quality = 0;
		private $nexthop = "";
		private $outgoing_interface = "";
		private $last_seen = "";
		
		public function __construct($status_id=false, $crawl_cycle_id=false, $router_id=false, $originator=false,
									$link_quality=false, $nexthop=false, $outgoing_interface=false, $last_seen=false,
									$create_date=false) {
			$this->setStatusId($status_id);
			$this->setCrawlCycleId($crawl_cycle_id);
			$this->setRouterId($router_id);
			$this->setOriginator($originator);
			$this->setLinkQuality($link_quality);
			$this->setNexthop($nexthop);
			$this->setOutgoingInterface($outgoing_interface);
			$this->setLastSeen($last_seen);
			$this->setCreateDate($create_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM crawl_batman_advanced_originators
													WHERE
														(id = :status_id OR :status_id=0) AND
														(router_id = :router_id OR :router_id=0) AND
														(crawl_cycle_id = :crawl_cycle_id OR :crawl_cycle_id=0) AND
														(originator = :originator OR :originator='') AND
														(link_quality = :link_quality OR :link_quality=0) AND
														(nexthop = :nexthop OR :nexthop='') AND
														(outgoing_interface = :outgoing_interface OR :outgoing_interface='') AND
														(last_seen = :last_seen OR :last_seen='') AND
														(crawl_date = FROM_UNIXTIME(:create_date) OR :create_date=0)");
				$stmt->bindParam(':status_id', $this->getStatusId(), PDO::PARAM_INT);
				$stmt->bindParam(':router_id', $this->getRouterId(), PDO::PARAM_INT);
				$stmt->bindParam(':crawl_cycle_id', $this->getCrawlCycleId(), PDO::PARAM_INT);
				$stmt->bindParam(':originator', $this->getOriginator(), PDO::PARAM_STR);
				$stmt->bindParam(':link_quality', $this->getLinkQuality(), PDO::PARAM_INT);
				$stmt->bindParam(':nexthop', $this->getNexthop(), PDO::PARAM_STR);
				$stmt->bindParam(':outgoing_interface', $this->getOutgoingInterface(), PDO::PARAM_STR);
				$stmt->bindParam(':last_seen', $this->getLastSeen(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setStatusId((int)$result['id']);
				$this->setRouterId((int)$result['router_id']);
				$this->setCrawlCycleId((int)$result['crawl_cycle_id']);
				$this->setOriginator($result['originator']);
				$this->setLinkQuality((int)$result['link_quality']);
				$this->setNexthop($result['nexthop']);
				$this->setOutgoingInterface($result['outgoing_interface']);
				$this->setLastSeen($result['last_seen']);
				$this->setCreateDate($result['crawl_date']);
				return true;
			}
			
			return false;
		}
		
		public function setRouterId($router_id) {
			if(is_int($router_id))
				$this->router_id = $router_id;
		}
		
		public function setOriginator($originator) {
			if(is_string($originator))
				$this->originator = $originator;
		}
		
		public function setLinkQuality($link_quality) {
			if(is_int($link_quality))
				$this->link_quality = $link_quality;
		}
		
		public function setNexthop($nexthop) {
			if(is_string($nexthop))
				$this->nexthop = $nexthop;
		}
		
		public function setOutgoingInterface($outgoing_interface) {
			if(is_string($outgoing_interface))
				$this->outgoing_interface = $outgoing_interface;
		}
		
		public function setLastSeen($last_seen) {
			if(is_string($last_seen))
				$this->last_seen = $last_seen;
		}
		
		public function getRouterId() {
			return $this->router_id;
		}
		
		public function getOriginator() {
			return $this->originator;
		}
		
		public function getLinkQuality() {
			return $this->link_quality;
		}
		
		public function getNexthop() {
			return $this->nexthop;
		}
		
		public function getOutgoingInterface() {
			return $this->outgoing_interface;
		}
		
		public function getLastSeen() {
			return $this->last_seen;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('originator_status');
			$domxmlelement->appendChild($domdocument->createElement("status_id", $this->getStatusId()));
			$domxmlelement->appendChild($domdocument->createElement("crawl_cycle_id", $this->getCrawlCycleId()));
			$domxmlelement->appendChild($domdocument->createElement("router_id", $this->getRouterId()));
			$domxmlelement->appendChild($domdocument->createElement("originator", $this->getOriginator()));
			$domxmlelement->appendChild($domdocument->createElement("link_quality", $this->getLinkQuality()));
			$domxmlelement->appendChild($domdocument->createElement("nexthop", $this->getNexthop()));
			$domxmlelement->appendChild($domdocument->createElement("outgoing_interface", $this->getOutgoingInterface()));
			$domxmlelement->appendChild($domdocument->createElement("last_seen", $this->getLastSeen()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));

			return $domxmlelement;
		}
	}
?>