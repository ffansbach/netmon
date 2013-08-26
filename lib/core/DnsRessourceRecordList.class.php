<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsRessourceRecord.class.php');
	
	class DnsRessourceRecordList extends ObjectList {
		private $dns_ressource_record_list = array();
		
		public function __construct($dns_zone_id=false, $user_id=false,
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
													FROM dns_ressource_records
													WHERE
														(dns_zone_id = :dns_zone_id OR :dns_zone_id=0) AND
														(user_id = :user_id OR :user_id=0)");
				$stmt->bindParam(':dns_zone_id', $dns_zone_id, PDO::PARAM_INT);
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
				$stmt = DB::getInstance()->prepare("SELECT id as dns_ressource_record_id
													FROM dns_ressource_records
													WHERE
														(dns_zone_id = :dns_zone_id OR :dns_zone_id=0) AND
														(user_id = :user_id OR :user_id=0)
													ORDER BY
														case :sort_by
															when 'create_date' then dns_ressource_records.create_date
															else dns_ressource_records.id
														end
													".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':dns_zone_id', $dns_zone_id, PDO::PARAM_INT);
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
			
			foreach($result as $dns_ressource_record) {
				$dns_ressource_record = new DnsRessourceRecord((int)$dns_ressource_record['dns_ressource_record_id']);
				$dns_ressource_record->fetch();
				$this->dns_ressource_record_list[] = $dns_ressource_record;
			}
		}
		
		public function add($item) {
			if($item instanceof DnsRessourceRecordList) {
				$this->setDnsRessourceRecordList(array_merge($this->getDnsRessourceRecordList(), $item->getDnsRessourceRecordList()));
				return true;
			} elseif($item instanceof DnsRessourceRecord) {
				$this->dns_ressource_record_list[] = $item;
				return true;
			}
			return false;
		}
		
		public function setDnsRessourceRecordList($dns_ressource_record_list) {
			if($dns_ressource_record_list instanceof DnsRessourceRecordList) {
				$this->dns_ressource_record_list = $dns_ressource_record_list;
				return true;
			}
			return false;
		}
		
		public function getDnsRessourceRecordList() {
			return $this->dns_ressource_record_list;
		}
		
		public function getNumberOfElements() {
			return count($this->dns_ressource_record_list);
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('dns_ressource_record_list');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->dns_ressource_record_list as $dns_ressource_record) {
				$domxmlelement->appendChild($dns_ressource_record->getDomXMLElement($domdocument));
			}
			
			return $domxmlelement;
		}
	}
?>