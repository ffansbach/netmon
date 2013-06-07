<?php
	require_once(ROOT_DIR.'/lib/classes/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/ObjectList.class.php');
	
	class RouterStatusList extends ObjectList {
		private $router_status_list = array();
		
		public function __construct($router_id=false, $offset=0, $limit=100) {
			$result = array();
			$this->setOffset((int)$offset);
			$this->setLimit((int)$limit);
			
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
														ORDER BY crawl_routers.id DESC
														LIMIT :offset, :limit");
					$stmt->bindParam(':router_id', $router_id, PDO::PARAM_INT);
					$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
					$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
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
														ORDER BY crawl_routers.id DESC
														LIMIT :offset, :limit");
					$stmt->bindParam(':offset', $this->getOffset(), PDO::PARAM_INT);
					$stmt->bindParam(':limit', $this->getLimit(), PDO::PARAM_INT);
					$stmt->execute();
					
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			
			foreach($result as $router_status) {
				$this->router_status_list[] = new RouterStatus((int)$router_status['status_id']);
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