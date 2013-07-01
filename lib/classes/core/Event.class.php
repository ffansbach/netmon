<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	
	class Event extends Object {
		private $event_id = 0;
		private $crawl_cycle_id = 0;
		private $object = "";
		private $object_id = 0;
		private $action="";
		private $data = null;
		
		public function __construct($event_id=false, $crawl_cycle_id=false, $object=false, $object_id=false,
									$action=false, $data=false, $create_date=false, $update_date=false) {
			$this->setEventId($event_id);
			$this->setCrawlCycleId($crawl_cycle_id);
			$this->setObject($object);
			$this->setObjectId((int)$object_id);
			$this->setAction($action);
			$this->setData($data);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM events
													WHERE
														(id = :event_id OR :event_id=0) AND
														(crawl_cycle_id = :crawl_cycle_id OR :crawl_cycle_id=0) AND
														(object = :object OR :object='') AND
														(object_id = :object_id OR :object_id=0) AND
														(action = :action OR :action='') AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':event_id', $this->getEventId(), PDO::PARAM_INT);
				$stmt->bindParam(':crawl_cycle_id', $this->getCrawlCycleId(), PDO::PARAM_INT);
				$stmt->bindParam(':object', $this->getObject(), PDO::PARAM_STR);
				$stmt->bindParam(':object_id', $this->getObjectId(), PDO::PARAM_INT);
				$stmt->bindParam(':action', $this->getAction(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setEventId((int)$result['id']);
				$this->setCrawlCycleId((int)$result['crawl_cycle_id']);
				$this->setObject($result['object']);
				$this->setObjectId((int)$result['object_id']);
				$this->setAction($result['action']);
				$this->setData($result['data']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->crawl_cycle_id != 0 AND $this->object != "" AND $this->object_id != 0 AND $this->action != "" AND $this->data != null) {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO events (crawl_cycle_id, object, object_id, action, create_date, update_date, data)
														VALUES (?, ?, ?, ?, NOW(), NOW(), ?)");
					$stmt->execute(array($this->crawl_cycle_id, $this->object, $this->object_id, $this->action, $this->data));
					return DB::getInstance()->lastInsertId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			return false;
		}
		
		public function delete() {
			if($this->getEventId() != 0) {
				try {
 					$stmt = DB::getInstance()->prepare("DELETE FROM events WHERE id=?");
					$stmt->execute(array($this->getEventId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				return false;
			}
		}
		
		public function setEventId($event_id) {
			if(is_int($event_id))
				$this->event_id = $event_id;
		}
		
		public function setCrawlCycleId($crawl_cycle_id=false) {
			if($crawl_cycle_id == false)
				$this->crawl_cycle_id = 1; //TODO: set current crawl_cycle_id
			else if(is_int($crawl_cycle_id))
				$this->crawl_cycle_id = $crawl_cycle_id;
		}
		
		public function setObject($object) {
			if(is_string($object))
				$this->object = $object;
		}
		
		public function setObjectId($object_id) {
			if(is_int($object_id))
				$this->object_id = $object_id;
		}
		
		public function setAction($action) {
			if(is_string($action))
				$this->action = $action;
		}
		
		public function setData($data) {
				if(@unserialize($data)!==false)
					$this->data = $data;
				else
					$this->data = serialize($data);
		}
		
		public function getEventId() {
			return $this->event_id;
		}
		
		public function getCrawlCycleId() {
			return $this->crawl_cycle_id;
		}
		
		public function getObject() {
			return $this->object;
		}
		
		public function getObjectId() {
			return $this->object_id;
		}
		
		public function getAction() {
			return $this->action;
		}
		
		public function getData() {
			return unserialize($this->data);
		}
		
/*		public function getObjectData() {
			if($this->getObject()=='router') {
				$router = new Router($this->getObjectId());
				$router->fetch();
				return $router;
			}
		}*/
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('event');
			$domxmlelement->appendChild($domdocument->createElement("event_id", $this->getEventId()));
			$domxmlelement->appendChild($domdocument->createElement("object", $this->getObject()));
			$domxmlelement->appendChild($domdocument->createElement("object_id", $this->getObjectId()));
			$domxmlelement->appendChild($domdocument->createElement("action", $this->getAction()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			
			$data = $domdocument->createElement("data");
			$this->fromMixed($this->getData(), $data, $domdocument);
			$domxmlelement->appendChild($data);
			
			return $domxmlelement;
		}
		
		private function fromMixed($mixed, DOMElement $domElement = null, $domdocument) {
			if (is_array($mixed)) {
				foreach( $mixed as $index => $mixedElement ) {
					if ( is_int($index) ) {
						if ( $index == 0 ) {
							$node = $domElement;
						} else {
							$node = $domdocument->createElement($domElement->tagName);
							$domElement->parentNode->appendChild($node);
						}
					} else {
						$node = $domdocument->createElement($index);
						$domElement->appendChild($node);
					}
					
					$this->fromMixed($mixedElement, $node, $domdocument);
				}
			} else {
				$domElement->appendChild($domdocument->createTextNode($mixed));
			}
		}
	}
?>