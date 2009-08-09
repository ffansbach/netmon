<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/nodelist.class.php');

	if (usermanagement::checkPermission(4)) {
		$nodelist = new nodelist;
		
		$smarty->assign('message', message::getMessage());
		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$smarty->assign('essid', $GLOBALS['project_essid']);
		$smarty->assign('bssid', $GLOBALS['project_bssid']);
		$smarty->assign('kanal', $GLOBALS['project_kanal']);
		$smarty->assign('project_name', $GLOBALS['project_name']);

		$smarty->assign('servicelist', $nodelist->getServiceListByUserId($_SESSION['user_id']));
		
		$smarty->display("header.tpl.php");
		$smarty->display("desktop.tpl.php");
		$smarty->display("footer.tpl.php");
	} else {
		$message[] = array("Nur eingeloggte Benutzer dürfen den Desktop sehen!", 2);
		message::setMessage($message);
		header('Location: ./login.php');
	}
?>