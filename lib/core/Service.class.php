<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/Ip.class.php');
	require_once(ROOT_DIR.'/lib/core/Iplist.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsRessourceRecord.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsRessourceRecordList.class.php');
	
	class Service extends Object {
		private $service_id = 0;
		private $user_id = 0;
		private $title = "";
		private $description = "";
		private $port = 0;
		private $visible = 0;
		
		private $iplist = null;
		private $dns_ressource_record_list = null;
		private $user = null;
		
		public function __construct($service_id=false, $user_id=false, $title=false, $description=false,
									$port=false, $visible=false, $iplist=false, $dns_ressource_record_list=false,
									$create_date=false, $update_date=false) {
			$this->setServiceId($service_id);
			$this->setUserId($user_id);
			$this->setTitle($title);
			$this->setDescription($description);
			$this->setPort($port);
			$this->setVisible($visible);
			
			$this->setIplist($iplist);
			$this->setDnsRessourceRecordList($dns_ressource_record_list);
			
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM services
													WHERE
														(id = :service_id OR :service_id=0) AND
														(user_id = :user_id OR :user_id=0) AND
														(title = :title OR :title='') AND
														(description = :description OR :description='') AND
														(port = :port OR :port=0) AND
														(description = :description OR :description=0) AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':service_id', $this->getServiceId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_INT);
				$stmt->bindParam(':title', $this->getTitle(), PDO::PARAM_STR);
				$stmt->bindParam(':description', $this->getDescription(), PDO::PARAM_STR);
				$stmt->bindParam(':port', $this->getPort(), PDO::PARAM_INT);
				$stmt->bindParam(':description', $this->getDescription(), PDO::PARAM_INT);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setServiceId((int)$result['id']);
				$this->setUserId((int)$result['user_id']);
				$this->setTitle($result['title']);
				$this->setDescription($result['description']);
				$this->setPort((int)$result['port']);
				$this->setVisible((int)$result['visible']);
				
				$this->setIplist();
				$this->setDnsRessourceRecordList();
				$this->setUser((int)$result['user_id']);
				
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			if($this->getServiceId() != 0 AND $this->getUserId()!=0 AND $this->getPort()!=0
			   AND ($this->getIplist()->getTotalCount()>0 OR $this->getDnsRessourceRecordList()->getTotalCount()>0)) {
					//Update Service
					//Remove all links
					//reinstall links
			} elseif($this->getServiceId() == 0 AND $this->getUserId()!=0 AND $this->getPort()!=0
					 AND ((is_object($this->getIplist()) AND $this->getIplist()->getNumberOfElements()>0)
					       OR (is_object($this->getDnsRessourceRecordList()) AND $this->getDnsRessourceRecordList()->getNumberOfElements()>0))) {
				//insert service
				$service_id = 0;
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO services (user_id, title, description, port, visible,
																						   create_date, update_date)
														VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
					$stmt->execute(array($this->getUserId(), $this->getTitle(), $this->getDescription(),
										 $this->getPort(), $this->getVisible()));
					$service_id =  DB::getInstance()->lastInsertId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				
				//insert links to ips
				if(is_object($this->getIplist())) {
					foreach($this->getIplist()->getIplist() as $ip) {
						try {
							$stmt = DB::getInstance()->prepare("INSERT INTO service_ips (service_id, ip_id, create_date, update_date)
																VALUES (?, ?, NOW(), NOW())");
							$stmt->execute(array($service_id, $ip->getIpId()));
						} catch(PDOException $e) {
							echo $e->getMessage();
							echo $e->getTraceAsString();
						}
					}
				}
				
				//insert links to dns_ressource_records
				if(is_object($this->getDnsRessourceRecordList())) {
					foreach($this->getDnsRessourceRecordList()->getDnsRessourceRecordList() as $dns_ressource_record) {
						try {
							$stmt = DB::getInstance()->prepare("INSERT INTO service_dns_ressource_records (service_id, dns_ressource_record_id, create_date, update_date)
																VALUES (?, ?, NOW(), NOW())");
							$stmt->execute(array($service_id, $dns_ressource_record->getDnsRessourceRecordId()));
						} catch(PDOException $e) {
							echo $e->getMessage();
							echo $e->getTraceAsString();
						}
					}
				}
				
				return $service_id;
			}
			
			return false;
		}
		
		public function delete() {
			// Delete links between service and ips
			try {
				$stmt = DB::getInstance()->prepare("DELETE FROM service_ips WHERE service_id=?");
				$stmt->execute(array($this->getServiceId()));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			// Delete links between service and dns_ressource_records
			try {
				$stmt = DB::getInstance()->prepare("DELETE FROM service_dns_ressource_records WHERE service_id=?");
				$stmt->execute(array($this->getServiceId()));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			// Delete service
			try {
				$stmt = DB::getInstance()->prepare("DELETE FROM services WHERE id=?");
				$stmt->execute(array($this->getServiceId()));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			return true;
		}
		
		// Setter methods
		public function setServiceId($service_i) {
			if(is_int($service_i)) {
				$this->service_id = $service_i;
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
		public function setTitle($title) {
			if(is_string($title)) {
				$this->title = $title;
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
		
		public function setPort($port) {
			if(is_int($port)) {
				$this->port = $port;
				return true;
			}
			return false;
		}
		
		public function setVisible($visible) {
			if(is_int($visible)) {
				$this->visible = $visible;
				return true;
			}
			return false;
		}
		
		public function setIplist($itemlist=false) {
			if($itemlist instanceof Iplist) {
				$this->iplist = $itemlist;
				return true;
			} elseif(is_array($itemlist)) {
				$iplist = new Iplist(false, false, 0, 0);
				foreach($itemlist as $ip_id) {
					$ip = new Ip((int)$ip_id);
					if($ip->fetch()) {
						$iplist->add($ip);
					}
				}
				$this->setIplist($iplist);
				return true;
			} elseif ($itemlist == false AND $this->getServiceId() != 0) {
				$result = array();
				try {
					$stmt = DB::getInstance()->prepare("SELECT ip_id
														FROM service_ips
														WHERE service_id = ?");
					$stmt->execute(array($this->getServiceId()));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				
				$iplist = new Iplist(false, false, 0, 0);
				foreach($result as $ip_id) {
					$ip = new Ip((int)$ip_id['ip_id']);
					if($ip->fetch()) {
						$iplist->add($ip);
					}
				}
				$this->setIplist($iplist);
				return true;
			}
			return false;
		}
		
		public function setDnsRessourceRecordList($itemlist=false) {
			if($itemlist instanceof DnsRessourceRecordList) {
				$this->dns_ressource_record_list = $itemlist;
				return true;
			} elseif(is_array($itemlist)) {
				$dns_ressource_record_list = new DnsRessourceRecordList(false, false, 0, 0);
				foreach($itemlist as $dns_ressource_record_id) {
					$dns_ressource_record = new DnsRessourceRecord((int)$dns_ressource_record_id);
					if($dns_ressource_record->fetch()) {
						$dns_ressource_record_list->add($dns_ressource_record);
					}
				}
				$this->setDnsRessourceRecordList($dns_ressource_record_list);
				return true;
			} elseif ($itemlist == false AND $this->getServiceId() != 0) {
				$result = array();
				try {
					$stmt = DB::getInstance()->prepare("SELECT dns_ressource_record_id
														FROM service_dns_ressource_records
														WHERE service_id = ?");
					$stmt->execute(array($this->getServiceId()));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				
				$dns_ressource_record_list = new DnsRessourceRecordList(false, false, 0, 0);
				foreach($result as $dns_ressource_record_id) {
					$dns_ressource_record = new DnsRessourceRecord((int)$dns_ressource_record_id['dns_ressource_record_id']);
					if($dns_ressource_record->fetch()) {
						$dns_ressource_record_list->add($dns_ressource_record);
					}
				}
				$this->setDnsRessourceRecordList($dns_ressource_record_list);
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
		
		// Getter methods
		public function getServiceId() {
			return $this->service_id;
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		public function getTitle() {
			return $this->title;
		}
		
		public function getDescription() {
			return $this->description;
		}
		
		public function getPort() {
			return $this->port;
		}
		
		public function getVisible() {
			return $this->visible;
		}
		
		public function getIplist() {
			return $this->iplist;
		}
		
		public function getDnsRessourceRecordList() {
			return $this->dns_ressource_record_list;
		}
		
		public function getUser() {
			return $this->user;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('service');
			$domxmlelement->appendChild($domdocument->createElement("service_id", $this->getServiceId()));
			$domxmlelement->appendChild($domdocument->createElement("user_id", $this->getUserId()));
			$domxmlelement->appendChild($domdocument->createElement("title", $this->getTitle()));
			$domxmlelement->appendChild($domdocument->createElement("description", $this->getDescription()));
			$domxmlelement->appendChild($domdocument->createElement("port", $this->getPort()));
			$domxmlelement->appendChild($domdocument->createElement("visible", $this->getVisible()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			$domxmlelement->appendChild($this->getIplist()->getDomXMLElement($domdocument));
			$domxmlelement->appendChild($this->getDnsRessourceRecordList()->getDomXMLElement($domdocument));
			return $domxmlelement;
		}
	}
?>