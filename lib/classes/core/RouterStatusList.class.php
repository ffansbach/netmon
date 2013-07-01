<?php
	require_once(ROOT_DIR.'/lib/classes/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/ObjectList.class.php');
	
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
			else 
				$this->SetOrder("desc");
				
			if($router_id != false) {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM crawl_routers
														WHERE crawl_routers.router_id=?");
					$stmt->execute(array($router_id));
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				// fetch ids from all objects of the list from the database
				try {
					$stmt = DB::getInstance()->prepare("SELECT crawl_routers.id as status_id
														FROM crawl_routers
														WHERE crawl_routers.router_id = :router_id
														ORDER BY
															case :sort_by
																when 'crawl_date' then crawl_routers.crawl_date
																else crawl_routers.id
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
			} else {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM crawl_routers");
					$stmt->execute(array());
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				// fetch ids from all objects of the list from the database
				try {
					$stmt = DB::getInstance()->prepare("SELECT crawl_routers.id as status_id
														FROM crawl_routers
														ORDER BY
															case :sort_by
																when 'crawl_date' then crawl_routers.crawl_date
																else crawl_routers.id
															end
														".$this->getOrder()."
														LIMIT :offset, :limit");
					$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
					$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
					$stmt->bindParam(':sort_by', $this->getSortBy(), PDO::PARAM_STR);
					$stmt->execute();
					
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			
			foreach($result as $router_status) {
				$router_status = new RouterStatus((int)$router_status['status_id']);
				$router_status->fetch();
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