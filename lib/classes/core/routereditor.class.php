<?php

require_once('lib/classes/core/interfaces.class.php');
require_once('lib/classes/core/service.class.php');
require_once('lib/classes/core/serviceeditor.class.php');
require_once('lib/classes/core/crawling.class.php');

class RouterEditor {
	public function insertNewRouter() {
		$check_router_hostname_exist = Router::getRouterByHostname($_POST['hostname']);
		if(!isset($_POST['allow_router_auto_assign'])) {
			$_POST['allow_router_auto_assign'] = 0;
			$_POST['router_auto_assign_login_string'] = '';
		}
		
		if($_POST['allow_router_auto_assign'] == '1' AND !empty($_POST['router_auto_assign_login_string'])) {
			$check_router_auto_assign_login_string = Router::getRouterByAutoAssignLoginString($_POST['router_auto_assign_login_string']);
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
				$stmt = DB::getInstance()->prepare("INSERT INTO routers (user_id, create_date, update_date, crawl_method, hostname, allow_router_auto_assign, router_auto_assign_login_string, description, location, latitude, longitude, chipset_id, notify, notification_wait)
								    VALUES (?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$stmt->execute(array($_SESSION['user_id'], $_POST['crawl_method'], $_POST['hostname'], $_POST['allow_router_auto_assign'], $_POST['router_auto_assign_login_string'], $_POST['description'], $_POST['location'], $_POST['latitude'], $_POST['longitude'], $_POST['chipset_id'], $_POST['notify'], $_POST['notification_wait']));
				$router_id = DB::getInstance()->lastInsertId();
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			$crawl_data['status'] = "unknown";
			$crawl_cycle_id = Crawling::getLastEndedCrawlCycle();
			Crawling::insertRouterCrawl($router_id, $crawl_data, $crawl_cycle_id);
			
			if($_POST['allow_router_auto_assign']=='1' AND !empty($_POST['router_auto_assign_login_string'])) {
				try {
					$stmt = DB::getInstance()->prepare("DELETE FROM routers_not_assigned
									    WHERE router_auto_assign_login_string=?
									    LIMIT 1");
					$stmt->execute(array($_POST['router_auto_assign_login_string']));
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			$message[] = array("Der Router $_POST[hostname] wurde angelegt.", 1);
			
			//Make history
			History::addHistoryEntry('router', $router_id, serialize(array('router_id'=>$router_id, 'action'=>'new')));
			
			//Send Message to twitter
			if($_POST['twitter_notification']=='1') {
				Message::postTwitterMessage("Neuer #Freifunk Knoten in #Oldenburg! Wo? Schau nach: http://netmon.freifunk-ol.de/router_status.php?router_id=$router_id");
			}

			Message::setMessage($message);
			return array("result"=>true, "router_id"=>$router_id);
		}
	}

	public function resetRouterAutoAssignHash($router_id) {
		try {
			$stmt = DB::getInstance()->prepare("UPDATE routers SET
									router_auto_assign_hash = '',
									trying_to_assign_notified = 0
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
		$check_router_hostname_exist = Router::getRouterByHostname($_POST['hostname']);
		$check_router_auto_assign_login_string = Router::getRouterByAutoAssignLoginString($_POST['router_auto_assign_login_string']);
		$router_data = Router::getRouterInfo($_GET['router_id']);

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
										chipset_id=?,
										notify=?,
										notification_wait=?
								    WHERE id = ?");
				$stmt->execute(array($_POST['crawl_method'], $_POST['hostname'], $_POST['allow_router_auto_assign'], $_POST['router_auto_assign_login_string'], $_POST['description'], $_POST['location'], $_POST['latitude'], $_POST['longitude'], $_POST['chipset_id'], $_POST['notify'], $_POST['notification_wait'], $_GET['router_id']));
				$result = $stmt->rowCount();
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
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
		$router_data=Router::getRouterInfo($router_id);
		
		//Delete all Interfaces of the router
		$interfaces = Interfaces::getInterfacesByRouterId($router_id);
		foreach($interfaces as $interface) {
			Interfaces::deleteInterface($interface['interface_id']);
		}

		//Delete all services of the router
		$services = Service::getServicesByRouterId($router_id);
		foreach($services as $service) {
			ServiceEditor::deleteService($service['id']);
		}

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
			$stmt = DB::getInstance()->prepare("DELETE FROM crawl_clients_count WHERE router_id=?");
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