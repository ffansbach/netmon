<?php

require_once($path.'lib/classes/core/interfaces.class.php');
require_once($path.'lib/classes/core/service.class.php');
require_once($path.'lib/classes/core/serviceeditor.class.php');

class RouterEditor {
	public function insertNewRouter() {
		DB::getInstance()->exec("INSERT INTO routers (user_id, create_date, update_date, crawl_method, hostname, allow_router_auto_assign, description, location, latitude, longitude, chipset_id)
						      VALUES ('$_SESSION[user_id]', NOW(), NOW(), '$_POST[crawl_method]', '$_POST[hostname]', '$_POST[allow_router_auto_assign]', '$_POST[description]', '$_POST[location]', '$_POST[latitude]', '$_POST[longitude]', '$_POST[chipset_id]');");
		$router_id = DB::getInstance()->lastInsertId();
		
		$message[] = array("Der Router $_POST[hostname] wurde angelegt.", 1);
		Message::setMessage($message);
		
		return array("result"=>true, "router_id"=>$router_id);
	}

	public function resetRouterAutoAssignHash($router_id) {
		$result = DB::getInstance()->exec("UPDATE routers SET
							router_auto_assign_hash = ''
						WHERE id = '$router_id'");
		if ($result>0) {
			$message[] = array("Der Auto Assign Hash wurde zurückgesetzt.", 1);
			Message::setMessage($message);
			return true;
		} else {
			$message[] = array("Fehler!", 2);
			Message::setMessage($message);
			return false;
		}
	}

	public function insertEditRouter() {
		$result = DB::getInstance()->exec("UPDATE routers SET
							update_date=NOW(),
							crawl_method='$_POST[crawl_method]',
							hostname='$_POST[hostname]',
							allow_router_auto_assign='$_POST[allow_router_auto_assign]',
							description='$_POST[description]',
							location='$_POST[location]',
							latitude='$_POST[latitude]',
							longitude='$_POST[longitude]',
							chipset_id='$_POST[chipset_id]'
						WHERE id = '$_GET[router_id]'");
		if ($result>0) {
			$message[] = array("Die Änderungen wurden gespeichert.", 1);
			Message::setMessage($message);
			return true;
		} else {
			$message[] = array("Fehler!", 2);
			Message::setMessage($message);
			return false;
		}
	}

	public function insertDeleteRouter($router_id) {
		$router_data=Router::getRouterInfo($router_id);

		$interfaces = Interfaces::getInterfacesByRouterId($router_id);
		foreach($interfaces as $interface) {
			Interfaces::deleteInterface($interface['interface_id']);
		}

		$services = Service::getServicesByRouterId($router_id);
		foreach($services as $service) {
			ServiceEditor::deleteService($service['id']);
		}

		DB::getInstance()->exec("DELETE FROM routers WHERE id='$router_id';");

		$message[] = array("Der Router $router_data[hostname] wurde gelöscht.", 1);
		Message::setMessage($message);
		return true;
	}

}

?>