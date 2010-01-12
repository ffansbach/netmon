<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/ipeditor.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
  require_once('./lib/classes/core/vpn.class.php');
  
  $IpEditor = new IpEditor;
  
    if ($_GET['section'] == "new") {
		if (UserManagement::checkPermission(4)) {
			$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
			$smarty->assign('imggen_supported_chipsets', $GLOBALS['imggen_supported_chipsets']);
			$smarty->assign('existing_subnets', EditingHelper::getExistingSubnets());
			$smarty->assign('message', Message::getMessage());
			
			$smarty->display("header.tpl.php");
			$smarty->display("ip_new.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "insert") {
		if (UserManagement::checkPermission(4)) {
			$insert_result = $IpEditor->insertNewIp();

			$ip_data = Helper::getIpInfo($insert_result['ip_id']);
			$user_data = Helper::getUserByID($ip_data['user_id']);
			$subnet_data = Helper::getSubnetById($ip_data['subnet_id']);
			if ($insert_result['result']) {
				if(!empty($subnet_data['vpn_server_ca']) AND !empty($subnet_data['vpn_server_cert'])  AND !empty($subnet_data['vpn_server_key'])) {
					$Vpn = new Vpn;

					$keys = $Vpn->generateKeys($insert_result['ip_id'], $user_data['nickname'], $insert_result['ip_id'], $user_data['email'], '', '', 3650);
					if ($keys['return']) {
						$Vpn->saveKeysToDB($insert_result['ip_id'], $keys['vpn_client_cert'], $keys['vpn_client_key']);
						$Vpn->writeCCD($insert_result['ip_id']);
					}
				}

				header('Location: ./ip.php?id='.$insert_result['ip_id']);
			} else {
				header('Location: ./ipeditor.php?section=new');
			}
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen oder editieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}

    if ($_GET['section'] == "edit") {
		$ip_data = Helper::getIpDataByIpId($_GET['id']);
		UserManagement::isOwner($smarty, $ip_data['user_id']);

		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$smarty->assign('imggen_supported_chipsets', $GLOBALS['imggen_supported_chipsets']);
		$ip_data = Helper::getIpDataByIpId($_GET['id']);
		$smarty->assign('ip_data', $ip_data);
		$smarty->assign('ccd', Vpn::getCCD($_GET['id']));
		

		$smarty->display("header.tpl.php");
		$smarty->display("ip_edit.tpl.php");
		$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "insert_edit") {
		Ipeditor::insertEditIp($_GET['id'], $_POST['radius']);
		Vpn::writeCCD($_GET['id'], $_POST['ccd']);
		header('Location: ./ip.php?id='.$_GET['id']);
    }

    if ($_GET['section'] == "delete") {
		$ip_data = Helper::getIpDataByIpId($_GET['id']);
		UserManagement::isOwner($smarty, $ip_data['user_id']);

		$IpEditor->deleteIp($_GET['id']);
		header('Location: ./user.php?id='.$_SESSION['user_id']);
    }
?>