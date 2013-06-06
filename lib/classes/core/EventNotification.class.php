<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
		
	class EventNotification extends Object {
		private $event_notification_id = 0;
		private $user_id = 0;
		private $action="";
		private $object="";
		private $notify=0;
		private $notified=0;
		private $notification_date=0;
	
		public function __construct($event_notification_id=false, $user_id=false, $action=false, $object=false, $create_date=false, $notify=false, $notified=false, $notification_date=false) {
			if($event_notification_id==false AND $user_id!=false AND $action!=false) {
				$this->setUserId((int)$user_id);
				$this->setCreateDate();
				$this->setAction($action);
				$this->setObject($object);
				$this->setNotify($notify);
				$this->setNotified($notified);
				$this->setNotificationDate($notification_date);
			} else if ($event_notification_id !== false AND is_int($event_notification_id)) {
				//fetch event data from database
				$result = array();
				try {
					$stmt = DB::getInstance()->prepare("SELECT *
														FROM event_notifications
														WHERE id = ?");
					$stmt->execute(array($event_notification_id));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				
				$this->setEventNotificationId((int)$result['id']);
				$this->setUserId($result['user_id']);
				$this->setCreateDate($result['create_date']);
				$this->setAction($result['action']);
				$this->setObject($result['object']);
				$this->setNotify($result['notify']);
				$this->setNotified($result['notified']);
				$this->setNotificationDate($result['notification_date']);
			}
		}
		
		public function store() {
			if($this->getUserId() != 0 AND $this->getAction()!="" AND $this->getCreateDate() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO event_notifications (user_id, create_date, action, object, notify, notified, notification_date)
														VALUES (?, FROM_UNIXTIME(?), ?, ?, ?, ?, ?)");
					$stmt->execute(array($this->getUserId(), $this->getCreateDate(), $this->getAction(), $this->getObject(), $this->getNotify(), $this->getNotified(), $this->getNotificationDate()));
					return DB::getInstance()->lastInsertId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				if($this->getAction() != 0) {
					echo "hallo";
				}
				
				echo $this->getUserId();
				echo "problem";
				echo  $this->getCreateDate();
				echo $this->getAction();
			}
			
			return false;
		}
		
		public static function delete($event_notification_id) {
			try {
				$stmt = DB::getInstance()->prepare("DELETE FROM event_notifications WHERE id=?");
				$stmt->execute(array($event_notification_id));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
		}
		
		public function setEventNotificationId($event_notification_id) {
			if(is_int($event_notification_id))
				$this->event_notification_id = $event_notification_id;
		}
		
		public function setUserId($user_id) {
			if(is_int($user_id))
				$this->user_id = $user_id;
		}
		
		public function setAction($action) {
			if(is_string($action))
				$this->action = $action;
		}
		
		public function setObject($object) {
			$this->object = $object;
		}
		
		public function setNotify($notify) {
			if(is_bool($notify))
				$this->notify = $notify;
		}
		
		public function setNotified($notified) {
			if(is_bool($notified))
				$this->notified = $notified;
		}
		
		public function setNotificationDate($notification_date) {
			if($notification_date == false)
				$this->notification_date = time();
			else if(is_string($notification_date)) {
				$date = new DateTime($notification_date);
				$this->notification_date = $date->getTimestamp();
			} else if(is_int($notification_date))
				$this->notification_date = $notification_date;
		}
		
		public function getEventNotificationId() {
				return $this->event_notification_id;
		}
		
		public function getUserId() {
				return $this->user_id;
		}
		
		public function getAction() {
				return $this->action;
		}
		
		public function getObject() {
			return $this->object;
		}
		
		public function getNotify() {
				return $this->notify;
		}
		
		public function getNotified() {
				return $this->notified;
		}
		
		public function getNotificationDate() {
				return $this->notification_date;
		}
	}

?>