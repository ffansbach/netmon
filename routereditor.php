<?php
	require_once('runtime.php');
	require_once('./lib/core/router.class.php');
	require_once('./lib/core/routereditor.class.php');
	require_once('./lib/core/Chipsetlist.class.php');
	require_once('./lib/core/chipsets.class.php');
	require_once('./lib/core/ConfigLine.class.php');
	
	$smarty->assign('google_maps_api_key', $GLOBALS['google_maps_api_key']);
	
	if ($_GET['section'] == "new") {
		//Logged in users can add a new router
		if(Permission::checkPermission(PERM_USER)) {
			$smarty->assign('message', Message::getMessage());

			$smarty->assign('google_maps_api_key', ConfigLine::configByName('google_maps_api_key'));
			$smarty->assign('community_location_longitude', ConfigLine::configByName('community_location_longitude'));
			$smarty->assign('community_location_latitude', ConfigLine::configByName('community_location_latitude'));
			$smarty->assign('community_location_zoom', ConfigLine::configByName('community_location_zoom'));
			$smarty->assign('twitter_token', ConfigLine::configByName('twitter_token'));
			
			$chipsetlist = new Chipsetlist(false, false, false, 0, -1);
			$smarty->assign('chipsetlist', $chipsetlist->getList());

			$smarty->display("header.tpl.html");
			$smarty->display("router_new.tpl.html");
			$smarty->display("footer.tpl.html");
		} else {
			Permission::denyAccess(PERM_USER);
		}
	}
	
	if ($_GET['section'] == "insert") {
		//Logged in users can add a new router
		if(Permission::checkPermission(PERM_USER)) {
			$insert_result = RouterEditor::insertNewRouter();
			if($insert_result['result']) {
				header('Location: ./router.php?router_id='.$insert_result['router_id']);
			} else {
				header("Location: ./routereditor.php?section=new&router_auto_assign_login_string=$_POST[router_auto_assign_login_string]&hostname=$_POST[hostname]");
			}
		} else {
			Permission::denyAccess(PERM_USER);
		}
	}

	if ($_GET['section'] == "edit") {
		$router_data = Router_old::getRouterInfo($_GET['router_id']);
		$smarty->assign('router_data', $router_data);
		//Moderator and owning user can edit router
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, (int)$router_data['user_id'])) {
			$smarty->assign('community_location_longitude', Config::getConfigValueByName('community_location_longitude'));
			$smarty->assign('community_location_latitude', Config::getConfigValueByName('community_location_latitude'));
			$smarty->assign('community_location_zoom', Config::getConfigValueByName('community_location_zoom'));
			
			$smarty->assign('message', Message::getMessage());
			$smarty->assign('is_root', Permission::checkPermission(PERM_ROOT));

			/** Get and assign Router Informations **/
			$smarty->assign('chipsets', Chipsets::getChipsets());
			
			$smarty->display("header.tpl.html");
			$smarty->display("router_edit.tpl.html");
			$smarty->display("footer.tpl.html");
		} else {
			Permission::denyAccess(PERM_ROOT, (int)$router_data['user_id']);
		}
	}

	if ($_GET['section'] == "insert_edit") {
		//Moderator and owning user can edit router
		$router_data = Router_old::getRouterInfo($_GET['router_id']);
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, (int)$router_data['user_id'])) {
			$insert_result = RouterEditor::insertEditRouter();
			if($insert_result) {
				header('Location: ./router.php?router_id='.$_GET['router_id']);
			} else {
				header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
			}
		} else {
			Permission::denyAccess(PERM_ROOT, (int)$router_data['user_id']);
		}
	}

	if ($_GET['section'] == "insert_edit_hash") {
		$router_data = Router_old::getRouterInfo($_GET['router_id']);
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, (int)$router_data['user_id'])) {
			$insert_result = RouterEditor::insertEditHash($_GET['router_id'], $_POST['router_auto_assign_hash']);
			if($insert_result) {
				header('Location: ./router.php?router_id='.$_GET['router_id']);
			} else {
				header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
			}
			
			header('Location: ./router.php?router_id='.$_GET['router_id']);
		} else {
			Permission::denyAccess(PERM_ROOT, (int)$router_data['user_id']);
		}
	}

	if ($_GET['section'] == "insert_reset_auto_assign_hash") {
		$router_data = Router_old::getRouterInfo($_GET['router_id']);
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, (int)$router_data['user_id'])) {
			$insert_result = RouterEditor::resetRouterAutoAssignHash($_GET['router_id']);

			header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
		} else {
			Permission::denyAccess(PERM_ROOT, (int)$router_data['user_id']);
		}
	}

	if ($_GET['section'] == "insert_delete") {
		$router_data = Router_old::getRouterInfo($_GET['router_id']);
		//Root and owning user can delete router
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, (int)$router_data['user_id'])) {
			if($_POST['really_delete']==1) {
				$insert_result = RouterEditor::insertDeleteRouter($_GET['router_id']);
				header('Location: ./user.php?user_id='.$_SESSION['user_id']);
			} else {
				$message[] = array("Zum löschen des Routers ist eine Bestätigung erforderlich!", 2);
				Message::setMessage($message);
				header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
			}
		} else {
			Permission::denyAccess(PERM_ROOT, (int)$router_data['user_id']);
		}
	}
?>