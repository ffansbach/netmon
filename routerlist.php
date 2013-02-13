<?php
	require_once('runtime.php');
	require_once('./lib/classes/core/router.class.php');
	require_once('./lib/classes/core/ip.class.php');

	if(!isset($_GET['where']))
	  $_GET['where'] = "";
	if(!isset($_GET['operator']))
	  $_GET['operator'] = "";
	if(!isset($_GET['value']))
	  $_GET['value'] = "";

	$routerlist = Router::getRouterList($_GET['where'], $_GET['operator'], $_GET['value']);
	$smarty->assign('routerlist', $routerlist);

	$smarty->display("header.tpl.php");
	$smarty->display("routerlist.tpl.php");
	$smarty->display("footer.tpl.php");
?>