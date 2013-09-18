<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/Event.class.php');

	class Eventlist extends ObjectList {
		private $eventlist = array();
		
		public function __construct() {
		
		}
		
		//TODO:	allow fetching an eventlist by user_id.
		//		fetch ids of object of the user first
		//		then fetch events of these objects and merge the lists.
		//		then sort by create_date
		public function init($object=false, $object_id=false, $action=false,
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
				//if limit -1 then get all ressource records
				if($this->getLimit()==-1)
					$this->setLimit($this->getTotalCount());
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.object=:object AND events.object_id=:object_id AND events.action=:action
														ORDER BY
															case :sort_by
																when 'create_date' then events.create_date
																else NULL
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
																else NULL
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
				//if limit -1 then get all ressource records
				if($this->getLimit()==-1)
					$this->setLimit($this->getTotalCount());
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.object=:object
														ORDER BY
															case :sort_by
																when 'create_date' then events.create_date
																else NULL
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
				//if limit -1 then get all ressource records
				if($this->getLimit()==-1)
					$this->setLimit($this->getTotalCount());
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.action=:action
														ORDER BY
															case :sort_by
																when 'create_date' then events.create_date
																else NULL
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
				//if limit -1 then get all ressource records
				if($this->getLimit()==-1)
					$this->setLimit($this->getTotalCount());
					
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														ORDER BY
															case :sort_by
																when 'create_date' then events.create_date
																else NULL
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
				$event = new Event((int)$event['event_id'], 0);
				$event->fetch();
				$this->eventlist[] = $event;
			}
		}
		
		public function setEventlist($eventlist) {
			if(is_array($eventlist)) {
				$this->eventlist = $eventlist;
			}
		}
		
		public function getEventlist() {
			return $this->eventlist;
		}
		
		/**
		 * Inserts an event or and eventlist to the eventlist.
		 * The Attribute offset will be set to 0 and limit will be recalculated.
		 * @param $event can be an Object of type Event or an Object of Type Eventlist.
		 *				 If the Object is of type Eventlist, the optional $index param is ignored
		 * @param $index Specifies an optional index where the Object of type Event should be inserted
		 */
		public function add($event, $index=false) {
			if($event instanceof Event) {
				if($index==false) {
					array_push($this->eventlist, $event);
				} elseif(is_int($index)) {
					array_splice($this->eventlist, $index, 0, $event);
				}
			} elseif ($event instanceof Eventlist) {
				$this->setEventlist(array_merge($this->getEventlist(), $event->getEventlist()));
			}
			
			$this->setOffset(0);
			$this->setLimit(count($this->eventlist));
		}
		
		public function store() {
			foreach($this->getEventlist() as $event) {
				$event->store();
			}
		}
		
		public function sort($sort, $order) {
			$tmp = array();
			
			$eventlist = $this->getEventlist();
			foreach($eventlist as $key=>$event) {
				switch($sort) {
					case 'create_date':		$tmp[$key] = $event->getCreateDate();
											break;
					default:				$tmp[$key] = $event->getCreateDate();
											break;
				}
			}
			
			if($order == 'asc')
				array_multisort($tmp, SORT_ASC, $eventlist);
			elseif($order == 'desc')
				array_multisort($tmp, SORT_DESC, $eventlist);
			
			$new_eventlist = array();
			for($i=0; $i<count($eventlist); $i++) {
				if(!empty($eventlist[$i])) {
					$new_eventlist[] = $eventlist[$i];
				}
			}
			
			$this->setEventlist($new_eventlist);
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('eventlist');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->getEventlist() as $eventlist) {
				$domxmlelement->appendChild($eventlist->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>