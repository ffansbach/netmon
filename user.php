<?php
require_once('runtime.php');
require_once('lib/classes/core/helper.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/service.class.php');
require_once('lib/classes/core/dns.class.php');
require_once('lib/classes/core/user.class.php');
  
$smarty->assign('message', Message::getMessage());

if (!is_numeric($_GET['user_id'])) die('invalid user id');
  
if (UserManagement::checkIfUserIsOwnerOrPermitted(64, $_GET['user_id']))
	$smarty->assign('permitted', true);

$smarty->assign('user', User::getUserByID($_GET['user_id']));

$routerlist = Router::getRouterListByUserId($_GET['user_id']);
$smarty->assign('routerlist', $routerlist);



if($_GET['user_id']==$_SESSION['user_id'] AND empty($routerlist)) {
  $smarty->assign('show_routersnotassigned_list', true);
  require_once('lib/classes/core/routersnotassigned.class.php');
  
  $routersnotassigned_list = RoutersNotAssigned::getRouters();
  $smarty->assign('routersnotassigned_list', $routersnotassigned_list);
} else
  $smarty->assign('show_routersnotassigned_list', false);

$is_logged_id = Usermanagement::isLoggedIn($_SESSION['user_id']);
if($is_logged_id) {
	$view='all';
} else {
	$view='public';
}
$smarty->assign('is_logged_id', $is_logged_id);


$servicelist = Service::getServiceList($view="", $_GET['user_id']);
$smarty->assign('servicelist', $servicelist);

$user_history = History::getUserHistory($_GET['user_id'], 5);

$smarty->assign('user_history', $user_history);

$smarty->assign('dns_tld', $GLOBALS['dns_tld']);
$smarty->assign('dns_hosts', DNS::getHostsByUser($_GET['user_id']));

/*$smarty->assign('iplist', Helper::getIplistByUserID($_GET['id']));
$smarty->assign('subnetlist', Helper::getSubnetlistByUserID($_GET['id']));
$smarty->assign('net_prefix', $GLOBALS['net_prefix']);*/

$smarty->display("header.tpl.php");
$smarty->display("user.tpl.php");
$smarty->display("footer.tpl.php");
?>
