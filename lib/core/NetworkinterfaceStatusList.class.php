<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/NetworkinterfaceStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	
	class NetworkinterfaceStatusList extends ObjectList {
		private $list = array();
		
		public function __construct($networkinterface_id=false,
									$name=false, $mac_addr=false, $mtu=false, $wlan_mode=false, $wlan_frequency=false,
									$wlan_essid=false, $wlan_bssid=false, $wlan_tx_power=false,
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
													FROM crawl_interfaces
													WHERE
														(interface_id = :networkinterface_id OR :networkinterface_id=0) AND
														(name = :name OR :name='') AND
														(mac_addr = :mac_addr OR :mac_addr='') AND
														(mtu = :mtu OR :mtu=0) AND
														(wlan_mode = :wlan_mode OR :wlan_mode='') AND
														(wlan_frequency = :wlan_frequency OR :wlan_frequency='') AND
														(wlan_essid = :wlan_essid OR :wlan_essid='') AND
														(wlan_bssid = :wlan_bssid OR :wlan_bssid='') AND
														(wlan_tx_power = :wlan_tx_power OR :wlan_tx_power=0)");
				$stmt->bindParam(':networkinterface_id', $networkinterface_id, PDO::PARAM_INT);
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt->bindParam(':mac_addr', $mac_addr, PDO::PARAM_STR);
				$stmt->bindParam(':mtu', $mtu, PDO::PARAM_INT);
				$stmt->bindParam(':wlan_mode', $wlan_mode, PDO::PARAM_STR);
				$stmt->bindParam(':wlan_frequency', $wlan_frequency, PDO::PARAM_STR);
				$stmt->bindParam(':wlan_essid', $wlan_essid, PDO::PARAM_STR);
				$stmt->bindParam(':wlan_bssid', $wlan_bssid, PDO::PARAM_STR);
				$stmt->bindParam(':wlan_tx_power', $wlan_tx_power, PDO::PARAM_INT);
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
				$stmt = DB::getInstance()->prepare("SELECT crawl_interfaces.id as status_id, crawl_interfaces.*
													FROM crawl_interfaces
													WHERE
														(interface_id = :networkinterface_id OR :networkinterface_id=0) AND
														(name = :name OR :name='') AND
														(mac_addr = :mac_addr OR :mac_addr='') AND
														(mtu = :mtu OR :mtu=0) AND
														(wlan_mode = :wlan_mode OR :wlan_mode='') AND
														(wlan_frequency = :wlan_frequency OR :wlan_frequency='') AND
														(wlan_essid = :wlan_essid OR :wlan_essid='') AND
														(wlan_bssid = :wlan_bssid OR :wlan_bssid='') AND
														(wlan_tx_power = :wlan_tx_power OR :wlan_tx_power=0)
													ORDER BY
														case :sort_by
															when 'status_id' then crawl_interfaces.id
															else NULL
														end
													".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':networkinterface_id', $networkinterface_id, PDO::PARAM_INT);
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt->bindParam(':mac_addr', $mac_addr, PDO::PARAM_STR);
				$stmt->bindParam(':mtu', $mtu, PDO::PARAM_INT);
				$stmt->bindParam(':wlan_mode', $wlan_mode, PDO::PARAM_STR);
				$stmt->bindParam(':wlan_frequency', $wlan_frequency, PDO::PARAM_STR);
				$stmt->bindParam(':wlan_essid', $wlan_essid, PDO::PARAM_STR);
				$stmt->bindParam(':wlan_bssid', $wlan_bssid, PDO::PARAM_STR);
				$stmt->bindParam(':wlan_tx_power', $wlan_tx_power, PDO::PARAM_INT);
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
				$item_object = new NetworkinterfaceStatus((int)$item['status_id'], (int)$item['crawl_cycle_id'],
														  (int)$item['interface_id'], 0,
														  $item['name'], $item['mac_addr'],
														  (int)$item['mtu'], (int)$item['traffic_rx'], 
														  (int)$item['traffic_rx_avg'], (int)$item['traffic_tx'],
														  (int)$item['traffic_tx_avg'], $item['wlan_mode'], 
														  $item['wlan_frequency'], $item['wlan_essid'],
														  $item['wlan_bssid'], (int)$item['wlan_tx_power'],
														  $item['crawl_date']);
				$this->list[] = $item_object;
			}
		}
		
		public function delete() {
			foreach($this->getList() as $item) {
				$item->delete();
			}
		}
		
		public function setList($list) {
			if(is_array($list)) {
				$this->list = $list;
			}
		}
		
		public function getList() {
			return $this->list;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('networkinterface_status_list');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->list as $item) {
				$domxmlelement->appendChild($item->getDomXMLElement($domdocument));
			}
			
			return $domxmlelement;
		}
	}
?>