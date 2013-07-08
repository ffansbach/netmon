<?php
	require_once('runtime.php');
	require_once('lib/classes/core/dns.class.php');
	require_once('lib/classes/core/ip.class.php');
	require_once('lib/classes/core/router.class.php');

	$smarty->assign('message', Message::getMessage());
	if ($_GET['section'] == "add_host") {
		$smarty->assign('ipv4_ips', IP::getExistingIps(4));
		$smarty->assign('ipv6_ips', IP::getExistingIps(6));

		$smarty->display("header.tpl.php");
		$smarty->display("host_add.tpl.php");
		$smarty->display("footer.tpl.php");
	}

	if ($_GET['section'] == "insert_add_host") {
		//Logged in users can add a new router
		if (Permission::checkPermission(4)) {
			$result = Dns::AddHost($_POST['host'], $_POST['ipv4_id'], $_POST['ipv6_id'], $_SESSION['user_id']);
			if($result)
				header("Location: ./user.php?user_id=$_SESSION[user_id]");
			else 
				header("Location: ./dnseditor.php?section=add_host");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Host anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "edit_host") {
		$host_data = DNS::getHostById($_GET['host_id']);
		//Moderator and owning user can add ip to interface
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $host_data['user_id'])) {
			$smarty->assign('host_data', $host_data);
			
			$smarty->display("header.tpl.php");
			$smarty->display("host_edit.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Host anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

	if ($_GET['section'] == "delete_host") {
		$host_data = DNS::getHostById($_GET['host_id']);
		//Moderator and owning user can add ip to interface
		if (Permission::checkIfUserIsOwnerOrPermitted(16, $host_data['user_id'])) {
			if($_POST['really_delete']=='1') {
				DNS::deleteHost($_GET['host_id']);
				header("Location: ./user.php?user_id=$_SESSION[user_id]");
			} else {
				$message[] = array("Zum Löschen bitte das Häckchen setzen!", 2);
				Message::setMessage($message);
				header("Location: ./dnseditor.php?section=edit_host&host_id=$_GET[host_id]");
			}
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Host anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}



/*
	if ($_GET['section'] == "insert") {
		$router_data = Router_old::getRouterInfo($_GET['router_id']);
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
		$router_data = Router_old::getRouterByIpId($_GET['ip_id']);
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
	}*/
?>