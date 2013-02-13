<?php

require_once('lib/classes/core/crawling.class.php');
require_once('lib/classes/extern/xmpphp/XMPP.php');

class RoutersNotAssigned {
	public function getRouterByAutoAssignLoginString($router_auto_assign_login_string) {
		$routers = array();
		try {
			$sql = "SELECT  *
					FROM routers_not_assigned
					WHERE router_auto_assign_login_string='$router_auto_assign_login_string'";
			$result = DB::getInstance()->query($sql);
			$router = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $router;
	}

	public function getRouters() {
		$routers = array();
		try {
			$sql = "SELECT  *
					FROM routers_not_assigned";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$routers[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $routers;
	}
}

?>