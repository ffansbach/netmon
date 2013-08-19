<?php
require_once('runtime.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/user.class.php');
require_once('lib/classes/core/routersnotassigned.class.php');
require_once('lib/classes/core/Eventlist.class.php');
require_once('lib/classes/core/Service.class.php');
require_once('lib/classes/core/Servicelist.class.php');

//get messages of the message system
$smarty->assign('message', Message::getMessage());

//get some status variables
$smarty->assign('permitted', Permission::checkIfUserIsOwnerOrPermitted(64, $_GET['user_id']));
$smarty->assign('is_logged_in', Permission::isLoggedIn($_SESSION['user_id']));

//get main page data
$smarty->assign('user', User_old::getUserByID($_GET['user_id']));

try {
	$stmt = DB::getInstance()->prepare("SELECT  * FROM routers WHERE user_id=?");
	$stmt->execute(array($_GET['user_id']));
	$routers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
	echo $e->getMessage();
	echo $e->getTraceAsString();
}

$evenlist = new Eventlist();
foreach($routers as $router) {
	$tmp_eventlist = new Eventlist();
	$tmp_eventlist->init('router', (int)$router['id'], false, 0, 6, 'create_date', 'desc');
	$evenlist->add($tmp_eventlist);
}
$evenlist->sort('create_date', 'desc');
$smarty->assign('eventlist', $evenlist->getEventlist());

$smarty->assign('routersnotassigned_list', RoutersNotAssigned::getRouters());
$smarty->assign('routerlist', Router_old::getRouterListByUserId($_GET['user_id']));

$servicelist = new Servicelist((int)$_GET['user_id']);
$smarty->assign('servicelist', $servicelist->getServicelist());

//load the temlate
$smarty->display("header.tpl.php");
$smarty->display("user.tpl.php");
$smarty->display("footer.tpl.php");
?>