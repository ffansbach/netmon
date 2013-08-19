<?php
	require_once(ROOT_DIR.'/lib/classes/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Service.class.php');
	
	class Servicelist extends ObjectList {
		private $servicelist = array();
		
		//TODO: add functionality so that user can select service by $ip_id and $dns_ressource_record_id
		public function __construct($user_id=false, $ip_id=false, $dns_ressource_record_id=false,
									$offset=false, $limit=false, $sort_by=false, $order=false) {
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
													FROM services
													WHERE
														(user_id = :user_id OR :user_id=0)");
				$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
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
				$stmt = DB::getInstance()->prepare("SELECT id as service_id
													FROM services
													WHERE
														(user_id = :user_id OR :user_id=0)
													ORDER BY
														case :sort_by
															when 'create_date' then services.create_date
															else services.id
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
			
			foreach($result as $service) {
				$service = new Service((int)$service['service_id']);
				$service->fetch();
				$this->servicelist[] = $service;
			}
		}
		
		/*
		public function add($item) {
			if($item instanceof DnsRessourceRecordList) {
				$this->setDnsRessourceRecordList(array_merge($this->getDnsRessourceRecordList(), $item->getDnsRessourceRecordList()));
				return true;
			} elseif($item instanceof DnsRessourceRecord) {
				$this->dns_ressource_record_list[] = $item;
				return true;
			}
			return false;
		}*/
		
		public function setServicelist($servicelist) {
			if($servicelist instanceof Servicelist) {
				$this->servicelist = $servicelist;
				return true;
			}
			return false;
		}
		
		public function getServiceList() {
			return $this->servicelist;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('servicelist');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->servicelist as $service) {
				$domxmlelement->appendChild($service->getDomXMLElement($domdocument));
			}
			
			return $domxmlelement;
		}
	}
?>