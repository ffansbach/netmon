<?php

require_once(ROOT_DIR.'/lib/core/interfaces.class.php');
require_once(ROOT_DIR.'/lib/core/crawling.class.php');
require_once(ROOT_DIR.'/lib/core/Event.class.php');
require_once(ROOT_DIR.'/lib/core/routersnotassigned.class.php');
require_once(ROOT_DIR.'/lib/core/config.class.php');
require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');

class RouterEditor {
	public function insertNewRouter() {
		$check_router_hostname_exist = Router_old::getRouterByHostname($_POST['hostname']);
		if(!isset($_POST['allow_router_auto_assign'])) {
			$_POST['allow_router_auto_assign'] = 0;
			$_POST['router_auto_assign_login_string'] = '';
		}
		
		if($_POST['allow_router_auto_assign'] == '1' AND !empty($_POST['router_auto_assign_login_string'])) {
			$check_router_auto_assign_login_string = Router_old::getRouterByAutoAssignLoginString($_POST['router_auto_assign_login_string']);
		}
		
		if(empty($_POST['hostname'])) {
			$message[] = array("Bitte geben Sie einen Hostname an.", 2);
			Message::setMessage($message);
			return array("result"=>false, "router_id"=>$router_id);
		} elseif (!empty($check_router_hostname_exist)) {
			$message[] = array("Ein Router mit dem Hostnamen $_POST[hostname] existiert bereits, bitte wählen Sie einen anderen Hostnamen.", 2);
			Message::setMessage($message);
			return array("result"=>false, "router_id"=>$router_id);
		} elseif (!preg_match('/^([a-zA-Z0-9])+$/i', $_POST['hostname'])) {
			$message[] = array("Der Hostname enthält ein oder mehr ungültige Zeichen. Erlaubt sind [a-zA-Z0-9]", 2);
			Message::setMessage($message);
			return array("result"=>false, "router_id"=>$router_id);
		} elseif (!empty($check_router_auto_assign_login_string)) {
			$message[] = array("Der Router Auto Assign Login String wird bereits verwendet.", 2);
			Message::setMessage($message);
			return array("result"=>false, "router_id"=>$router_id);
		} elseif($_POST['allow_router_auto_assign'] == '1' AND
			($_POST['router_auto_assign_login_string'] == "Mac-Adresse..." OR empty($_POST['router_auto_assign_login_string']) OR ctype_space ($_POST['router_auto_assign_login_string']))) {
			$message[] = array("Wenn Automatische Routerzuweisung aktiviert ist, muss eine Mac-Adresse gesetzt werden.", 2);
			$message[] = array("Du findest die Mac-Adresse oft auf der Rückseite des Routers.", 0);
			Message::setMessage($message);
			return array("result"=>false, "router_id"=>$router_id);
		} else {
			if(!is_numeric($_POST['latitude']) OR !is_numeric($_POST['longitude'])) {
				$_POST['latitude'] = 0;
				$_POST['longitude'] = 0;
			}
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO routers (user_id, create_date, update_date, crawl_method, hostname, allow_router_auto_assign, router_auto_assign_login_string, description, location, latitude, longitude, chipset_id)
								    VALUES (?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$stmt->execute(array($_SESSION['user_id'], $_POST['crawl_method'], $_POST['hostname'], $_POST['allow_router_auto_assign'], $_POST['router_auto_assign_login_string'], $_POST['description'], $_POST['location'], $_POST['latitude'], $_POST['longitude'], $_POST['chipset_id']));
				$router_id = DB::getInstance()->lastInsertId();
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			$crawl_cycle_id = Crawling::getLastEndedCrawlCycle();
			$router_status = New RouterStatus(false, (int)$crawl_cycle_id['id'], (int)$router_id, "offline");
			$router_status->store();
			
			if($_POST['allow_router_auto_assign']=='1' AND !empty($_POST['router_auto_assign_login_string'])) {
				RoutersNotAssigned::deleteByAutoAssignLoginString($_POST['router_auto_assign_login_string']);
			}
			$message[] = array("Der Router $_POST[hostname] wurde angelegt.", 1);
			
			//Add event for new router
			//TODO: add Router Object to data array
			$event = new Event(false, 'router', (int)$router_id, 'new', array());
			$event->store();
			
			//Send Message to twitter
			if($_POST['twitter_notification']=='1') {
				Message::postTwitterMessage("Neuer #Freifunk Knoten in #Oldenburg! Wo? Schau nach: ".Config::getConfigValueByName('url_to_netmon')."/router_status.php?router_id=$router_id");
			}

			Message::setMessage($message);
			return array("result"=>true, "router_id"=>$router_id);
		}
	}

	public function resetRouterAutoAssignHash($router_id) {
		try {
			$stmt = DB::getInstance()->prepare("UPDATE routers SET
									router_auto_assign_hash = ''
							    WHERE id=?");
			$stmt->execute(array($router_id));
			$result = $stmt->rowCount();
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		if ($result) {
			$message[] = array("Der Auto Assign Hash wurde zurückgesetzt.", 1);
			Message::setMessage($message);
			return true;
		} else {
			$message[] = array("Beim Zurücksetzen des Auto Assign Hashes ist ein Fehler aufgetreten.", 2);
			Message::setMessage($message);
			return false;
		}
	}

	public function insertEditRouter() {
		$check_router_hostname_exist = Router_old::getRouterByHostname($_POST['hostname']);
		$check_router_auto_assign_login_string = Router_old::getRouterByAutoAssignLoginString($_POST['router_auto_assign_login_string']);
		$router_data = Router_old::getRouterInfo($_GET['router_id']);

		if(empty($_POST['hostname'])) {
			$message[] = array("Bitte geben sie einen Hostname an.", 2);
			Message::setMessage($message);
			return false;
		} elseif (($router_data['hostname']!=$_POST['hostname']) AND !empty($check_router_hostname_exist)) {
			$message[] = array("Ein Router mit dem Hostnamen $_POST[hostname] existiert bereits, bitte wählen Sie einen anderen Hostnamen.", 2);
			Message::setMessage($message);
			return false;
		} elseif (!preg_match('/^([a-zA-Z0-9])+$/i', $_POST['hostname'])) {
			$message[] = array("Der Hostname enthält ein oder mehr ungültige Zeichen. Erlaubt sind [a-zA-Z0-9]", 2);
			Message::setMessage($message);
			return false;
		} elseif (($router_data['router_auto_assign_login_string']!=$_POST['router_auto_assign_login_string']) AND !empty($check_router_auto_assign_login_string)) {
			$message[] = array("Der Router Auto Assign Login String wird bereits verwendet.", 2);
			Message::setMessage($message);
			return false;
		} else {
			if(!is_numeric($_POST['latitude']) OR !is_numeric($_POST['longitude'])) {
				$_POST['latitude'] = 0;
				$_POST['longitude'] = 0;
			}
			
			try {
				$stmt = DB::getInstance()->prepare("UPDATE routers SET
										update_date=NOW(),
										crawl_method=?,
										hostname=?,
										allow_router_auto_assign=?,
										router_auto_assign_login_string=?,
										description=?,
										location=?,
										latitude=?,
										longitude=?,
										chipset_id=?
								    WHERE id = ?");
				$stmt->execute(array($_POST['crawl_method'], $_POST['hostname'], $_POST['allow_router_auto_assign'], $_POST['router_auto_assign_login_string'], $_POST['description'], $_POST['location'], $_POST['latitude'], $_POST['longitude'], $_POST['chipset_id'], $_GET['router_id']));
				$result = $stmt->rowCount();
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if($_POST['allow_router_auto_assign']=='1' AND !empty($_POST['router_auto_assign_login_string'])) {
				RoutersNotAssigned::deleteByAutoAssignLoginString($_POST['router_auto_assign_login_string']);
			}
			
			if ($result>0) {
				$message[] = array("Die Änderungen am Router $_POST[hostname] wurden gespeichert.", 1);
				Message::setMessage($message);
				return true;
			} else {
				$message[] = array("Beim Ändern des Routers ".$_POST['hostname']." ist ein Fehler aufgetreten.", 2);
				Message::setMessage($message);
				return false;
			}
		}
	}

	public function insertEditHash($router_id, $hash) {
			try {
				$stmt = DB::getInstance()->prepare("UPDATE routers SET
								router_auto_assign_hash=?
							WHERE id = ?");
				$stmt->execute(array($hash, $router_id));
				$result = $stmt->rowCount();
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			if ($result) {
				$message[] = array("Der geänderte Hash wurde gespeichert.", 1);
				Message::setMessage($message);
				return true;
			} else {
				$message[] = array("Beim Ändern des Hashes ist ein Fehler aufgetreten.", 2);
				Message::setMessage($message);
				return false;
			}
	}

	public function insertDeleteRouter($router_id) {
		$router_data=Router_old::getRouterInfo($router_id);
		
		//Delete all Interfaces of the router
		$interfaces = Interfaces::getInterfacesByRouterId($router_id);
		foreach($interfaces as $interface) {
			Interfaces::deleteInterface($interface['interface_id']);
		}

		//Delete all services of the router
		//TODO update this to new Service class
/*		$services = Service::getServicesByRouterId($router_id);
		foreach($services as $service) {
			ServiceEditor::deleteService($service['id']);
		}*/

		//Delete all crawl data of the router
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_batman_advanced_interfaces WHERE router_id=?");
			$stmt->execute(array($router_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_batman_advanced_originators WHERE router_id=?");
			$stmt->execute(array($router_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_interfaces WHERE router_id=?");
			$stmt->execute(array($router_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_olsr WHERE router_id=?");
			$stmt->execute(array($router_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_routers WHERE router_id=?");
			$stmt->execute(array($router_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		//delete other data assigned to the router
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM variable_splash_clients WHERE router_id=?");
			$stmt->execute(array($router_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM router_adds WHERE router_id=?");
			$stmt->execute(array($router_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		//Delete the router itself
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM routers WHERE id=?");
			$stmt->execute(array($router_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		$message[] = array("Der Router $router_data[hostname] wurde gelöscht.", 1);
		Message::setMessage($message);
		return true;
	}
}

?>