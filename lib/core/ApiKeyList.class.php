<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/ApiKey.class.php');

	class ApiKeyList extends ObjectList {
		public function __construct($object_id=false, $object_type=false,
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
													FROM api_keys
													WHERE
														(object_id = :object_id OR :object_id=0) AND
														(object_type = :object_type OR :object_type='')");
				$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
				$stmt->bindParam(':object_type', $object_type, PDO::PARAM_STR);
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
				$stmt = DB::getInstance()->prepare("SELECT a.id as api_key_id, a.*
													FROM api_keys a
													WHERE
														(a.object_id = :object_id OR :object_id=0) AND
														(a.object_type = :object_type OR :object_type='')
													ORDER BY 
														case :sort_by
															when 'api_key_id' then a.id
															else NULL
														end
														".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
				$stmt->bindParam(':object_type', $object_type, PDO::PARAM_STR);
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
				$this->list[] = new ApiKey((int)$item['api_key_id'], $item['api_key'], (int)$item['object_id'],
										   $item['object_type'], $item['description'], $item['create_date'], $item['update_date']);
			}
		}
	}
?>