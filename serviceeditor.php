<?php
	require_once('runtime.php');
	require_once('./lib/classes/core/editinghelper.class.php');
	require_once('./lib/classes/core/router.class.php');
	require_once('./lib/classes/core/service.class.php');
	require_once('./lib/classes/core/serviceeditor.class.php');

	if ($_GET['section'] == "add") {
		if (UserManagement::checkPermission(4)) {
			$smarty->assign('message', Message::getMessage());

			$smarty->assign('router_data', Router::getRouterInfo($_GET['router_id']));	
			$smarty->display("header.tpl.php");
			$smarty->display("add_service.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert_add") {
		if (UserManagement::checkPermission(4)) {
			$add_result = ServiceEditor::addService($_GET['router_id'], $_POST['title'], $_POST['description'], $_POST['port'], $_POST['url_prefix'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait'], $_POST['use_netmons_url'], $_POST['url']);
			if($add_result['result']) {
				header('Location: ./router_config.php?router_id='.$add_result['router_id']);
			} else {
				header('Location: ./serviceeditor.php?section=add&router_id='.$_GET['router_id']);
			}

		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

    if ($_GET['section'] == "edit") {
	$service_data = Service::getServiceByServiceId($_GET['service_id']);
	$smarty->assign('service_data', $service_data);

	$smarty->display("header.tpl.php");
	$smarty->display("service_edit.tpl.php");
	$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "insert_edit") {
		if (UserManagement::checkPermission(4)) {
			$edit_result = ServiceEditor::insertEditService($_GET['service_id'], $_POST['title'], $_POST['description'], $_POST['port'], $_POST['url_prefix'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait'], $_POST['use_netmons_url'], $_POST['url']);
			if($edit_result['result']) {
				header('Location: ./router_config.php?router_id='.$edit_result['router_id']);
			} else {
				header('Location: ./serviceeditor.php?section=edit&service_id='.$edit_result['service_id']);
			}

		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
    }

    if ($_GET['section'] == "delete") {
		if (UserManagement::checkPermission(4)) {
				$service_data = Service::getServiceByServiceId($_GET['service_id']);
				$insert_result = ServiceEditor::deleteService($_GET['service_id']);
				header('Location: ./router_config.php?router_id='.$service_data['router_id']);
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Router anlegen oder editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
    }
?>