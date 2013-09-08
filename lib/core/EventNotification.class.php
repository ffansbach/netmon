<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/core/Routerlist.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');
	require_once(ROOT_DIR.'/lib/extern/xmpphp/XMPP.php');
	
	class EventNotification extends Object {
		private $event_notification_id = 0;
		private $user_id = 0;
		private $action="";
		private $object="";
		private $object_data=null;
		private $notify=0;
		private $notified=0;
		private $notification_date=0;
	
		public function __construct($event_notification_id=false, $user_id=false, $action=false, $object=false,
									$notify=false, $notified=false, $notification_date=false,
									$create_date=false, $update_date=false) {
			$this->setEventNotificationId($event_notification_id);
			$this->setUserId($user_id);
			$this->setAction($action);
			$this->setObject($object);
			$this->setNotify($notify);
			$this->setNotified($notified);
			$this->setNotificationDate($notification_date);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM event_notifications
													WHERE
														(id = :event_notification_id OR :event_notification_id=0) AND
														(user_id = :user_id OR :user_id=0) AND
														(action = :action OR :action='') AND
														(object = :object OR :object='') AND
														(notify = :notify OR :notify=0) AND
														(notified = :notified OR :notified=0) AND
														(notification_date = :notification_date OR :notification_date=0) AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':event_notification_id', $this->getEventNotificationId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':action', $this->getAction(), PDO::PARAM_STR);
				$stmt->bindParam(':object', $this->getObject(), PDO::PARAM_STR);
				$stmt->bindParam(':notify', $this->getNotify(), PDO::PARAM_INT);
				$stmt->bindParam(':notified', $this->getNotified(), PDO::PARAM_INT);
				$stmt->bindParam(':notification_date', $this->getNotificationDate(), PDO::PARAM_INT);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setEventNotificationId((int)$result['id']);
				$this->setUserId((int)$result['user_id']);
				$this->setAction($result['action']);
				$this->setObject($result['object']);
				$this->setNotify((int)$result['notify']);
				$this->setNotified((int)$result['notified']);
				$this->setNotificationDate($result['notification_date']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				if($this->getAction() == 'router_offline') {
					$router = new Router((int)$this->getObject());
					$router->fetch();
					$this->setObjectData($router);
				}
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->getEventNotificationId() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE event_notifications SET
																user_id = ?,
																update_date = NOW(),
																action = ?,
																object = ?,
																notify = ?,
																notified = ?,
																notification_date = FROM_UNIXTIME(?)
														WHERE id=?");
					$stmt->execute(array($this->getUserId(), $this->getAction(), $this->getObject(),
										 $this->getNotify(), $this->getNotified(), $this->getNotificationDate(),
										 $this->getEventNotificationId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getUserId() != 0 AND $this->getAction()!="") {
				//check if there already exists an event for the given action, object and user_id
				$event_notification = new EventNotification(false, $this->getUserId(), $this->getAction(), $this->getObject());
				if($event_notification->fetch) {
					try {
						$stmt = DB::getInstance()->prepare("INSERT INTO event_notifications (user_id, create_date, update_date, action, object, notify, notified, notification_date)
															VALUES (?, NOW(), NOW(), ?, ?, ?, ?, ?)");
						$stmt->execute(array($this->getUserId(), $this->getAction(), $this->getObject(), $this->getNotify(), $this->getNotified(), $this->getNotificationDate()));
						return DB::getInstance()->lastInsertId();
					} catch(PDOException $e) {
						echo $e->getMessage();
						echo $e->getTraceAsString();
					}
				}
			}
			
			return false;
		}
		
		public function delete() {
			try {
				$stmt = DB::getInstance()->prepare("DELETE FROM event_notifications WHERE id=?");
				$stmt->execute(array($this->getEventNotificationId()));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			return true;
		}
		
		public function notify() {
			//check if notification has not already been send
			if($this->getNotify() == true) {
				//check which event to test
				if($this->getAction() == 'router_offline') {
					$crawl_cycles = ConfigLine::configByName('event_notification_router_offline_crawl_cycles');
					$router = new Router((int)$this->getObject());
					$router->fetch();
					
					$online = false;
					$statusdata_history = $router->getStatusdataHistory()->getRouterStatusList();
					foreach($statusdata_history as $key=>$statusdata) {
						if ($statusdata->getStatus() == 'online') {
							$online = true;
							break;
						} elseif($key>=$crawl_cycles) {
							break;
						}
					}
					
					if(!$online AND $this->getNotified() == 0) {
						//if router is marked as offline in each of the $crawl_cycles last crawl cycles, then
						//send a notification
						$this->notifyRouterOffline($router, $statusdata_history[$crawl_cycles]->getCreateDate());
						//store into database that the router has been notified
						$this->setNotified(1);
						$this->setNotificationDate(time());
						$this->store();
					} elseif($online AND $this->getNotified() == 1) {
						//if the router has been notified but is not offline anymore, then reset notification
						$this->setNotified(0);
						$this->store();
					}
				} elseif($this->getAction() == 'network_down') {
				
				}
			}
		}
		
		public function notifyRouterOffline($router, $datetime) {
			$user = new User($router->getUserId());

			$message = "Hallo ".$user->getNickname().",\n\n";
			$message .= "dein Router ".$router->getHostname()." ist seit dem ".date("d.m H:i", $datetime)." Uhr offline.\n";
			$message .= "Bitte stelle den Router zur Erhaltung des Freifunknetzwerkes wieder zur Verfuegung oder entferne den Router aus Netmon.\n\n";
			$message .= "Statusseite ansehen:\n$GLOBALS[url_to_netmon]/router.php?router_id=".$router->getRouterId()."\n\n";
			$message .= "Router bearbeiten/entfernen:\n$GLOBALS[url_to_netmon]/routereditor.php?section=edit&router_id=".$router->getRouterId()."\n\n";
			$message .= "Liebe Gruesse\n";
			$message .= "$GLOBALS[community_name]";
			$this->sendNotification($user, "Freifunk Router ".$router->getHostname()." offline", $message);
		}
		
		public function sendNotification($user, $subject, $message) {
			if($user->getNotificationMethod() == 'jabber') {

				$conn = new XMPPHP_XMPP(ConfigLine::configByName('jabber_server'), 5222, ConfigLine::configByName('jabber_username'), ConfigLine::configByName('jabber_password'), 'xmpphp', $server=null, $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
				try {
					$conn->connect();
					$conn->processUntil('session_start');
					$conn->presence();
					$conn->message($user->getJabber(), $message);
					$conn->disconnect();
				} catch(XMPPHP_Exception $e) {
					die($e->getMessage());
				}
			} elseif($user->getNotificationMethod() == 'email') {
				if ($GLOBALS['mail_sending_type']=='smtp') {
					$config['username'] = ConfigLine::configByName('mail_smtp_username');
					$config['password'] = ConfigLine::configByName('mail_smtp_password');
					$config['ssl'] = ConfigLine::configByName('mail_smtp_ssl');
					$config['auth'] = ConfigLine::configByName('mail_smtp_login_auth');
					
					$transport = new Zend_Mail_Transport_Smtp(ConfigLine::configByName('mail_smtp_server'), $config);
				}
				
				$mail = new Zend_Mail();
				$mail->setFrom(ConfigLine::configByName('mail_sender_adress'), ConfigLine::configByName('mail_sender_name'));
				$mail->addTo($user->getEmail());
				$mail->setSubject($subject);
				$mail->setBodyText($message);
				$mail->send($transport);
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
			if(is_string($object))
				$this->object = $object;
		}
		
		public function setObjectData($object_data) {
			$this->object_data = $object_data;
		}
		
		public function setNotify($notify) {
			if(is_bool($notify) OR $notify==1 OR $notify==0)
				$this->notify = $notify;
		}
		
		public function setNotified($notified) {
			if(is_bool($notified) OR $notified==1 OR $notified==0)
				$this->notified = $notified;
		}
		
		public function setNotificationDate($notification_date) {
			if(is_string($notification_date)) {
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
		
		public function getObjectData() {
			return $this->object_data;
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