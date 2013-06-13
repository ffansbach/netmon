<?php
	require_once(ROOT_DIR.'/lib/classes/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Networkinterfacelist.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/RouterStatusList.class.php');
	
	class Router extends Object {
 		private $router_id;
		private $user_id;
		private $hostname;
		private $description;
		private $location;
		private $latitude;
		private $longitude;
		private $chipset = null;
		private $statusdata = array();
		private $statusdata_history = array();
		private $networkinterfacelist = null;
		private $servicelist = null;
		
		public function __construct($router_id=false, $user_id=false, $hostname=false, $description=false,
									$location=false, $latitude=false, $longitude=false, $chipset_id=false) {
			if($router_id==false) {
				$result = array();
			} else {
				try {
						$stmt = DB::getInstance()->prepare("SELECT *
															FROM routers
															WHERE id = ?");
					$stmt->execute(array($router_id));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				
				$this->setRouterId((int)$result['id']);
				$this->setUserId((int)$result['user_id']);
				$this->setHostname($result['hostname']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				$this->setDescription($result['description']);
				$this->setLocation($result['location']);
				$this->setLatitude($result['latitude']);
				$this->setLongitude($result['longitude']);
				$this->setNetworkinterfacelist();
				$this->setStatusdata();
				$this->setStatusdataHistory();
				
			}
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
			if(!preg_match('/^([a-zA-Z0-9])+$/i', $hostname)) {
				return false;
			} else {
				$this->hostname = $hostname;
			}
		}
		
		public function setDescription($description) {
			$this->description = $description;
		}
		
		public function setLocation($location) {
			$this->location = $location;
		}
		
		public function setLatitude($latitude) {
			$this->latitude = $latitude;
		}
		
		public function setLongitude($longitude) {
			$this->longitude = $longitude;
		}
		
		public function setNetworkinterfacelist($networkinterfacelist=false) {
			if($networkinterfacelist!=false && is_array($networkinterfacelist))
				$this->networkinterfacelist = $networkinterfacelist;
			else
				$this->networkinterfacelist = new Networkinterfacelist($this->router_id);
		}
		
		public function setStatusdata($routerstatus=false) {
			if($routerstatus!=false)
				$this->statusdata = $routerstatus;
			else
				$this->statusdata = new RouterStatus(false, $this->router_id);
		}
		
		public function setStatusdataHistory($statusdata_history=false) {
			if($statusdata_history!=false && is_array($statusdata_history))
				$this->statusdata_history = $statusdata_history;
			else
				//limit statusdata_history to 10 entrys otherwise it will produce to much load
				$this->statusdata_history = new RouterStatusList($this->router_id, 0, 10);
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
		
		public function getNetworkinterfacelist() {
			return  $this->networkinterfacelist;
		}
		
		public function getStatusdata() {
			return  $this->statusdata;
		}
		
		public function getStatusdataHistory() {
			return  $this->statusdata_history;
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