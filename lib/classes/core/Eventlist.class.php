<?php
	require_once(ROOT_DIR.'/lib/classes/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Event.class.php');

	class Eventlist extends ObjectList {
		private $eventlist = array();
		
		public function __construct($object=false, $object_id=false, $action=false,
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
			else
				$this->SetOrder("desc");
			
			if($object!=false AND $object_id!=false AND $action!=false) {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM events
														WHERE events.object=? AND events.object_id=? AND events.action=?");
					$stmt->execute(array($object, $object_id, $action));
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.object=:object AND events.object_id=:object_id AND events.action=:action
														ORDER BY
															case :sort_by
																when 'create_date' then events.create_date
																else events.id
															end
														".$this->getOrder()."
														LIMIT :offset, :limit");
					$stmt->bindParam(':object', $object, PDO::PARAM_STR);
					$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
					$stmt->bindParam(':action', $action, PDO::PARAM_STR);
					$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
					$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
					$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
					$stmt->execute();
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($object!=false AND $object_id!=false) {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM events
														WHERE events.object=? AND events.object_id=?");
					$stmt->execute(array($object, $object_id));
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.object=:object AND events.object_id=:object_id
														ORDER BY
															case :sort_by
																when 'create_date' then events.create_date
																else events.id
															end
														".$this->getOrder()."
														LIMIT :offset, :limit");
					$stmt->bindParam(':object', $object, PDO::PARAM_STR);
					$stmt->bindParam(':object_id', $object_id, PDO::PARAM_INT);
					$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
					$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
					$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
					$stmt->execute();
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($object!=false) {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM events
														WHERE events.object=?");
					$stmt->execute(array($object));
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.object=:object
														ORDER BY
															case :sort_by
																when 'create_date' then events.create_date
																else events.id
															end
														".$this->getOrder()."
														LIMIT :offset, :limit");
					$stmt->bindParam(':object', $object, PDO::PARAM_STR);
					$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
					$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
					$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
					$stmt->execute();
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}  elseif($action!=false) {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM events
														WHERE events.action=?");
					$stmt->execute(array($action));
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.action=:action
														ORDER BY
															case :sort_by
																when 'create_date' then events.create_date
																else events.id
															end
														".$this->getOrder()."
														LIMIT :offset, :limit");
					$stmt->bindParam(':action', $action, PDO::PARAM_STR);
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
														FROM events");
					$stmt->execute(array($action));
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														ORDER BY
															case :sort_by
																when 'create_date' then events.create_date
																else events.id
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
			
			foreach($result as $event) {
				$this->eventlist[] = new Event((int)$event['event_id']);
			}
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('eventlist');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->eventlist as $eventlist) {
				$domxmlelement->appendChild($eventlist->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>