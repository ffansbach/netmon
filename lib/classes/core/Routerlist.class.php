<?php
	require_once(ROOT_DIR.'/lib/classes/core/ObjectList.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Router.class.php');

	class Routerlist extends ObjectList {
		private $routerlist = array();
		
		/**
		 * Initialize the routerlist with routers
		 * @param $user_id	possible values:
		 *							int: >0, initialize the routerlist with the routers of the user
		 *							boolean: false, initialize the routerlist with routers of all users
		 * @param $offset	possible values:
		 *							int: >=0, controll the position from where the first router in the list ist fetched from db
		 *							boolean: false, initialize offset with 0
		 * @param $limit	possible values:
		 *							int: >=0, controll the maximum numbers of routers in the list
		 *							int: -1, set limit to maximum
		 * @param $sort_by	possible values:
		 *							string: hostname, 
		 *							boolean: false, sort default by router_id
		 * @param $order	possible values:
		 *							string: asc, desc
		 *							boolean: false, order default asc
		 */
		public function __construct($user_id=false, $offset=false, $limit=false, $sort_by=false, $order=false) {
			$result = array();
			if($offset!==false)
				$this->setOffset((int)$offset);
			if($limit!==false)
				$this->setLimit((int)$limit);
			if($sort_by!==false)
				$this->setSortBy($sort_by);
			if($order!==false)
				$this->SetOrder($order);
				
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
				//if limit -1 then get all routers
				if($this->getLimit()==-1)
					$this->setLimit($this->getTotalCount());
				
				// fetch ids from all objects of the list from the database
				try {
					$stmt = DB::getInstance()->prepare("SELECT routers.id as router_id
														FROM routers
														WHERE routers.user_id = :user_id
														ORDER BY
															case :sort_by
																when 'hostname' then routers.hostname
																else routers.id
															end
														".$this->getOrder()."
														LIMIT :offset, :limit");
					$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
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
														FROM routers");
					$stmt->execute(array());
					$total_count = $stmt->fetch(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
				$this->setTotalCount((int)$total_count['total_count']);
				//if limit -1 then get all routers
				if($this->getLimit()==-1)
					$this->setLimit($this->getTotalCount());
					
				// fetch ids from all objects of the list from the database
				try {
					$stmt = DB::getInstance()->prepare("SELECT routers.id as router_id
														FROM routers
														ORDER BY
															case :sort_by
																when 'hostname' then routers.hostname
																else routers.id
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
			
			
			foreach($result as $router) {
				$router = new Router((int)$router['router_id']);
				$router->fetch();
				$this->routerlist[] = $router;
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