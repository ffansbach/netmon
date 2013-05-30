<?php
	require_once('../../lib/classes/core/Event.class.php');

	class Eventlist {
		private $eventlist = array();
		
		public function __construct($object=false, $object_id=false, $action=false) {
			$result = array();
			if($object!=false AND $object_id!=false AND $action!=false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.object=? AND events.object_id=? AND events.action=?");
					$stmt->execute(array($object, $object_id, $action));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($object!=false AND $object_id!=false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.object=? AND events.object_id=?");
					$stmt->execute(array($object, $object_id));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($object!=false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.object=?");
					$stmt->execute(array($object));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}  elseif($action!=false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events
														WHERE events.action=?");
					$stmt->execute(array($action));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				try {
					$stmt = DB::getInstance()->prepare("SELECT events.id as event_id
														FROM events");
					$stmt->execute(array());
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
			foreach($this->eventlist as $eventlist) {
				$domxmlelement->appendChild($eventlist->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>