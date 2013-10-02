<?php
	require_once(ROOT_DIR.'/lib/core/ObjectStatus.class.php');
	
	class NetworkinterfaceStatus extends ObjectStatus {
		private $networkinterface_id = 0;
		private $router_id = 0; //TODO: remove this
		private $name = "";
		private $mac_addr = "";
		private $mtu = 0;
		private $traffic_rx = 0;
		private $traffic_rx_avg = 0;
		private $traffic_tx = 0;
		private $traffic_tx_avg = 0;
		private $wlan_mode = "";
		private $wlan_frequency = "";
		private $wlan_essid = "";
		private $wlan_bssid = "";
		private $wlan_tx_power = 0;
		
		public function __construct($status_id=false, $crawl_cycle_id=false, $networkinterface_id=false, $router_id=false,
									$name=false, $mac_addr=false, $mtu=false, $traffic_rx=false, $traffic_rx_avg=false,
									$traffic_tx=false, $traffic_tx_avg=false,
									$wlan_mode=false, $wlan_frequency=false, $wlan_essid=false, $wlan_bssid=false,
									$wlan_tx_power=false, $create_date=false) {
			$this->setStatusId($status_id);
			$this->setCrawlCycleId($crawl_cycle_id);
			$this->setNetworkinterfaceId($networkinterface_id);
			$this->setRouterId($router_id);
			$this->setName($name);
			$this->setMacAddr($mac_addr);
			$this->setMtu($mtu);
			$this->setTrafficRx($traffic_rx);
			$this->setTrafficRxAvg($traffic_rx_avg);
			$this->setTrafficTx($traffic_tx);
			$this->setTrafficTxAvg($traffic_tx_avg);
			$this->setWlanMode($wlan_mode);
			$this->setWlanFrequency($wlan_frequency);
			$this->setWlanEssid($wlan_essid);
			$this->setWlanBssid($wlan_bssid);
			$this->setWlanTxPower($wlan_tx_power);
			$this->setCreateDate($create_date);
		}
		
		public function store() {
			if($this->getStatusId() != 0 AND $this->getCrawlCycleId() != 0 AND $this->getNetworkinterfaceId() != 0) {
				echo "UPDATE NOT IMPLEMENTED NOW";
			} elseif($this->getCrawlCycleId() != 0 AND $this->getNetworkinterfaceId()) {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO crawl_interfaces (router_id, crawl_cycle_id, interface_id, crawl_date,
																					  name, mac_addr, traffic_rx, traffic_rx_avg,
																					  traffic_tx, traffic_tx_avg,
																					  wlan_mode, wlan_frequency, wlan_essid, wlan_bssid,
																					  wlan_tx_power, mtu)
														VALUES (?, ?, ?, NOW(),
																?, ?, ?, ?,
																?, ?,
																?, ?, ?, ?,
																?, ?)");
					$stmt->execute(array($this->getRouterId(), $this->getCrawlCycleId(), $this->getNetworkinterfaceId(),
										 $this->getName(), $this->getMacAddr(), $this->getTrafficRx(), $this->getTrafficRxAvg(),
										 $this->getTrafficTx(), $this->getTrafficTxAvg(),
										 $this->getWlanMode(), $this->getWlanFrequency(), $this->getWlanEssid(), $this->getWlanBssid(),
										 $this->getWlanTxPower(), $this->getMtu()));
					return DB::getInstance()->lastInsertId();
					
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM crawl_interfaces
													WHERE
														(id = :status_id OR :status_id=0) AND
														(router_id = :router_id OR :router_id=0) AND
														(crawl_cycle_id = :crawl_cycle_id OR :crawl_cycle_id=0) AND
														(interface_id = :interface_id OR :interface_id=0) AND
														(name = :name OR :name='') AND
														(mac_addr = :mac_addr OR :mac_addr='') AND
														(traffic_rx = :traffic_rx OR :traffic_rx=0) AND
														(traffic_rx_avg = :traffic_rx_avg OR :traffic_rx_avg=0) AND
														(traffic_tx = :traffic_tx OR :traffic_tx=0) AND
														(traffic_tx_avg = :traffic_tx_avg OR :traffic_tx_avg=0) AND
														(wlan_mode = :wlan_mode OR :wlan_mode='') AND
														(wlan_frequency = :wlan_frequency OR :wlan_frequency='') AND
														(wlan_essid = :wlan_essid OR :wlan_essid='') AND
														(wlan_bssid = :wlan_bssid OR :wlan_bssid='') AND
														(wlan_tx_power = :wlan_tx_power OR :wlan_tx_power=0) AND
														(mtu = :mtu OR :mtu=0) AND
														(crawl_date = FROM_UNIXTIME(:create_date) OR :create_date=0)");
				$stmt->bindParam(':status_id', $this->getStatusId(), PDO::PARAM_INT);
				$stmt->bindParam(':router_id', $this->getRouterId(), PDO::PARAM_INT);
				$stmt->bindParam(':crawl_cycle_id', $this->getCrawlCycleId(), PDO::PARAM_INT);
				$stmt->bindParam(':interface_id', $this->getNetworkinterfaceId(), PDO::PARAM_INT);
				$stmt->bindParam(':name', $this->getName(), PDO::PARAM_STR);
				$stmt->bindParam(':mac_addr', $this->getMacAddr(), PDO::PARAM_STR);
				$stmt->bindParam(':traffic_rx', $this->getTrafficRx(), PDO::PARAM_INT);
				$stmt->bindParam(':traffic_rx_avg', $this->getTrafficRxAvg(), PDO::PARAM_STR);
				$stmt->bindParam(':traffic_tx', $this->getTrafficTx(), PDO::PARAM_STR);
				$stmt->bindParam(':traffic_tx_avg', $this->getTrafficTxAvg(), PDO::PARAM_INT);
				$stmt->bindParam(':wlan_mode', $this->getWlanMode(), PDO::PARAM_INT);
				$stmt->bindParam(':wlan_frequency', $this->getWlanFrequency(), PDO::PARAM_INT);
				$stmt->bindParam(':wlan_essid', $this->getWlanEssid(), PDO::PARAM_INT);
				$stmt->bindParam(':wlan_bssid', $this->getWlanBssid(), PDO::PARAM_STR);
				$stmt->bindParam(':wlan_tx_power', $this->getWlanTxPower(), PDO::PARAM_STR);
				$stmt->bindParam(':mtu', $this->getMtu(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setStatusId((int)$result['id']);
				$this->setRouterId((int)$result['router_id']);
				$this->setCrawlCycleId((int)$result['crawl_cycle_id']);
				$this->setNetworkinterfaceId((int)$result['interface_id']);
				$this->setName($result['name']);
				$this->setMacAddr($result['mac_addr']);
				$this->setMtu((int)$result['mtu']);
				$this->setTrafficRx((int)$result['traffic_rx']);
				$this->setTrafficRxAvg((int)$result['traffic_rx_avg']);
				$this->setTrafficTx((int)$result['traffic_tx']);
				$this->setTrafficTxAvg((int)$result['traffic_tx_avg']);
				$this->setWlanMode((int)$result['wlan_mode']);
				$this->setWlanFrequency($result['wlan_frequency']);
				$this->setWlanEssid($result['wlan_essid']);
				$this->setWlanBssid($result['wlan_bssid']);
				$this->setWlanTxPower((int)$result['wlan_tx_power']);
				$this->setCreateDate($result['crawl_date']);
				return true;
			}
			
			return false;
		}
		
		public function delete() {
			if($this->getStatusId() != 0) {
				try {
 					$stmt = DB::getInstance()->prepare("DELETE FROM crawl_interfaces WHERE id=?");
					$stmt->execute(array($this->getStatusId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			return false;
		}
		
		public function setNetworkinterfaceId($networkinterface_id) {
			if(is_int($networkinterface_id))
				$this->networkinterface_id = $networkinterface_id;
		}
		
		public function setRouterId($router_id) {
			if(is_int($router_id))
				$this->router_id = $router_id;
		}
		
		public function setName($name) {
			if(is_string($name))
				$this->name = trim($name);
		}
		
		public function setMacAddr($mac_addr) {
			if(is_string($mac_addr))
				$this->mac_addr = trim($mac_addr);
		}
		
		public function setMtu($mtu) {
			if(is_int($mtu))
				$this->mtu = $mtu;
		}
		
		public function setTrafficRx($traffic_rx) {
			if(is_int($traffic_rx))
				$this->traffic_rx = $traffic_rx;
		}
		
		public function setTrafficRxAvg($traffic_rx_avg) {
			if(is_int($traffic_rx_avg))
				$this->traffic_rx_avg = $traffic_rx_avg;
		}
		
		public function setTrafficTx($traffic_tx) {
			if(is_int($traffic_tx))
				$this->traffic_tx = $traffic_tx;
		}
		
		public function setTrafficTxAvg($traffic_tx_avg) {
			if(is_int($traffic_tx_avg))
				$this->traffic_tx_avg = $traffic_tx_avg;
		}
		
		public function setWlanMode($wlan_mode) {
			if(is_string($wlan_mode))
				$this->wlan_mode = trim($wlan_mode);
		}
		
		public function setWlanFrequency($wlan_frequency) {
			if(is_string($wlan_frequency))
				$this->wlan_frequency = trim($wlan_frequency);
		}
		
		public function setWlanEssid($wlan_essid) {
			if(is_string($wlan_essid))
				$this->wlan_essid = trim($wlan_essid);
		}
		
		public function setWlanBssid($wlan_bssid) {
			if(is_string($wlan_bssid))
				$this->wlan_bssid = trim($wlan_bssid);
		}
		
		public function setWlanTxPower($wlan_tx_power) {
			if(is_int($wlan_tx_power))
				$this->wlan_tx_power = $wlan_tx_power;
		}
		
		public function getNetworkinterfaceId() {
			return $this->networkinterface_id;
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
		
		public function getMtu() {
			return $this->mtu;
		}
		
		public function getTrafficRx() {
			return $this->traffic_rx;
		}
		
		public function getTrafficRxAvg() {
			return $this->traffic_rx_avg;
		}
		
		public function getTrafficTx() {
			return $this->traffic_tx;
		}
		
		public function getTrafficTxAvg() {
			return $this->traffic_tx_avg;
		}
		
		public function getWlanMode() {
			return $this->wlan_mode;
		}
		
		public function getWlanFrequency() {
			return $this->wlan_frequency;
		}
		
		public function getWlanChannel() {
			switch($this->wlan_frequency) {
				case "2.412": return 1; 
				case "2.417": return 2; 
				case "2.422": return 3; 
				case "2.427": return 4; 
				case "2.432": return 5; 
				case "2.437": return 6; 
				case "2.442": return 7; 
				case "2.447": return 8; 
				case "2.452": return 9; 
				case "2.457": return 10; 
				case "2.462": return 11; 
				case "2.467": return 12; 
				case "2.472": return 13; 
				case "2.484": return 14; 
			}
		}
		
		public function getWlanEssid() {
			return $this->wlan_essid;
		}
		
		public function getWlanBssid() {
			return $this->wlan_bssid;
		}
		
		public function getWlanTxPower() {
			return $this->wlan_tx_power;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('statusdata');
			$domxmlelement->appendChild($domdocument->createElement("status_id", $this->getStatusId()));
			$domxmlelement->appendChild($domdocument->createElement("crawl_cycle_id", $this->getCrawlCycleId()));
			$domxmlelement->appendChild($domdocument->createElement("networkinterface_id", $this->getNetworkinterfaceId()));
			$domxmlelement->appendChild($domdocument->createElement("name", $this->getName()));
			$domxmlelement->appendChild($domdocument->createElement("mac_addr", $this->getMacAddr()));
			$domxmlelement->appendChild($domdocument->createElement("mtu", $this->getMtu()));
			$domxmlelement->appendChild($domdocument->createElement("traffic_rx", $this->getTrafficRx()));
			$domxmlelement->appendChild($domdocument->createElement("traffic_rx_avg", $this->getTrafficRxAvg()));
			$domxmlelement->appendChild($domdocument->createElement("traffic_tx", $this->getTrafficTx()));
			$domxmlelement->appendChild($domdocument->createElement("traffic_tx_avg", $this->getTrafficTxAvg()));
			$domxmlelement->appendChild($domdocument->createElement("wlan_mode", $this->getWlanMode()));
			$domxmlelement->appendChild($domdocument->createElement("wlan_frequency", $this->getWlanFrequency()));
			$domxmlelement->appendChild($domdocument->createElement("wlan_essid", $this->getWlanEssid()));
			$domxmlelement->appendChild($domdocument->createElement("wlan_bssid", $this->getWlanBssid()));
			$domxmlelement->appendChild($domdocument->createElement("wlan_tx_power", $this->getWlanTxPower()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));

			return $domxmlelement;
		}
	}
?>