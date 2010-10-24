<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/router.class.php');
  require_once('./lib/classes/core/routereditor.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
    
	if ($_GET['section'] == "new") {
		if (UserManagement::checkPermission(4)) {
			$smarty->assign('message', Message::getMessage());
			$smarty->assign('chipsets', Helper::getChipsets());
			
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
		if (UserManagement::checkPermission(4)) {
			$insert_result = RouterEditor::insertNewRouter();

			header('Location: ./router_config.php?router_id='.$insert_result['router_id']);
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Router anlegen oder editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "edit") {
		if (UserManagement::checkPermission(4)) {
			$smarty->assign('message', Message::getMessage());

			/** Get and assign Router Informations **/
			$router_data = Router::getRouterInfo($_GET['router_id']);
			$smarty->assign('router_data', $router_data);

			$smarty->assign('chipsets', Helper::getChipsets());
			
			$smarty->display("header.tpl.php");
			$smarty->display("router_edit.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Router editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert_edit") {
		if (UserManagement::checkPermission(4)) {
			$insert_result = RouterEditor::insertEditRouter();

			header('Location: ./router_config.php?router_id='.$_GET['router_id']);
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Router anlegen oder editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert_reset_auto_assign_hash") {
		if (UserManagement::checkPermission(4)) {
			$insert_result = RouterEditor::resetRouterAutoAssignHash($_GET['router_id']);

			header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Router anlegen oder editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert_delete") {
		if (UserManagement::checkPermission(4)) {
			if($_POST['really_delete']==1) {
				$insert_result = RouterEditor::insertDeleteRouter($_GET['router_id']);
				header('Location: ./desktop.php');
			} else {
				$message[] = array("Zum löschen des Routers ist eine Bestätigung erforderlich!", 2);
				Message::setMessage($message);
				header('Location: ./routereditor.php?section=edit&router_id='.$_GET['router_id']);
			}
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Router anlegen oder editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}



?>