<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');
	
	class ConfigLineList Extends ObjectList {
		private $config_line_list = array();
		
		public function __construct($offset=false, $limit=false, $sort_by=false, $order=false) {
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
													FROM config");
				$stmt->execute();
				$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			$this->setTotalCount((int)$total_count['total_count']);
			//if limit -1 then get all configs
			if($this->getLimit()==-1)
				$this->setLimit($this->getTotalCount());
			
			try {
				$stmt = DB::getInstance()->prepare("SELECT config.id as config_id
													FROM config
													ORDER BY
														case :sort_by
															when 'create_date' then config.create_date
															else config.id
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
			
			foreach($result as $config_line) {
				$config_line = new ConfigLine((int)$config_line['config_id']);
				$config_line->fetch();
				$this->config_line_list[] = $config_line;
			}
		}
		
		public function delete() {
			foreach($this->getConfigLineList() as $config_line) {
				$config_line->delete();
			}
		}
		
		public function getConfigLineList() {
			return $this->config_line_list;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('configlist');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->config_line_list as $config_line) {
				$domxmlelement->appendChild($config_line->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>