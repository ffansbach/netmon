<?php
	require_once('./config/runtime.inc.php');
	require_once('./lib/classes/core/editinghelper.class.php');
	require_once('./lib/classes/core/router.class.php');
	require_once('./lib/classes/core/service.class.php');

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
			$add_result = Service::addService($_GET['router_id'], $_POST['title'], $_POST['description'], $_POST['port'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait'], $_POST['use_netmons_url'], $_POST['url']);
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


    if ($_GET['section'] == "insert_service") {
		if (UserManagement::checkPermission(4)) {
			$add_result = EditingHelper::addIpTyp($_GET['ip_id'], $_POST['title'], $_POST['description'], $_POST['typ'], $_POST['crawler'], $_POST['port'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait'], $_POST['use_netmons_url'], $_POST['url']);
			if(!$add_result['result']) {
				header('Location: ./serviceeditor.php?section=new&ip_id='.$_GET['ip_id']);
			} else {
				header('Location: ./service.php?service_id='.$add_result['service_id']);
			}

		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
    }

    if ($_GET['section'] == "edit") {
	$service_data = Helper::getServiceDataByServiceId($_GET['service_id']);
	if (!UserManagement::checkIfUserIsOwnerOrPermitted(64, $service_data['user_id']))
		UserManagement::denyAccess();

	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('servicedata', $service_data);
	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("service_edit.tpl.php");
	$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "insert_edit") {
	$edit_result = $ServiceEditor->insertEditService($_GET['service_id'], $_POST['typ'], $_POST['crawler'], $_POST['title'], $_POST['description'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait'], $_POST['use_netmons_url'], $_POST['url']);
	header('Location: ./service.php?service_id='.$edit_result['service_id']);
    }
    if ($_GET['section'] == "delete") {
		$ip = Helper::getIpIdByServiceId($_GET['service_id']);
		if ($ServiceEditor->deleteService($_GET['service_id']))
			header('Location: ./ip.php?id='.$ip['id']);
		else
			header('Location: ./serviceeditor.php?section=edit&service_id='.$_GET['service_id']);
    }
?>