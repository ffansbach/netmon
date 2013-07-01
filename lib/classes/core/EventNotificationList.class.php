<?php
	require_once('lib/classes/core/EventNotification.class.php');
	require_once('lib/classes/core/ObjectList.class.php');
	
	class EventNotificationList extends ObjectList {
		private $event_notification_list = array();
		
		public function __construct($user_id=false, $offset=false, $limit=false, $sort_by=false, $order=false) {
			$result = array();
			if($offset!==false)
				$this->setOffset((int)$offset);
			if($limit!==false)
				$this->setLimit((int)$limit);
			if($sort_by!==false)
				$this->setSortBy($sort_by);
			if($order!==false)
				$this->SetOrder($order);
				
			if($user_id!=false) {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM event_notifications
														WHERE event_notifications.user_id=?");
					$stmt->execute(array($user_id));
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT id as event_notification_id
														FROM event_notifications
														WHERE event_notifications.user_id = :user_id
														ORDER BY
															case :sort_by
																when 'create_date' then event_notifications.create_date
																else event_notifications.id
															end
														".$this->getOrder()."
														LIMIT :offset, :limit");
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
			} else {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM event_notifications");
					$stmt->execute(array($user_id));
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				try {
					$stmt = DB::getInstance()->prepare("SELECT id as event_notification_id
														FROM event_notifications
														ORDER BY
															case :sort_by
																when 'create_date' then event_notifications.create_date
																else event_notifications.id
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
			
			foreach($result as $event_notification) {
				$event_notification = new EventNotification((int)$event_notification['event_notification_id']);
				$event_notification->fetch();
				$this->event_notification_list[] = $event_notification;
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