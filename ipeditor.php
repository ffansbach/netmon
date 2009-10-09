<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/ipeditor.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
  
  $ipeditor = new ipeditor;
  
    if ($_GET['section'] == "new") {
		if (usermanagement::checkPermission(4)) {
			$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
			$smarty->assign('existing_subnets', editingHelper::getExistingSubnets());
			$smarty->assign('message', message::getMessage());
			
			$smarty->display("header.tpl.php");
			$smarty->display("ip_new.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "insert") {
		if (usermanagement::checkPermission(4)) {
			$insert_result = $ipeditor->insertNewIp($_POST['subnet_id'], $_POST['ips']);
			if ($insert_result['result']) {
				header('Location: ./ip.php?id='.$insert_result['ip_id']);
			} else {
				header('Location: ./ipeditor.php?section=new');
			}
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen oder editieren!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
	}

    if ($_GET['section'] == "edit") {
		$ip_data = Helper::getIpDataByIpId($_GET['id']);
		usermanagement::isOwner($smarty, $ip_data['user_id']);

		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$ip_data = Helper::getIpDataByIpId($_GET['id']);
		$smarty->assign('ip_data', $ip_data);

		$smarty->display("header.tpl.php");
		$smarty->display("ip_edit.tpl.php");
		$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "delete") {
		$ip_data = Helper::getIpDataByIpId($_GET['id']);
		usermanagement::isOwner($smarty, $ip_data['user_id']);

		$ipeditor->deleteIp($_GET['id']);
		header('Location: ./user.php?id='.$_SESSION['user_id']);
    }
?>