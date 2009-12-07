<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/ipeditor.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
  require_once('./lib/classes/core/vpn.class.php');
  
  $IpEditor = new IpEditor;
  
    if ($_GET['section'] == "new") {
		if (UserManagement::checkPermission(4)) {
			$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
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
			$insert_result = $IpEditor->insertNewIp($_POST['subnet_id'], $_POST['ips'], $_POST['ip_kind'], $_POST['ip'], $_POST['dhcp_kind'], $_POST['dhcp_first'], $_POST['dhcp_last']);
			if ($insert_result['result']) {
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
		$ip_data = Helper::getIpDataByIpId($_GET['id']);
		$smarty->assign('ip_data', $ip_data);
		$smarty->assign('ccd', Vpn::getCCD($_GET['id']));
		

		$smarty->display("header.tpl.php");
		$smarty->display("ip_edit.tpl.php");
		$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "insert_edit") {
		Ipeditor::insertEditIp($_GET['id'], $_POST['radius']);
		header('Location: ./ip.php?id='.$_GET['id']);
    }

    if ($_GET['section'] == "delete") {
		$ip_data = Helper::getIpDataByIpId($_GET['id']);
		UserManagement::isOwner($smarty, $ip_data['user_id']);

		$IpEditor->deleteIp($_GET['id']);
		header('Location: ./user.php?id='.$_SESSION['user_id']);
    }
?>