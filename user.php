<?php
	require_once('runtime.php');
	require_once('lib/core/router.class.php');
	require_once('lib/core/User.class.php');
	require_once('lib/core/routersnotassigned.class.php');
	require_once('lib/core/Eventlist.class.php');
	require_once('lib/core/Service.class.php');
	require_once('lib/core/Servicelist.class.php');
	
	//get messages of the message system
	$smarty->assign('message', Message::getMessage());
	
	//get some status variables
	$smarty->assign('is_logged_in', Permission::isLoggedIn($_SESSION['user_id']));
	
	//get user data
	$user = new User((int)$_GET['user_id']);
	$user->fetch();
	$smarty->assign('user', $user);
	
	$evenlist = new Eventlist();
	$routerlist = new Routerlist(false, (int)$_GET['user_id']);
	foreach($routerlist->getRouterlist() as $router) {
		$tmp_eventlist = new Eventlist();
		$tmp_eventlist->init('router', $router->getRouterId(), false, 0, 6, 'event_id', 'desc');
		$evenlist->add($tmp_eventlist);
	}
	$evenlist->sort('create_date', 'desc');
	$smarty->assign('eventlist', array_slice($evenlist->getEventlist(), 0, 10));
	
	$smarty->assign('routersnotassigned_list', RoutersNotAssigned::getRouters());

	$routerlist = new Routerlist(false, (int)$_GET['user_id']);
	$smarty->assign('routerlist', $routerlist->getRouterlist());
	
	$servicelist = new Servicelist((int)$_GET['user_id']);
	$smarty->assign('servicelist', $servicelist->getServicelist());
	
	//load the temlate
	$smarty->display("header.tpl.html");
	$smarty->display("user.tpl.html");
	$smarty->display("footer.tpl.html");
?>