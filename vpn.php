<?php

	require_once('runtime.php');
	require_once('./lib/classes/core/vpn.class.php');
	require_once('./lib/classes/core/interfaces.class.php');
	require_once('./lib/classes/core/router.class.php');
	require_once('./lib/classes/core/user.class.php');
	require_once('./lib/classes/core/project.class.php');

	$smarty->assign('message', Message::getMessage());

	if ($_GET['section'] == "new") {
		$interface = Interfaces::getInterfaceByInterfaceId($_GET['interface_id']);
		$router = Router::getRouterInfo($interface['router_id']);
		$project = Project::getProjectData($interface['project_id']);
		$user = User::getUserByID($router['user_id']);

		$smarty->assign('interface', $interface);
		$smarty->assign('router', $router);
		$smarty->assign('project', $project);
		$smarty->assign('user', $user);

		$smarty->assign('expiration', $GLOBALS['expiration']);

		$smarty->display("header.tpl.php");
		$smarty->display("sslcertificate_new.tpl.php");
		$smarty->display("footer.tpl.php");
    }

	if ($_GET['section'] == "generate") {
		$interface = Interfaces::getInterfaceByInterfaceId($_GET['interface_id']);

		$keys = Vpn::generateKeys($_GET['interface_id'], $_POST['organizationalunitname'], $_POST['commonname'], $_POST['emailaddress'], $_POST['privkeypass'], $_POST['privkeypass_chk'], $_POST['numberofdays']);

		if ($keys['return']) {
			Vpn::saveKeysToDB($_GET['interface_id'], $keys['vpn_client_cert'], $keys['vpn_client_key']);
			Vpn::writeCCD($_GET['interface_id']);
			header('Location: ./vpn.php?section=download&interface_id='.$_GET['interface_id']);
		} else {
			header('Location: ./vpn.php?section=new&interface_id='.$_GET['interface_id']);
		}
	}

	if ($_GET['section'] == "download") {
		$interface = Interfaces::getInterfaceByInterfaceId($_GET['interface_id']);
		$project = Project::getProjectData($interface['project_id']);
		$router = Router::getRouterInfo($interface['router_id']);

		if (!empty($project['vpn_server_ca_crt']) AND !empty($interface['vpn_client_cert']) AND !empty($interface['vpn_client_key'])) {
			Vpn::downloadKeyBundle($_GET['interface_id']);
			$smarty->assign('message', Message::getMessage());
		//	header('Location: ./router_status.php?router_id='.$router['router_id']);
		} else {
			$message[] = array("Es sind nicht genügen Informationen vorhanden, um die Keys zum Download bereit zu stellen.", 2);
			$message[] = array("Warscheinlich müssen haben sie die Keys noch nicht erstellt.", 2);
			Message::setMessage($message);
			header('Location: ./router_status.php?router_id='.$router['router_id']);
		}
	}

/*    if ($_GET['section'] == "edit") {
		$ip_data = Helper::getIpDataByIpId($_GET['ip_id']);
		UserManagement::isOwner($smarty, $ip_data['user_id']);

		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$ip_data = Helper::getIpDataByIpId($_GET['ip_id']);
		$smarty->assign('ip_data', $ip_data);
		$smarty->assign('ccd', Vpn::getCCD($_GET['ip_id']));

     	$smarty->display("header.tpl.php");
		$smarty->display("vpn_edit.tpl.php");
		$smarty->display("footer.tpl.php");
    }*/

    if ($_GET['section'] == "info") {
      $ip = Helper::getIpDataByIpId($_GET['ip_id']);
      UserManagement::isOwner($smarty, $ip['user_id']);
    	if (!empty($ip['vpn_server_ca']) AND !empty($ip['vpn_client_cert']) AND !empty($ip['vpn_client_key'])) {
		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$smarty->assign('vpn_config', $Vpn->getVpnConfig($_GET['ip_id']));
		$smarty->assign('certificate_data', $Vpn->getCertificateInfo($_GET['ip_id']));
		$smarty->assign('ccd', $Vpn->getCCD($_GET['ip_id']));
		$smarty->display("header.tpl.php");
		$smarty->display("sslcertificate_info.tpl.php");
		$smarty->display("footer.tpl.php");
	    } else {
	        $message[] = array("Es sind nicht genügen Informationen vorhanden um die Keys bereit zu stellen.", 2);
    	  	$message[] = array("Warscheinlich müssen haben sie die Keys noch nicht erstellt.", 2);
      		Message::setMessage($message);
		header('Location: ./ip.php?id='.$_GET['ip_id']);
    	}
    }

    if ($_GET['section'] == "regenerate_ccd_subnet") {
	//Only owner and Root can access this site.
	if (!UserManagement::checkPermission(32, $_SESSION['user_id']))
		UserManagement::denyAccess();
	  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
      $smarty->assign('subnets', Helper::getSubnetsByUserId($_SESSION['user_id']));
      $smarty->display("header.tpl.php");
      $smarty->display("regenerate_ccd_subnet.tpl.php");
      $smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "insert_regenerate_ccd_subnet") {
	$subnet = Helper::getSubnetById($_POST['subnet_id']);

	//Only owner and Root can access this site.
	if (!UserManagement::checkIfUserIsOwnerOrPermitted(64, $subnet['user_id']))
		UserManagement::denyAccess();

      Vpn::regenerateCCD($_POST['subnet_id']);
      header('Location: subnet.php?id='.$_POST['subnet_id']);
    }

    if ($_GET['section'] == "insert_regenerate_ccd") {
		$Vpn->writeCCD($_GET['ip_id']);

		header('Location: ip.php?id='.$_GET['ip_id']);
	}

    if ($_GET['section'] == "insert_delete_ccd") {
      $Vpn->deleteCCD($_GET['ip_id']);
      header('Location: ip.php?id='.$_GET['ip_id']);
    }
?>