<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/iplist.class.php');
  require_once('./lib/classes/core/history.class.php');
  
	if (UserManagement::checkPermission(4)) {
		$iplist = new iplist;
		
		$smarty->assign('message', Message::getMessage());
		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$smarty->assign('essid', $GLOBALS['project_essid']);
		$smarty->assign('bssid', $GLOBALS['project_bssid']);
		$smarty->assign('kanal', $GLOBALS['project_kanal']);
		$smarty->assign('project_name', $GLOBALS['project_name']);
		$smarty->assign('session_user_id', $_SESSION['user_id']);


		$smarty->assign('servicelist', $iplist->getServiceListByUserId($_SESSION['user_id']));
		$smarty->assign('history', History::getServiceHistoryByUser(false, 24, $_SESSION['user_id']));

		$smarty->display("header.tpl.php");
		$smarty->display("desktop.tpl.php");
		$smarty->display("footer.tpl.php");
	} else {
		$message[] = array("Nur eingeloggte Benutzer dürfen den Desktop sehen!", 2);
		Message::setMessage($message);
		header('Location: ./login.php');
	}
?>