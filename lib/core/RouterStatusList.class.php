<?php
	require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/ObjectList.class.php');
	
	class RouterStatusList extends ObjectList {
		private $router_status_list = array();
		
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
													FROM crawl_routers
													WHERE
														(crawl_routers.router_id = :router_id OR :router_id=0)");
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
				$stmt = DB::getInstance()->prepare("SELECT crawl_routers.id as status_id, crawl_routers.*
													FROM crawl_routers
													WHERE
														(crawl_routers.router_id = :router_id OR :router_id=0)
													ORDER BY
														case :sort_by
															when 'crawl_date' then crawl_routers.crawl_date
															when 'status_id' then crawl_routers.id
															else NULL
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
			
			foreach($result as $router_status) {
				$router_status = new RouterStatus((int)$router_status['status_id'], (int)$router_status['crawl_cycle_id'], (int)$router_status['router_id'], 
									$router_status['status'], $router_status['crawl_date'], $router_status['hostname'], (int)$router_status['client_count'], $router_status['chipset'], 
									$router_status['cpu'], (int)$router_status['memory_total'], (int)$router_status['memory_caching'], (int)$router_status['memory_buffering'], 
									(int)$router_status['memory_free'], $router_status['loadavg'], $router_status['processes'], $router_status['uptime'], 
									$router_status['idletime'], $router_status['local_time'], $router_status['distname'], $router_status['distversion'], $router_status['openwrt_core_revision'], 
									$router_status['openwrt_feeds_packages_revision'], $router_status['firmware_version'], 
									$router_status['firmware_revision'], $router_status['kernel_version'], $router_status['configurator_version'], 
									$router_status['nodewatcher_version'], $router_status['fastd_version'], $router_status['batman_advanced_version']);
				$this->router_status_list[] = $router_status;
			}
		}
		
		public function getRouterStatuslist() {
			return $this->router_status_list;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('statusdata_history');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->router_status_list as $router_status) {
				$domxmlelement->appendChild($router_status->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>