<?php
	require_once('runtime.php');
	require_once('./lib/classes/core/helper.class.php');
	require_once('./lib/classes/core/interfaces.class.php');
	require_once('./lib/classes/core/project.class.php');
	require_once('./lib/classes/core/router.class.php');
	
	$smarty->assign('google_maps_api_key', Config::getConfigValueByName('google_maps_api_key'));
	
	if ($_GET['section'] == "add") {
		$router_data = Router::getRouterInfo($_GET['router_id']);
		//Moderator and owning user can add interface to router
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			$smarty->assign('message', Message::getMessage());
			$smarty->assign('projects', Project::getProjects());
			$smarty->assign('router_data', Router::getRouterInfo($_GET['router_id']));

			$smarty->display("header.tpl.php");
			$smarty->display("interface_add.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Du hast nicht genügend Rechte um dem Router ein Interface hinzuzufügen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
	
	if ($_GET['section'] == "insert_add") {
		$router_data = Router::getRouterInfo($_GET['router_id']);
		//Moderator and owning user can add interface to router
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			Interfaces::addNewInterface($_GET['router_id'], $_POST['project_id'], $_POST['name'], $_POST['ipv4_addr'], $_POST['ipv6_addr'], $_POST['ipv4_dhcp_range']);
			header('Location: ./router_config.php?router_id='.$_GET['router_id']);

		} else {
			$message[] = array("Du hast nicht genügend Rechte um dem Router ein Interface hinzuzufügen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
	if ($_GET['section'] == "delete") {
		$interface_data = Interfaces::getInterfaceByInterfaceId($_GET['interface_id']);
		$router_data = Router::getRouterInfo($interface_data['router_id']);

		//Moderator and owning user can delete interface from router
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $router_data['user_id'])) {
			Interfaces::deleteInterface($_GET['interface_id']);
			header("Location: ./router_config.php?router_id=$interface_data[router_id]");

		} else {
			$message[] = array("Du hast nicht genügend Rechte um das Interface zu löschen!", 2);
			Message::setMessage($message);
			header("Location: ./router_config.php?router_id=$interface_data[router_id]");
		}
	}
?>