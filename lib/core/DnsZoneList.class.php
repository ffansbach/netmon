<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsZone.class.php');
	
	class DnsZoneList extends ObjectList {
		private $dns_zone_list = array();
		
		public function __construct($user_id=false, $offset=false, $limit=false, $sort_by=false, $order=false) {
			$result = array();
			if($offset!==false)
				$this->setOffset((int)$offset);
			if($limit!==false)
				$this->setLimit((int)$limit);
			if($sort_by!==false)
				$this->setSortBy($sort_by);
			if($order!==false)
				$this->SetOrder($order);
			if($user_id!=false) {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM dns_zones
														WHERE dns_zones.user_id=?");
					$stmt->execute(array($user_id));
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
					$stmt = DB::getInstance()->prepare("SELECT id as dns_zone_id
														FROM dns_zones
														WHERE dns_zones.user_id = :user_id
														ORDER BY
															case :sort_by
																when 'create_date' then dns_zones.create_date
																else dns_zones.id
															end
														".$this->getOrder()."
														LIMIT :offset, :limit");
					$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
					$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
					$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
					$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
					$stmt->execute();
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM dns_zones");
					$stmt->execute(array($user_id));
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
					$stmt = DB::getInstance()->prepare("SELECT id as dns_zone_id
														FROM dns_zones
														ORDER BY
															case :sort_by
																when 'create_date' then dns_zones.create_date
																else dns_zones.id
															end
														".$this->getOrder()."
														LIMIT :offset, :limit");
					$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
					$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
					$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
					$stmt->execute();
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			foreach($result as $dns_zone) {
				$dns_zone = new DnsZone((int)$dns_zone['dns_zone_id']);
				$dns_zone->fetch();
				$this->dns_zone_list[] = $dns_zone;
			}
		}
		
		public function getDnsZoneList() {
			return $this->dns_zone_list;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('dns_zone_list');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->dns_zone_list as $dns_zone) {
				$domxmlelement->appendChild($dns_zone->getDomXMLElement($domdocument));
			}
			
			return $domxmlelement;
		}
	}
?>