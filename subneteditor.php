<?php

  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/subneteditor.class.php');
  require_once('./lib/classes/core/editinghelper.class.php');

  $subneteditor = new subneteditor;

    if ($_GET['section'] == "new") {
		if (usermanagement::checkPermission(32)) {
			$smarty->assign('message', message::getMessage());
			$smarty->assign('avalailable_subnets', editingHelper::getFreeSubnets());
			$smarty->assign('subnets_with_defined_vpnserver', $subneteditor->getSubnetsWithDefinedVpnserver());
			$smarty->display("header.tpl.php");
			$smarty->display("subnet_new.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze anlegen!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "insert") {
		if (usermanagement::checkPermission(32)) {
			$checkdata = $subneteditor->checkSubnetData();
			if ($checkdata) {
				$subnet_result = $subneteditor->createNewSubnet($checkdata);
				header('Location: ./subnet.php?id='.$subnet_result['subnet_id']);
			} else {
				header('Location: ./subneteditor.php?section=new');
			}
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze anlegen!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "edit") {
		if (usermanagement::checkPermission(32)) {
			$smarty->assign('message', message::getMessage());
			$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
			$smarty->assign('subnets_with_defined_vpnserver', $subneteditor->getSubnetsWithDefinedVpnserver());
			$subnetdata = Helper::getSubnetDataBySubnetID($_GET['id']);
			$smarty->assign('subnet_data', $subnetdata);
			$smarty->assign('avalailable_subnets', editingHelper::getFreeSubnetsPlusPredefinedSubnet($subnetdata['subnet_ip']));
			$smarty->display("header.tpl.php");
			$smarty->display("subnet_edit.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze editieren!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
    if ($_GET['section'] == "update") {
		if (usermanagement::checkPermission(32)) {
			if ($subneteditor->checkSubnetData($_POST['subnet'], $_POST['vpnserver'], $_POST['vpnserver_from_project'], $_POST['vpnserver_from_project_check'], $_POST['no_vpnserver_check'], $_POST['vpn_cacrt'])) {
				$subneteditor->updateSubnet($_POST['subnet'],  $_POST['vpnserver'], $_POST['vpnserver_from_project'], $_POST['vpnserver_from_project_check'], $_POST['no_vpnserver_check'], $_POST['title'], $_POST['description'], $_POST['longitude'], $_POST['latitude'], $_POST['radius'], $POST['vpn_cacrt']);
				header('Location: ./subnet.php?id='.$_GET['id']);
			} else {
				header('Location: ./subneteditor.php?section=edit&id='.$_GET['id']);
			}
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze editieren!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
	}
    if ($_GET['section'] == "delete") {
		if (usermanagement::checkPermission(32)) {
			if ($_POST['delete'] == "true") {
				$subneteditor->deleteSubnet($_GET['subnet_id']);
				header('Location: ./user.php?id='.$_SESSION['user_id']);
			} else {
				$message[] = array("Sie müssen \"Ja\" anklicken um das Subnets zu löschen.", 2);
				message::setMessage($message);
				header('Location: ./subneteditor.php?section=edit&id='.$_GET['id']);
			}
		} else {
			$message[] = array("Nur Administratoren dürfen Subnetze editieren!", 2);
			message::setMessage($message);
			header('Location: ./login.php');
		}
    }
?>