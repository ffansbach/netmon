<?php
	require_once('../../lib/classes/core/Object.class.php');
	require_once('../../lib/classes/core/Iplist.class.php');
	
	class Networkinterface extends Object {
		private $networkinterface_id = 0;
		private $router_id = 0;
		private $name = "";
		private $mac_addr = "";
		private $statusdata = array();
		private $statusdata_history = array();
		private $iplist = array();
		
		public function __construct($networkinterface_id=false, $router_id=false, $name=false, $mac_addr=false) {
			if($router_id != false AND $name != false AND $mac_addr) {
				$this->setRouterId((int)$router_id);
				$this->setName($name);
				$this->setMacAddr($mac_addr);
			} else if ($networkinterface_id !== false AND is_int($networkinterface_id)) {
				//fetch interface data from database
				$result = array();
				try {
					$stmt = DB::getInstance()->prepare("SELECT *
														FROM interfaces
														WHERE id = ?");
					$stmt->execute(array($networkinterface_id));
					$result = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}

				$this->setNetworkinterfaceId((int)$result['id']);
				$this->setRouterId((int)$result['router_id']);
				$this->setName($result['name']);
				$this->setMacAddr($result['mac_addr']);
				$this->setCreateDate($result['create_date']);
				$this->setIplist((int)$result['id']);
			}
		}
		
		public function setNetworkinterfaceId($networkinterface_id) {
			if(is_int($networkinterface_id))
				$this->interface_id = $networkinterface_id;
		}
		
		public function setRouterId($router_id) {
			if(is_int($router_id))
				$this->router_id = $router_id;
		}
		
		public function setName($name) {
			if(is_string($name))
				$this->name = $name;
		}
		
		public function setMacAddr($mac_addr) {
			if(is_string($mac_addr))
				$this->mac_addr = $mac_addr;
		}
		
		public function setIplist($iplist=false) {
			if($iplist!=false && is_array($iplist))
				$this->iplist = $iplist;
			else
				$this->iplist = new Iplist($this->interface_id);
		}
		
		public function getInterfaceId() {
			return $this->interface_id;
		}
		
		public function getRouterId() {
			return $this->router_id;
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getMacAddr() {
			return $this->mac_addr;
		}
		
		public function getIpList() {
			return $this->iplist;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('networkinterface');
			$domxmlelement->appendChild($domdocument->createElement("networkinterface_id", $this->getInterfaceId()));
			$domxmlelement->appendChild($domdocument->createElement("router_id", $this->getRouterId()));
			$domxmlelement->appendChild($domdocument->createElement("name", $this->getName()));
			$domxmlelement->appendChild($domdocument->createElement("mac_addr", $this->getMacAddr()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			
			$domxmliplist = $domdocument->createElement('iplist');
			$domxmliplist->appendChild($this->getIplist()->getDomXMLElement($domdocument));
			$domxmlelement->appendChild($domxmliplist);
			return $domxmlelement;
		}
	}
?>