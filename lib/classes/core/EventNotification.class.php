<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Routerlist.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/User.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/ConfigLine.class.php');
	require_once(ROOT_DIR.'/lib/classes/extern/xmpphp/XMPP.php');
	
	class EventNotification extends Object {
		private $event_notification_id = 0;
		private $user_id = 0;
		private $action="";
		private $object="";
		private $object_data=null;
		private $notify=false;
		private $notified=false;
		private $notification_date=0;
	
		public function __construct($event_notification_id=false, $user_id=false, $action=false, $object=false, $notify=false, $notified=false, $create_date=false, $notification_date=false) {
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
				$this->setUserId((int)$result['user_id']);
				$this->setCreateDate($result['create_date']);
				$this->setAction($result['action']);
				$this->setObject($result['object']);
				$this->setNotify((bool)$result['notify']);
				$this->setNotified((bool)$result['notified']);
				$this->setNotificationDate($result['notification_date']);
				
				if($this->getAction() == 'router_offline') {
					$this->setObjectData(new Router($this->getObject()));
				}
			}
		}
		
		public function store() {
			if($this->getEventNotificationId() != 0) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE event_notifications SET
																user_id = ?,
																create_date = FROM_UNIXTIME(?),
																action = ?,
																object = ?,
																notify = ?,
																notified = ?,
																notification_date = FROM_UNIXTIME(?)
														WHERE id=?");
					$stmt->execute(array($this->getUserId(), $this->getCreateDate(), $this->getAction(), $this->getObject(),
										 $this->getNotify(), $this->getNotified(), $this->getNotificationDate(),
										 $this->getEventNotificationId()));
					$result = $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getUserId() != 0 AND $this->getAction()!="" AND $this->getCreateDate() != 0) {
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
		
		public function notify() {
			//check if notification has not already been send
			if($this->getNotify() == true) {
				//check which event to test
				if($this->getAction() == 'router_offline') {
					$router = new Router((int)$this->getObject());
					
					$online = false;
					$statusdata_history = $router->getStatusdataHistory()->getRouterStatusList();
					foreach($statusdata_history as $key=>$statusdata) {
						if ($statusdata->getStatus() == 'online') {
							$online = true;
							break;
						} elseif($key>=6) {
							break;
						}
					}
					
					if(!$online AND $this->getNotified() == 0) {
						//if router is marked as offline in each of the 6 last crawl cycles, then
						//send a notification
						$this->notifyRouterOffline($router, $statusdata_history[6]->getCreateDate());
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
			$message .= "Statusseite ansehen:\n$GLOBALS[url_to_netmon]/router_status.php?router_id=".$router->getRouterId()."\n\n";
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
			if($notification_date === false)
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