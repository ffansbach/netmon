<?php
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/helper.class.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/service.class.php');
  
$smarty->assign('message', Message::getMessage());
  
if (UserManagement::checkIfUserIsOwnerOrPermitted(64, $_GET['id']))
	$smarty->assign('permitted', true);

$smarty->assign('user', Helper::getUserByID($_GET['id']));

$routerlist = Router::getRouterListByUserId($_GET['id']);
$smarty->assign('routerlist', $routerlist);

$servicelist = Service::getServiceListByUserId($_GET['id']);
$smarty->assign('servicelist', $servicelist);

$user_history = History::getUserHistory($_GET['id'], 5);
$smarty->assign('user_history', $user_history);

/*$smarty->assign('iplist', Helper::getIplistByUserID($_GET['id']));
$smarty->assign('subnetlist', Helper::getSubnetlistByUserID($_GET['id']));
$smarty->assign('net_prefix', $GLOBALS['net_prefix']);*/

$smarty->display("header.tpl.php");
$smarty->display("user.tpl.php");
$smarty->display("footer.tpl.php");
?>