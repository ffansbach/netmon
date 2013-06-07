<?php
	require_once('lib/classes/core/EventNotification.class.php');

	class EventNotificationList {
		private $event_notification_list = array();
		
		public function __construct($user_id=false) {
			$result = array();
			if($user_id!=false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT id as event_notification_id
														FROM event_notifications
														WHERE user_id=?");
					$stmt->execute(array($user_id));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				try {
					$stmt = DB::getInstance()->prepare("SELECT id as event_notification_id
														FROM event_notifications");
					$stmt->execute(array());
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			foreach($result as $event_notification) {
				$this->event_notification_list[] = new EventNotification((int)$event_notification['event_notification_id']);
			}
		}
		
		public function getEventNotificationList() {
			return $this->event_notification_list;
		}
		
		public function notify() {
			foreach($this->getEventNotificationList() as $event_notification) {
				$event_notification->notify();
			}
		}
	}
?>