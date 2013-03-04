<?php
	require_once('runtime.php');
	require_once('lib/classes/core/ipeditor.class.php');
	require_once('lib/classes/core/interfaces.class.php');
	require_once('lib/classes/core/project.class.php');
	require_once('lib/classes/core/ip.class.php');
	require_once('lib/classes/core/router.class.php');
	
	if ($_GET['section'] == "add") {
		$router_data = Router::getRouterInfo($_GET['router_id']);
		//Moderator and owning user can add ip to interface
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			$smarty->assign('message', Message::getMessage());
			
			$smarty->assign('router_interfaces', Interfaces::getInterfacesByRouterId($_GET['router_id']));
			$smarty->assign('projects', Project::getProjects(true));

			$smarty->display("header.tpl.php");
			$smarty->display("ip_add.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Du hast nicht genügend Rechte um diesem Router/Interface eine IP hinzuzufügen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "insert") {
		$router_data = Router::getRouterInfo($_GET['router_id']);
		//Moderator and owning user can add ip to interface
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			$smarty->assign('message', Message::getMessage());

			if(!empty($_POST['ipv4_addr'])) {
				Ip::addIPv4Address($_GET['router_id'], $_POST['project_id'], $_POST['interface_id'], $_POST['ipv4_addr']);
			}

			if(!empty($_POST['ipv6_addr'])) {
				Ip::addIPv6Address($_GET['router_id'], $_POST['project_id'], $_POST['interface_id'], $_POST['ipv6_addr']);
			}

			$message[] = array("Du hast nicht genügend Rechte um diesem Router/Interface eine IP hinzuzufügen!", 2);
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "delete") {
		$router_data = Router::getRouterByIpId($_GET['ip_id']);
		//Moderator and owning user can add ip to interface
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			$smarty->assign('message', Message::getMessage());

			Ip::deleteIPAddress($_GET['ip_id']);
			header("Location: ./router_config.php?router_id=$_GET[router_id]");
		} else {
			$message[] = array("Du hast nicht genügend Rechte um diese IP zu löschen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
?>