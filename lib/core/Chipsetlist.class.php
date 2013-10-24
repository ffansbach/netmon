<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	
	class Chipsetlist extends ObjectList {
		private $list = array();
		
		public function __construct($name=false, $hardware_name=false,
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
													FROM chipsets
													WHERE
														(name = :name OR :name='') AND
														(hardware_name = :hardware_name OR :hardware_name='')");
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt->bindParam(':hardware_name', $hardware_name, PDO::PARAM_STR);
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
				$stmt = DB::getInstance()->prepare("SELECT c.id as chipset_id, c.*
													FROM chipsets c
													WHERE
														(c.name = :name OR :name='') AND
														(c.hardware_name = :hardware_name OR :hardware_name='')
													ORDER BY 
														case :sort_by
															when 'chipset_id' then c.id
															else NULL
														end
														".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt->bindParam(':hardware_name', $hardware_name, PDO::PARAM_STR);
				$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
				$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
				$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
				$stmt->execute();
				$resultset = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			foreach($resultset as $item) {
				$this->list[] = new Chipset((int)$item['chipset_id'], $item['name'],
										   $item['hardware_name'], $item['create_date'], $item['update_date']);
			}
		}
		
		public function getList() {
			return $this->list;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('chipsetlist');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->list as $item) {
				$domxmlelement->appendChild($item->getDomXMLElement($domdocument));
			}
			return $domxmlelement;
		}
	}
?>