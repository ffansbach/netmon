<?php
	require_once('runtime.php');
	require_once('./lib/classes/core/router.class.php');
	require_once('./lib/classes/core/routereditor.class.php');
	require_once('./lib/classes/core/chipsets.class.php');
	
	$smarty->assign('google_maps_api_key', $GLOBALS['google_maps_api_key']);
	
	if ($_GET['section'] == "new") {
		//Logged in users can add a new router
		if (Permission::checkPermission(4)) {
			$smarty->assign('message', Message::getMessage());

			$smarty->assign('community_location_longitude', Config::getConfigValueByName('community_location_longitude'));
			$smarty->assign('community_location_latitude', Config::getConfigValueByName('community_location_latitude'));
			$smarty->assign('community_location_zoom', Config::getConfigValueByName('community_location_zoom'));
			$smarty->assign('chipsets', Chipsets::getChipsets());
			$smarty->assign('twitter_token', Config::getConfigValueByName('twitter_token'));

			$smarty->display("header.tpl.php");
			$smarty->display("router_new.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Router anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
	
	if ($_GET['section'] == "insert") {
		//Logged in users can add a new router
		if (Permission::checkPermission(4)) {
			$insert_result = RouterEditor::insertNewRouter();
			if($insert_result['result']) {
				header('Location: ./router_config.php?router_id='.$insert_result['router_id']);
			} else {
				header("Location: ./routereditor.php?section=new&router_auto_assign_login_string=$_POST[router_auto_assign_login_string]&hostname=$_POST[hostname]");
			}
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Router anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "edit") {
		$router_data = Router::getRouterInfo($_GET['router_id']);
		$smarty->assign('router_data', $router_data);
		//Moderator and owning user can edit router
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			$smarty->assign('message', Message::getMessage());
			$smarty->assign('is_root', Permission::checkPermission(120));

			/** Get and assign Router Informations **/
			$smarty->assign('chipsets', Chipsets::getChipsets());
			
			$smarty->display("header.tpl.php");
			$smarty->display("router_edit.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Du hast nicht genügend Rechte um diesen Router zu editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert_edit") {
		//Moderator and owning user can edit router
		$router_data = Router::getRouterInfo($_GET['router_id']);
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			$insert_result = RouterEditor::insertEditRouter();
			if($insert_result) {
				header('Location: ./router_config.php?router_id='.$_GET['router_id']);
			} else {
				header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
			}
		} else {
			$message[] = array("Du hast nicht genügend Rechte um diesen Router zu editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert_edit_hash") {
		//only root can edit hash
		if(Permission::checkPermission(120)) {
			$insert_result = RouterEditor::insertEditHash($_GET['router_id'], $_POST['router_auto_assign_hash']);
			if($insert_result) {
				header('Location: ./router_config.php?router_id='.$_GET['router_id']);
			} else {
				header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
			}
			
			header('Location: ./router_config.php?router_id='.$_GET['router_id']);
		} else {
			$message[] = array('Nur Root kann den Hash ändern.', 2);
			Message::setMessage($message);
			
			header('Location: ./router_config.php?router_id='.$_GET['router_id']);
		}
	}

	if ($_GET['section'] == "insert_reset_auto_assign_hash") {
		$router_data = Router::getRouterInfo($_GET['router_id']);
		//Admin and owning user can reset hash
		if (Permission::checkIfUserIsOwnerOrPermitted(32, $router_data['user_id'])) {
			$insert_result = RouterEditor::resetRouterAutoAssignHash($_GET['router_id']);

			header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
		} else {
			$message[] = array("Du hast nicht genügend Rechte um den Hash zurückzusetzen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert_delete") {
		$router_data = Router::getRouterInfo($_GET['router_id']);
		//Root and owning user can delete router
		if (Permission::checkIfUserIsOwnerOrPermitted(64, $router_data['user_id'])) {
			if($_POST['really_delete']==1) {
				$insert_result = RouterEditor::insertDeleteRouter($_GET['router_id']);
				header('Location: ./user.php?user_id='.$_SESSION['user_id']);
			} else {
				$message[] = array("Zum löschen des Routers ist eine Bestätigung erforderlich!", 2);
				Message::setMessage($message);
				header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
			}
		} else {
			$message[] = array("Du hast nicht genügend Rechte um den Router zu löschenn!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
?>