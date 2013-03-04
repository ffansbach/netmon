<?php
require_once('runtime.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/service.class.php');
require_once('lib/classes/core/dns.class.php');
require_once('lib/classes/core/user.class.php');
require_once('lib/classes/core/routersnotassigned.class.php');

//get messages of the message system
$smarty->assign('message', Message::getMessage());

//get some status variables
$smarty->assign('permitted', Permission::checkIfUserIsOwnerOrPermitted(64, $_GET['user_id']));
$smarty->assign('show_routersnotassigned_list', ($_GET['user_id']==$_SESSION['user_id'] AND empty($routerlist)));
$smarty->assign('is_logged_in', Permission::isLoggedIn($_SESSION['user_id']));

//get main page data
$smarty->assign('user', User::getUserByID($_GET['user_id']));
$smarty->assign('user_history', History::getUserHistory($_GET['user_id'], 5));
$smarty->assign('routersnotassigned_list', RoutersNotAssigned::getRouters());
$smarty->assign('routerlist', Router::getRouterListByUserId($_GET['user_id']));
$smarty->assign('servicelist', Service::getServiceList(Permission::isLoggedIn($_SESSION['user_id']) ? 'all' : 'public', $_GET['user_id']));

//get infotmation about the dns system
$smarty->assign('dns_tld', $GLOBALS['dns_tld']);
$smarty->assign('dns_hosts', DNS::getHostsByUser($_GET['user_id']));

//load the temlate
$smarty->display("header.tpl.php");
$smarty->display("user.tpl.php");
$smarty->display("footer.tpl.php");
?>