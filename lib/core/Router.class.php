<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterfacelist.class.php');
	require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/RouterStatusList.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	require_once(ROOT_DIR.'/lib/core/Chipset.class.php');
	
	class Router extends Object {
 		private $router_id = 0;
		private $user_id = 0;
		private $hostname = "";
		private $description = "";
		private $location = "";
		private $latitude = "";
		private $longitude = "";
		private $chipset_id = 0;
		private $statusdata = array();
		private $statusdata_history = array();
		private $networkinterfacelist = null;
		private $servicelist = null;
		private $chipset = null;
		
		public function __construct($router_id=false, $user_id=false, $hostname=false, $description=false,
									$location=false, $latitude=false, $longitude=false, $chipset_id=false,
									$create_date=false, $update_date=false) {
			$this->setRouterId($router_id);
			$this->setUserId($user_id);
			$this->setHostname($hostname);
			$this->setDescription($description);
			$this->setLocation($location);
			$this->setLatitude($latitude);
			$this->setLongitude($longitude);
			$this->setChipsetId($chipset_id);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
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
														(create_date = :create_date OR :create_date=0) AND
														(update_date = :update_date OR :update_date=0)");
				$stmt->bindParam(':router_id', $this->getRouterId(), PDO::PARAM_INT);
				$stmt->bindParam(':user_id', $this->getUserId(), PDO::PARAM_STR);
				$stmt->bindParam(':hostname', $this->getHostname(), PDO::PARAM_STR);
				$stmt->bindParam(':description', $this->getDescription(), PDO::PARAM_STR);
				$stmt->bindParam(':location', $this->getLocation(), PDO::PARAM_STR);
				$stmt->bindParam(':latitude', $this->getLatitude(), PDO::PARAM_STR);
				$stmt->bindParam(':longitude', $this->getLongitude(), PDO::PARAM_STR);
				$stmt->bindParam(':chipset_id', $this->getChipsetId(), PDO::PARAM_INT);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setRouterId((int)$result['id']);
				$this->setUserId((int)$result['user_id']);
				$this->setHostname($result['hostname']);
				$this->setDescription($result['description']);
				$this->setLocation($result['location']);
				$this->setLatitude($result['latitude']);
				$this->setLongitude($result['longitude']);
				$this->setChipsetId((int)$result['chipset_id']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				$this->setNetworkinterfacelist();
				$this->setStatusdata();
				$this->setStatusdataHistory();
				$this->setChipset();
				return true;
			}
			
			return false;
		}
		
		public function setRouterId($router_id) {
			if(is_int($router_id))
				$this->router_id = $router_id;
		}
		
		public function setUserId($user_id) {
			if(is_int($user_id))
				$this->user_id = $user_id;
		}
		
		public function setHostname($hostname) {
			if(!is_string($hostname) AND !preg_match('/^([a-zA-Z0-9])+$/i', $hostname)) {
				return false;
			} else {
				$this->hostname = $hostname;
			}
		}
		
		public function setDescription($description) {
			if($description!==false)
				$this->description = $description;
		}
		
		public function setLocation($location) {
			if($location!==false)
				$this->location = $location;
		}
		
		public function setLatitude($latitude) {
			if($latitude!==false)
				$this->latitude = $latitude;
		}
		
		public function setLongitude($longitude) {
			if($longitude!==false)
				$this->longitude = $longitude;
		}
		
		public function setChipsetId($chipset_id) {
			if(is_int($chipset_id))
				$this->chipset_id = $chipset_id;
		}
		
		public function setNetworkinterfacelist($networkinterfacelist=false) {
			if($networkinterfacelist!=false && is_array($networkinterfacelist))
				$this->networkinterfacelist = $networkinterfacelist;
			else
				$this->networkinterfacelist = new Networkinterfacelist($this->router_id);
		}
		
		public function setStatusdata($routerstatus=false) {
			if($routerstatus!=false) {
				$this->statusdata = $routerstatus;
			} else {
				$router_status = new RouterStatus(false, false, $this->router_id);
				$router_status->fetch();
				$this->statusdata = $router_status;
			}
		}
		
		public function setStatusdataHistory($statusdata_history=false) {
			if($statusdata_history!=false && is_array($statusdata_history))
				$this->statusdata_history = $statusdata_history;
			else
				//limit statusdata_history to 10 entrys otherwise it will produce to much load
				$this->statusdata_history = new RouterStatusList($this->router_id, 0, 10);
		}
		
		public function setChipset($chipset=false) {
			if($chipset!=false) {
				$this->chipset = $chipset;
			} else {
				$chipset = new Chipset($this->chipset_id);
				$chipset->fetch();
				$this->chipset = $chipset;
			}
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
		
		public function getNetworkinterfacelist() {
			return  $this->networkinterfacelist;
		}
		
		public function getStatusdata() {
			return  $this->statusdata;
		}
		
		public function getStatusdataHistory() {
			return  $this->statusdata_history;
		}
		
		public function getChipset() {
			return  $this->chipset;
		}
		
		public function getUser() {
			$user = new User($this->getUserId());
			if($user->fetch())
				return $user;
			return false;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('router');
			$domxmlelement->appendChild($domdocument->createElement("router_id", $this->getRouterId()));
			$domxmlelement->appendChild($domdocument->createElement("user_id", $this->getUserId()));
			$domxmlelement->appendChild($domdocument->createElement("hostname", $this->gethostname()));
			$domxmlelement->appendChild($domdocument->createElement("description", $this->getDescription()));
			$domxmlelement->appendChild($domdocument->createElement("location", $this->getLocation()));
			$domxmlelement->appendChild($domdocument->createElement("latitude", $this->getLatitude()));
			$domxmlelement->appendChild($domdocument->createElement("longitude", $this->getLongitude()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			$domxmlelement->appendChild($domdocument->createElement("update_date", $this->getUpdateDate()));
			
			$domxmlelement->appendChild($this->getNetworkinterfacelist()->getDomXMLElement($domdocument));
			$domxmlelement->appendChild($this->getStatusdata()->getDomXMLElement($domdocument));
			$domxmlelement->appendChild($this->getStatusdataHistory()->getDomXMLElement($domdocument));
			return $domxmlelement;
		}
	}
?>