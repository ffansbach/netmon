<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterfacelist.class.php');
	require_once(ROOT_DIR.'/lib/core/EventNotificationList.class.php');
	require_once(ROOT_DIR.'/lib/core/OriginatorStatusList.class.php');
	require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/RouterStatusList.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	require_once(ROOT_DIR.'/lib/core/Chipset.class.php');
	require_once(ROOT_DIR.'/lib/core/Event.class.php');
	require_once(ROOT_DIR.'/lib/core/ApiKeyList.class.php');
	
	class Router extends Object {
 		private $router_id = 0;
		private $user_id = 0;
		private $hostname = "";
		private $description = "";
		private $location = "";
		private $latitude = "";
		private $longitude = "";
		private $chipset_id = 0;
		private $crawl_method = "";
		private $mac = "";
		
		private $user = null;
		private $statusdata = null;
		private $chipset = null;
		
		public function __construct($router_id=false, $user_id=false, $hostname=false, $description=false,
									$location=false, $latitude=false, $longitude=false, $chipset_id=false,
									$crawl_method=false, $create_date=false, $update_date=false, $mac=false) {
			$this->setRouterId($router_id);
			$this->setUserId($user_id);
			$this->setHostname($hostname);
			$this->setDescription($description);
			$this->setLocation($location);
			$this->setLatitude($latitude);
			$this->setLongitude($longitude);
			$this->setChipsetId($chipset_id);
			$this->setCrawlMethod($crawl_method);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
			$this->setMac($mac);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT routers.id as router_id, routers.*
													FROM routers
													WHERE
														(id = :router_id OR :router_id=0) AND
														(user_id = :user_id OR :user_id=0) AND
														(hostname = :hostname OR :hostname='') AND
														(description = :description OR :description='') AND
														(location = :location OR :location='') AND
														(latitude = :latitude OR :latitude='') AND
														(longitude = :longitude OR :longitude='') AND
														(chipset_id = :chipset_id OR :chipset_id=0) AND
														(crawl_method = :crawl_method OR :crawl_method='') AND
														(create_date = :create_date OR :create_date=0) AND
														(update_date = :update_date OR :update_date=0) AND
														(router_auto_assign_login_string = :mac OR :mac='')");
				$stmt->bindParam(':router_id', $this->getRouterId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_STR);
				$stmt->bindParam(':hostname', $this->getHostname(), PDO::PARAM_STR);
				$stmt->bindParam(':description', $this->getDescription(), PDO::PARAM_STR);
				$stmt->bindParam(':location', $this->getLocation(), PDO::PARAM_STR);
				$stmt->bindParam(':latitude', $this->getLatitude(), PDO::PARAM_STR);
				$stmt->bindParam(':longitude', $this->getLongitude(), PDO::PARAM_STR);
				$stmt->bindParam(':chipset_id', $this->getChipsetId(), PDO::PARAM_INT);
				$stmt->bindParam(':crawl_method', $this->getCrawlMethod(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':mac', $this->getMac(), PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setRouterId((int)$result['router_id']);
				$this->setUserId((int)$result['user_id']);
				$this->setHostname($result['hostname']);
				$this->setDescription($result['description']);
				$this->setLocation($result['location']);
				$this->setLatitude($result['latitude']);
				$this->setLongitude($result['longitude']);
				$this->setChipsetId((int)$result['chipset_id']);
				$this->setCrawlMethod($result['crawl_method']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				$this->setMac($result['router_auto_assign_login_string']);
				$this->setUser($this->getUserId());
				$this->setChipset($this->getChipsetId());
				$this->setStatusdata($this->getRouterId());
				return true;
			}
			return false;
		}
		
		public function store() {
			if($this->getUserId() != 0 AND $this->getHostname() != "" AND $this->getCrawlMethod() != "" AND $this->getChipsetId() != 0) {
				$router_test = new Router(false, false, $this->getHostname());
				$router_test->fetch();
				
				if($this->getRouterId() != 0 AND !($router_test->getRouterId()!=$this->getRouterId() AND $router_test->getHostname()==$this->getHostname())) {
					try {
						$stmt = DB::getInstance()->prepare("UPDATE routers SET
																	user_id = ?,
																	hostname = ?,
																	description = ?,
																	location = ?,
																	latitude = ?,
																	longitude = ?,
																	chipset_id = ?,
																	crawl_method = ?,
																	update_date = NOW()
															WHERE id=?");
						$stmt->execute(array($this->getUserId(), $this->getHostname(), $this->getDescription(), $this->getLocation(),
											$this->getLatitude(), $this->getLongitude(), $this->getChipsetId(), $this->getCrawlMethod(),
											$this->getRouterId()));
						return $stmt->rowCount();
					} catch(PDOException $e) {
						echo $e->getMessage();
						echo $e->getTraceAsString();
					}
				} elseif($router_test->getRouterId()==0) {
					try {
						$stmt = DB::getInstance()->prepare("INSERT INTO routers (user_id, hostname, description, location, 
																				latitude, longitude, chipset_id, crawl_method,
																				create_date, update_date)
															VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
						$stmt->execute(array($this->getUserId(), $this->getHostname(), $this->getDescription(), $this->getLocation(),
											$this->getLatitude(), $this->getLongitude(), $this->getChipsetId(), $this->getCrawlMethod()));
						$this->setRouterId((int)DB::getInstance()->lastInsertId());
						
						//create event for new router
						$event = new Event(false, false, 'router', $this->getRouterId(), 'new', array('hostname'=>$router->getHostname()));
						$event->store();
						
						return $this->getRouterId();
					} catch(PDOException $e) {
						echo $e->getMessage();
						echo $e->getTraceAsString();
					}
				}
			}
			return false;
		}
		
		public function delete() {
			if($this->getRouterId() != 0) {
				//delete all interfaces
				$networkinterfacelist = new Networkinterfacelist(false, $this->getRouterId());
				$networkinterfacelist->delete();
				
				//delete originator statusses
				$originator_status_list = new OriginatorStatusList($this->getRouterId());
				$originator_status_list->delete();
				
				//delete batman advanced interfaces
				try {
					$stmt = DB::getInstance()->prepare("DELETE FROM crawl_batman_advanced_interfaces WHERE router_id=?");
					$stmt->execute(array($this->getRouterId()));
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				
				//delete router statusses
				$router_status_list = new RouterStatusList($this->getRouterId());
				$router_status_list->delete();
				
				//delete event notifications (we need to delete all notifications that users created for this router
				//thats why we need a list here)
				$event_notification_list = new EventNotificationList(false, "router_offline", $this->getRouterId());
				$event_notification_list->delete();
				
				//Delete api keys
				$api_key_list = new ApiKeyList($this->getRouterId(), 'router');
				$api_key_list->delete();
				
				//delete router
				try {
 					$stmt = DB::getInstance()->prepare("DELETE FROM routers WHERE id=?");
					$stmt->execute(array($this->getRouterId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			return false;
		}
		
		public function setRouterId($router_id) {
			if(is_int($router_id)) {
				$this->router_id = $router_id;
				return true;
			}
			return false;
		}
		
		public function setUserId($user_id) {
			if(is_int($user_id)) {
				$this->user_id = $user_id;
				return true;
			}
			return false;
		}
		
		public function setHostname($hostname) {
			//check for valid hostname as specified in rfc 1123
			//see http://stackoverflow.com/a/3824105
			$regex = "/^([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])(\.([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]{0,61}[a-zA-Z0-9]))*$/";
			if(is_string($hostname) AND strlen($hostname)<=255 AND preg_match($regex, $hostname)) {
				$this->hostname = $hostname;
				return true;
			}
			return false;
		}
		
		public function setDescription($description) {
			if(is_string($description)) {
				$this->description = $description;
				return true;
			}
			return false;
		}
		
		public function setLocation($location) {
			if(is_string($location)) {
				$this->location = $location;
				return true;
			}
			return false;
		}
		
		public function setLatitude($latitude) {
			if(is_string($latitude)) {
				$this->latitude = $latitude;
				return true;
			}
			return false;
		}
		
		public function setLongitude($longitude) {
			if(is_string($longitude)) {
				$this->longitude = $longitude;
				return true;
			}
			return false;
		}
		
		public function setChipsetId($chipset_id) {
			if(is_int($chipset_id)) {
				$this->chipset_id = $chipset_id;
				return true;
			}
			return false;
		}
		
		public function setCrawlMethod($crawl_method) {
			if($crawl_method=="router" OR $crawl_method=="crawler") {
				$this->crawl_method = $crawl_method;
				return true;
			}
			return false;
		}
		
		public function setUser($user) {
			if($user instanceof User) {
				$this->user = $user;
				return true;
			} elseif(is_int($user)) {
				$user = new User($user);
				if($user->fetch()) {
					$this->user = $user;
					return true;
				}
			}
			return false;
		}
		
		public function setChipset($chipset) {
			if($chipset instanceof Chipset) {
				$this->chipset = $chipset;
				return true;
			} else {
				$chipset = new Chipset($chipset);
				if($chipset->fetch()) {
					$this->chipset = $chipset;
					return true;
				}
			}
			return false;
		}
		
		public function setStatusdata($routerstatus) {
			if($routerstatus instanceof RouterStatus) {
				$this->statusdata = $routerstatus;
				return true;
			} elseif(is_int($routerstatus)) {
				$router_status = new RouterStatus(false, false, $routerstatus);
				if($router_status->fetch()) {
					$this->statusdata = $router_status;
					return true;
				}
			}
			return false;
		}
		
		public function setMac($mac) {
			if(is_string($mac)) {
				$this->mac = $mac;
				return true;
			}
			return false;
		}
		
		public function getRouterId() {
			return $this->router_id;
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		public function getHostname() {
			return $this->hostname;
		}
		
		public function getDescription() {
			return $this->description;
		}
		
		public function getLocation() {
			return $this->location;
		}
		
		public function getLatitude() {
			return $this->latitude;
		}
		
		public function getLongitude() {
			return  $this->longitude;
		}
		
		public function getChipsetId() {
			return $this->chipset_id;
		}
		
		public function getCrawlMethod() {
			return $this->crawl_method;
		}
		
		public function getUser() {
			return $this->user;
		}
		
		public function getChipset() {
			return  $this->chipset;
		}
		
		public function getStatusdata() {
			return  $this->statusdata;
		}
		
		public function getMac() {
			return  $this->mac;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('router');
			$domxmlelement->appendChild($domdocument->createElement("router_id", $this->getRouterId()));
			$domxmlelement->appendChild($domdocument->createElement("user_id", $this->getUserId()));
			$domxmlelement->appendChild($domdocument->createElement("hostname", $this->gethostname()));
			$domxmlelement->appendChild($domdocument->createElement("description", $this->getDescription()));
			$domxmlelement->appendChild($domdocument->createElement("mac", $this->getMac()));
			$domxmlelement->appendChild($domdocument->createElement("location", $this->getLocation()));
			$domxmlelement->appendChild($domdocument->createElement("latitude", $this->getLatitude()));
			$domxmlelement->appendChild($domdocument->createElement("longitude", $this->getLongitude()));
			$domxmlelement->appendChild($domdocument->createElement("crawl_method", $this->getCrawlMethod()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			
			$domxmlelement->appendChild($this->getUser()->getDomXMLElement($domdocument));
			$domxmlelement->appendChild($this->getChipset()->getDomXMLElement($domdocument));
			$domxmlelement->appendChild($this->getStatusdata()->getDomXMLElement($domdocument));
			return $domxmlelement;
		}
	}
?>