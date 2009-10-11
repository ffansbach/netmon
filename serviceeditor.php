<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');
  require_once('./lib/classes/core/serviceeditor.class.php');
  
  $serviceeditor = new serviceeditor;

    if ($_GET['section'] == "new") {
		if (usermanagement::checkPermission(4)) {
			$smarty->assign('message', message::getMessage());
			$smarty->assign('ip_data', Helper::getIpInfo($_GET['ip_id']));	
			$smarty->display("header.tpl.php");
			$smarty->display("add_service.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
	}
    if ($_GET['section'] == "insert_service") {
		if (usermanagement::checkPermission(4)) {
			if ($_POST['ips'] > 0) {
				$ip_info = Helper::getIpInfo($_GET['ip_id']);
				$range = editingHelper::getFreeIpZone($ip_info['subnet_id'], $_POST['ips'], 0);
			} else {
				$range['start'] = "NULL";
				$range['end'] = "NULL";
			}
			$add_result = editingHelper::addIpTyp($_GET['ip_id'], $_POST['title'], $_POST['description'], $_POST['typ'], $_POST['crawler'], $_POST['port'], $range['start'], $range['end'], $_POST['radius'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait']);
			header('Location: ./service.php?service_id='.$add_result['service_id']);
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen einen Service anlegen!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "edit") {
	$service_data = Helper::getServiceDataByServiceId($_GET['service_id']);
    usermanagement::isOwner($smarty, $service_data['user_id']);
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('servicedata', $service_data);
	$smarty->assign('message', message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("service_edit.tpl.php");
	$smarty->display("footer.tpl.php");
    }
    if ($_GET['section'] == "insert_edit") {
	$edit_result = $serviceeditor->insertEditService($_GET['service_id'], $_POST['typ'], $_POST['crawler'], $_POST['title'], $_POST['description'], $_POST['radius'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait']);
	header('Location: ./service.php?service_id='.$edit_result['service_id']);
    }
    if ($_GET['section'] == "delete") {
		$ip = Helper::getIpIdByServiceId($_GET['service_id']);
		if ($serviceeditor->deleteService($_GET['service_id']))
			header('Location: ./ip.php?id='.$ip['id']);
		else
			header('Location: ./serviceeditor.php?section=edit&service_id='.$_GET['service_id']);
    }
?>