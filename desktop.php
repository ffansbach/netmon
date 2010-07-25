<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/iplist.class.php');
  require_once('./lib/classes/core/history.class.php');

	if (UserManagement::checkPermission(4)) {
		$smarty->assign('message', Message::getMessage());

		$routerlist = Router::getRouterListByUserId($_SESSION['user_id']);
		$smarty->assign('routerlist', $routerlist);

		
		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$smarty->assign('session_user_id', $_SESSION['user_id']);


/*		$smarty->assign('servicelist', $iplist->getServiceListByUserId($_SESSION['user_id']));
		$smarty->assign('subnetlist', Helper::getSubnetsByUserId($_SESSION['user_id']));

		$smarty->assign('history', History::getServiceHistoryByUser(false, 24, $_SESSION['user_id']));
*/
		$smarty->display("header.tpl.php");
		$smarty->display("desktop.tpl.php");
		$smarty->display("footer.tpl.php");
	} else {
		$message[] = array("Nur eingeloggte Benutzer dürfen den Desktop sehen!", 2);
		Message::setMessage($message);
		header('Location: ./login.php');
	}
?>