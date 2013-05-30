<?php
	require_once('../../lib/classes/core/Object.class.php');
	require_once('../../lib/classes/core/Networkinterfacelist.class.php');
	
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
			}
		}
		
		private function setRouterId($router_id) {
			if(is_int($router_id))
				$this->router_id = $router_id;
		}
		
		private function setUserId($user_id) {
			if(is_int($user_id))
				$this->user_id = $user_id;
		}
		
		private function setHostname($hostname) {
			if(!preg_match('/^([a-zA-Z0-9])+$/i', $hostname)) {
				return false;
			} else {
				$this->hostname = $hostname;
			}
		}
		
		private function setDescription($description) {
			$this->description = $description;
		}
		
		private function setLocation($location) {
			$this->location = $location;
		}
		
		private function setLatitude($latitude) {
			$this->latitude = $latitude;
		}
		
		private function setLongitude($longitude) {
			$this->longitude = $longitude;
		}
		
		private function setNetworkinterfacelist($networkinterfacelist=false) {
			if($networkinterfacelist!=false && is_array($networkinterfacelist))
				$this->networkinterfacelist = $networkinterfacelist;
			else
				$this->networkinterfacelist = new Networkinterfacelist($this->router_id);
		}
		
		public function getRouterId() {
			return $this->router_id;
		}
		
		public function getUserId() {
			return $this->user_id;
		}
		
		private function getHostname() {
			return $this->hostname;
		}
		
		private function getDescription() {
			return $this->description;
		}
		
		private function getLocation() {
			return $this->location;
		}
		
		private function getLatitude() {
			return $this->latitude;
		}
		
		private function getLongitude() {
			return  $this->longitude;
		}
		
		private function getNetworkinterfacelist() {
			return  $this->networkinterfacelist;
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
			
			$domxmlnetworkinterfacelist = $domdocument->createElement('networkinterfacelist');
			$domxmlnetworkinterfacelist->appendChild($this->getNetworkinterfacelist()->getDomXMLElement($domdocument));
			$domxmlelement->appendChild($domxmlnetworkinterfacelist);
			return $domxmlelement;
		}
	}
?>