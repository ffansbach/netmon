<?php
	require_once(ROOT_DIR.'/lib/core/EventNotification.class.php');
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	
	class EventNotificationList extends ObjectList {
		private $event_notification_list = array();
		
		public function __construct($user_id=false, $action=false, $object=false,
									$notify=false, $notified=false,
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
													FROM event_notifications
													WHERE
														(user_id = :user_id OR :user_id=0) AND
														(action = :action OR :action='') AND
														(object = :object OR :object=0) AND
														(notify = :notify OR :notify=0) AND
														(notified = :notified OR :notified=0)");
				$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindParam(':action', $action, PDO::PARAM_STR);
				$stmt->bindParam(':object', $object, PDO::PARAM_INT);
				$stmt->bindParam(':notify', $notify, PDO::PARAM_INT);
				$stmt->bindParam(':notified', $notified, PDO::PARAM_INT);
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
				$stmt = DB::getInstance()->prepare("SELECT event_notifications.id as event_notification_id, event_notifications.*
													FROM event_notifications
													WHERE
														(user_id = :user_id OR :user_id=0) AND
														(action = :action OR :action='') AND
														(object = :object OR :object=0) AND
														(notify = :notify OR :notify=0) AND
														(notified = :notified OR :notified=0)
													ORDER BY
														case :sort_by
																when 'event_notification_id' then event_notifications.id
																else NULL
														end
													".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindParam(':action', $action, PDO::PARAM_STR);
				$stmt->bindParam(':object', $object, PDO::PARAM_INT);
				$stmt->bindParam(':notify', $notify, PDO::PARAM_INT);
				$stmt->bindParam(':notified', $notified, PDO::PARAM_INT);
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
				$event_notification = new EventNotification((int)$item['event_notification_id']);
				$event_notification->fetch();
				$this->event_notification_list[] = $event_notification;
			}
		}
		
		public function delete() {
			foreach($this->getEventNotificationList() as $item) {
				$item->delete();
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