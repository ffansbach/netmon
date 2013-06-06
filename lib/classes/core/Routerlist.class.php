<?php
	require_once(ROOT_DIR.'/lib/classes/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Router.class.php');

	class Routerlist extends ObjectList {
		private $routerlist = array();
		
		public function __construct($user_id=false, $offset=0, $limit=100) {
			$result = array();
			$this->setOffset((int)$offset);
			$this->setLimit((int)$limit);
			
			if($user_id != false) {
				// initialize $total_count with the total number of objects in the list (over all pages)
				try {
					$stmt = DB::getInstance()->prepare("SELECT COUNT(*) as total_count
														FROM routers
														WHERE routers.user_id=?");
					$stmt->execute(array($user_id));
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				// fetch ids from all objects of the list from the database
				try {
					$stmt = DB::getInstance()->prepare("SELECT routers.id as router_id
														FROM routers
														WHERE routers.user_id = :user_id
														ORDER BY routers.id ASC
														LIMIT :offset, :limit");
					$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
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
														FROM routers");
					$stmt->execute(array());
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				
				// fetch ids from all objects of the list from the database
				try {
					$stmt = DB::getInstance()->prepare("SELECT routers.id as router_id
														FROM routers
														ORDER BY routers.id ASC
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
			
			
			foreach($result as $router) {
				$this->routerlist[] = new Router((int)$router['router_id']);
			}
		}
		
		public function getRouterlist() {
			return $this->routerlist;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('routerlist');
			$domxmlelement->setAttribute("total_count", $this->getTotalCount());
			$domxmlelement->setAttribute("offset", $this->getOffset());
			$domxmlelement->setAttribute("limit", $this->getLimit());
			foreach($this->routerlist as $routerlist) {
				$domxmlelement->appendChild($routerlist->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>