<?php
	require_once('../../lib/classes/core/Router.class.php');

	class Routerlist {
		private $routerlist = array();
		
		public function __construct($user_id=false) {
			$result = array();
			if($user_id != false) {
				try {
					$stmt = DB::getInstance()->prepare("SELECT routers.id as router_id
														FROM routers
														WHERE routers.user_id=?");
					$stmt->execute(array($user_id));
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} else {
				try {
					$stmt = DB::getInstance()->prepare("SELECT routers.id as router_id
														FROM routers");
					$stmt->execute(array());
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
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('routerlist');
			foreach($this->routerlist as $routerlist) {
				$domxmlelement->appendChild($routerlist->getDomXMLElement($domdocument));
			}

			return $domxmlelement;
		}
	}
?>