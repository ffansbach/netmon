<?php
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterface.class.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	
	class Networkinterfacelist extends ObjectList {
		private $networkinterfacelist = array();
		
		public function __construct($status_id=false, $router_id=false, $offset=false, $limit=false, $sort_by=false, $order=false) {
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
				$stmt = DB::getInstance()->prepare("SELECT i.id as i_id, i.router_id as i_router_id, i.name as i_name,
													i.create_date as i_create_date, i.update_date as i_update_date,
													ci.id as ci_id, ci.router_id as ci_router_id, ci.crawl_cycle_id as ci_crawl_cycle_id,
													ci.interface_id ci_interface_id, ci.crawl_date as ci_crawl_date, ci.name as ci_name,
													ci.mac_addr as ci_mac_addr, ci.traffic_rx ci_traffic_rx, ci.traffic_rx_avg ci_traffic_rx_avg,
													ci.traffic_tx ci_traffic_tx, ci.traffic_tx_avg ci_traffic_tx_avg, ci.wlan_mode ci_wlan_mode,
													ci.wlan_frequency ci_wlan_frequency, ci.wlan_essid ci_wlan_essid, ci.wlan_bssid ci_wlan_bssid,
													ci.wlan_tx_power ci_wlan_tx_power, ci.mtu ci_mtu
													FROM interfaces i, crawl_interfaces ci
													WHERE
														(i.router_id = :router_id OR :router_id=0) AND
														ci.interface_id = i.id AND
														ci.crawl_cycle_id = :status_id
													ORDER BY
														case :sort_by
															when 'name' then i.name
															else NULL
														end
													".$this->getOrder()."
													LIMIT :offset, :limit");
				$stmt->bindParam(':status_id', ($status_id) ? $status_id : (int)crawling::getLastEndedCrawlCycle()['id'], PDO::PARAM_INT);
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
				$new_networkinterface = new Networkinterface((int)$networkinterface['i_id'], (int)$networkinterface['i_router_id'],
															 $networkinterface['i_name'],
															 $networkinterface['i_create_date'], $networkinterface['i_update_date']);
				$new_networkinterface->setStatusdata(new NetworkinterfaceStatus((int)$networkinterface['ci_id'],
																						(int)$networkinterface['ci_crawl_cycle_id'],
																						(int)$networkinterface['ci_interface_id'],
																						(int)$networkinterface['ci_router_id'],
																						$networkinterface['ci_name'],
																						$networkinterface['ci_mac_addr'],
																						(int)$networkinterface['ci_mtu'],
																						(int)$networkinterface['ci_traffic_rx'], 
																						(int)$networkinterface['ci_traffic_rx_avg'],
																						(int)$networkinterface['ci_traffic_tx'],
																						(int)$networkinterface['ci_traffic_tx_avg'],
																						$networkinterface['ci_wlan_mode'], 
																						$networkinterface['ci_wlan_frequency'],
																						$networkinterface['ci_wlan_essid'],
																						$networkinterface['ci_wlan_bssid'],
																						(int)$networkinterface['ci_wlan_tx_power'],
																						$networkinterface['ci_crawl_date']));
				$this->networkinterfacelist[] = $new_networkinterface;
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