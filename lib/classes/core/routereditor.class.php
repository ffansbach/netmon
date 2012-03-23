<?php

require_once('lib/classes/core/interfaces.class.php');
require_once('lib/classes/core/service.class.php');
require_once('lib/classes/core/serviceeditor.class.php');
require_once('lib/classes/core/crawling.class.php');

class RouterEditor {
	public function insertNewRouter() {
		$check_router_hostname_exist = Router::getRouterByHostname($_POST['hostname']);
		if(!empty($_POST['router_auto_assign_login_string'])) {
			$check_router_auto_assign_login_string = Router::getRouterByAutoAssignLoginString($_POST['router_auto_assign_login_string']);
		}

		if(empty($_POST['hostname'])) {
			$message[] = array("Bitte geben sie einen Hostname an.", 2);
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
		} else {
			if(!is_numeric($_POST['latitude']) OR !is_numeric($_POST['longitude'])) {
				$_POST['latitude'] = 0;
				$_POST['longitude'] = 0;
			}

			DB::getInstance()->exec("INSERT INTO routers (user_id, create_date, update_date, crawl_method, hostname, allow_router_auto_assign, router_auto_assign_login_string, description, location, latitude, longitude, chipset_id, notify, notification_wait)
						      VALUES ('$_SESSION[user_id]', NOW(), NOW(), '$_POST[crawl_method]', '$_POST[hostname]', '$_POST[allow_router_auto_assign]', '$_POST[router_auto_assign_login_string]', '$_POST[description]', '$_POST[location]', '$_POST[latitude]', '$_POST[longitude]', '$_POST[chipset_id]', '$_POST[notify]', '$_POST[notification_wait]');");
			$router_id = DB::getInstance()->lastInsertId();
			
			$crawl_data['status'] = "unknown";
			$crawl_cycle_id = Crawling::getLastEndedCrawlCycle();
			Crawling::insertRouterCrawl($router_id, $crawl_data, $crawl_cycle_id);
			
			if($_POST['allow_router_auto_assign']=='1' AND !empty($_POST['router_auto_assign_login_string'])) {
				try {
					$result = DB::getInstance()->exec("DELETE FROM routers_not_assigned
									   WHERE router_auto_assign_login_string='$_POST[router_auto_assign_login_string]'
									   LIMIT 1;");
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
			}

			$message[] = array("Der Router $_POST[hostname] wurde angelegt.", 1);


			//Make history
			$actual_crawl_cycle = Crawling::getActualCrawlCycle();
			$history_data = serialize(array('router_id'=>$router_id, 'action'=>'new'));
			DB::getInstance()->exec("INSERT INTO history (crawl_cycle_id, object, object_id, create_date, data) VALUES ('$actual_crawl_cycle[id]', 'router', '$router_id', NOW(), '$history_data');");
			
			//Send Message to twitter
			if($_POST['twitter_notification']=='1') {
				Message::postTwitterMessage("Neuer #Freifunk Knoten in #Oldenburg! Wo? Schau nach: http://netmon.freifunk-ol.de/router_status.php?router_id=$router_id");
				$message[] = array("Der neue Router wird auf dem Twitteraccount von <a href=\"http://twitter.com/$GLOBALS[twitter_username]\">$GLOBALS[twitter_username]</a> angekündigt.", 1);
			}

			Message::setMessage($message);
			return array("result"=>true, "router_id"=>$router_id);
		}
	}

	public function resetRouterAutoAssignHash($router_id) {
		$result = DB::getInstance()->exec("UPDATE routers SET
							router_auto_assign_hash = '',
							trying_to_assign_notified = 0
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
			$result = DB::getInstance()->exec("UPDATE routers SET
								update_date=NOW(),
								crawl_method='$_POST[crawl_method]',
								hostname='$_POST[hostname]',
								allow_router_auto_assign='$_POST[allow_router_auto_assign]',
								router_auto_assign_login_string='$_POST[router_auto_assign_login_string]',
								description='$_POST[description]',
								location='$_POST[location]',
								latitude='$_POST[latitude]',
								longitude='$_POST[longitude]',
								chipset_id='$_POST[chipset_id]',
								notify='$_POST[notify]',
								notification_wait='$_POST[notification_wait]'
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

		//Delete the router itself
		DB::getInstance()->exec("DELETE FROM routers WHERE id='$router_id';");

		$message[] = array("Der Router $router_data[hostname] wurde gelöscht.", 1);
		Message::setMessage($message);
		return true;
	}
}

?>