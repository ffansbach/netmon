<?php
	require_once('runtime.php');
	require_once('./lib/classes/core/router.class.php');
	require_once('./lib/classes/core/service.class.php');
	require_once('./lib/classes/core/serviceeditor.class.php');

	if ($_GET['section'] == "add") {
		$router_data = Router_old::getRouterInfo($_GET['router_id']);
		//Root and owning user can add service to router
		if (Permission::checkIfUserIsOwnerOrPermitted(64, $router_data['user_id'])) {
			$smarty->assign('message', Message::getMessage());
			$smarty->assign('ips', Ip_old::getIpAddressesByRouterId($_GET['router_id']));

			$smarty->assign('router_data', Router_old::getRouterInfo($_GET['router_id']));	
			$smarty->display("header.tpl.php");
			$smarty->display("add_service.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Du hast nicht genügend Rechte um dem Router einen Dienst hinzuzufügen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert_add") {
		$router_data = Router_old::getRouterInfo($_GET['router_id']);
		//Root and owning user can add service to router
		if (Permission::checkIfUserIsOwnerOrPermitted(64, $router_data['user_id'])) {
			$add_result = ServiceEditor::addService($_GET['router_id'], $_POST['title'], $_POST['description'], $_POST['ip_addresses'], $_POST['port'], $_POST['url_prefix'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait'], $_POST['use_netmons_url'], $_POST['url']);
			if($add_result['result']) {
				header('Location: ./router_config.php?router_id='.$add_result['router_id']);
			} else {
				header('Location: ./serviceeditor.php?section=add&router_id='.$_GET['router_id']);
			}
		} else {
			$message[] = array("Du hast nicht genügend Rechte um dem Router einen Dienst hinzuzufügen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
	
	if ($_GET['section'] == "edit") {
		$service_data = Service::getServiceByServiceId($_GET['service_id']);
		$smarty->assign('service_data', $service_data);
		$router_data = Router_old::getRouterInfo($service_data['router_id']);
		//Moderator and owning user can edit service
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			$smarty->display("header.tpl.php");
			$smarty->display("service_edit.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Du hast nicht genügend Rechte um den Dienst zu editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert_edit") {
		$service_data = Service::getServiceByServiceId($_GET['service_id']);
		$smarty->assign('service_data', $service_data);
		$router_data = Router_old::getRouterInfo($service_data['router_id']);
		//Moderator and owning user can edit service
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			$edit_result = ServiceEditor::insertEditService($_GET['service_id'], $_POST['title'], $_POST['description'], $_POST['port'], $_POST['url_prefix'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait'], $_POST['use_netmons_url'], $_POST['url']);
			if($edit_result['result']) {
				header('Location: ./router_config.php?router_id='.$edit_result['router_id']);
			} else {
				header('Location: ./serviceeditor.php?section=edit&service_id='.$edit_result['service_id']);
			}
		} else {
			$message[] = array("Du hast nicht genügend Rechte um den Dienst zu editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
	
	if ($_GET['section'] == "delete") {
		$service_data = Service::getServiceByServiceId($_GET['service_id']);
		$smarty->assign('service_data', $service_data);
		$router_data = Router_old::getRouterInfo($service_data['router_id']);
		//Root and owning user can delete service
		if (Permission::checkIfUserIsOwnerOrPermitted(64, $router_data['user_id'])) {
			$insert_result = ServiceEditor::deleteService($_GET['service_id']);
			header('Location: ./router_config.php?router_id='.$service_data['router_id']);
		} else {
			$message[] = array("Du hast nicht genügend Rechte um den Dienst zu löschen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
?>