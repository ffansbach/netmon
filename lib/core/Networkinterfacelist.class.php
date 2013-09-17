<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterface.class.php');

	class Networkinterfacelist extends ObjectList {
		private $networkinterfacelist = array();
		
		public function __construct($router_id=false, $offset=false, $limit=false, $sort_by=false, $order=false) {
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
													FROM interfaces
													WHERE
														(interfaces.router_id = :router_id OR :router_id=0)");
				$stmt->bindParam(':router_id', $router_id, PDO::PARAM_INT);
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
				$stmt = DB::getInstance()->prepare("SELECT interfaces.id as networkinterface_id,
													interfaces.router_id as networkinterface_router_id,
													interfaces.name as networkinterface_name,
													interfaces.create_date as networkinterface_create_date,
													interfaces.update_date as networkinterface_update_date,
													crawl_interfaces.id as crawl_interfaces_id,
													crawl_interfaces.router_id as crawl_interfaces_router_id,
													crawl_interfaces.crawl_cycle_id as crawl_interfaces_crawl_cycle_id,
													crawl_interfaces.interface_id crawl_interfaces_interface_id,
													crawl_interfaces.crawl_date as crawl_interfaces_crawl_date,
													crawl_interfaces.name as crawl_interfaces_name,
													crawl_interfaces.mac_addr as crawl_interfaces_mac_addr,
													crawl_interfaces.traffic_rx crawl_interfaces_traffic_rx,
													crawl_interfaces.traffic_rx_avg crawl_interfaces_traffic_rx_avg,
													crawl_interfaces.traffic_tx crawl_interfaces_traffic_tx,
													crawl_interfaces.traffic_tx_avg crawl_interfaces_traffic_tx_avg,
													crawl_interfaces.wlan_mode crawl_interfaces_wlan_mode,
													crawl_interfaces.wlan_frequency crawl_interfaces_wlan_frequency,
													crawl_interfaces.wlan_essid crawl_interfaces_wlan_essid,
													crawl_interfaces.wlan_bssid crawl_interfaces_wlan_bssid,
													crawl_interfaces.wlan_tx_power crawl_interfaces_wlan_tx_power,
													crawl_interfaces.mtu crawl_interfaces_mtu
													FROM interfaces, crawl_interfaces
													WHERE
														(interfaces.router_id = :router_id OR :router_id=0) AND
														interfaces.id = crawl_interfaces.interface_id
													ORDER BY
														case :sort_by
															when 'name' then interfaces.name
																else interfaces.id
														end
													".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':router_id', $router_id, PDO::PARAM_INT);
				$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
				$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
				$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			foreach($result as $networkinterface) {
				$networkinterface = new Networkinterface((int)$networkinterface['networkinterface_id'], (int)$networkinterface['networkinterface_router_id'],
														 $networkinterface['networkinterface_name'],
														 $networkinterface['networkinterface_create_date'], $networkinterface['networkinterface_update_date'],
														 new NetworkinterfaceStatus($networkinterface['crawl_interfaces_id'], $networkinterface['crawl_interfaces_router_id'],
																					$networkinterface['crawl_interfaces_crawl_cycle_id'], $networkinterface['crawl_interfaces_interface_id'], 
																					$networkinterface['crawl_interfaces_crawl_date'], $networkinterface['crawl_interfaces_name'],
																					$networkinterface['crawl_interfaces_mac_addr'], $networkinterface['crawl_interfaces_traffic_rx'], 
																					$networkinterface['crawl_interfaces_traffic_rx_avg'], $networkinterface['crawl_interfaces_traffic_tx'],
																					$networkinterface['crawl_interfaces_traffic_tx_avg'], $networkinterface['crawl_interfaces_wlan_mode'], 
																					$networkinterface['crawl_interfaces_wlan_frequency'], $networkinterface['crawl_interfaces_wlan_essid'],
																					$networkinterface['crawl_interfaces_wlan_bssid'], $networkinterface['crawl_interfaces_wlan_tx_power'], 
																					$networkinterface['crawl_interfaces_mtu'])
														 );
				$this->networkinterfacelist[] = $networkinterface;
			}
		}
		
		public function setNetworkinterfacelist($networkinterfacelist) {
			if(is_array($networkinterfacelist)) {
				$this->networkinterfacelist = $networkinterfacelist;
			}
		}
		
		public function getNetworkinterfacelist() {
			return $this->networkinterfacelist;
		}
		
		public function sort($sort, $order) {
			$tmp = array();
			
			$networkinterfacelist = $this->getNetworkinterfacelist();
			foreach($networkinterfacelist as $key=>$event) {
				switch($sort) {
					case 'create_date':		$tmp[$key] = $event->getCreateDate();
											break;
					case 'name':			$tmp[$key] = $event->getName();
											break;
					default:				$tmp[$key] = $event->getCreateDate();
											break;
				}
			}
			
			if($order == 'asc')
				array_multisort($tmp, SORT_ASC, $networkinterfacelist);
			elseif($order == 'desc')
				array_multisort($tmp, SORT_DESC, $networkinterfacelist);
			
			$new_eventlist = array();
			for($i=0; $i<count($networkinterfacelist); $i++) {
				if(!empty($networkinterfacelist[$i])) {
					$new_eventlist[] = $networkinterfacelist[$i];
				}
			}
			
			$this->setNetworkinterfacelist($new_eventlist);
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('networkinterfacelist');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->networkinterfacelist as $networkinterface) {
				$domxmlelement->appendChild($networkinterface->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>