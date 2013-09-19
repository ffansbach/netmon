<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Routerlist.class.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	 $_GET['status'] = (isset($_GET['status'])) ? $_GET['status'] : false;
	 $_GET['hardware_name'] = (isset($_GET['hardware_name'])) ? $_GET['hardware_name'] : false;
	 $_GET['firmware_version'] = (isset($_GET['firmware_version'])) ? $_GET['firmware_version'] : false;
	 $_GET['batman_advanced_version'] = (isset($_GET['batman_advanced_version'])) ? $_GET['batman_advanced_version'] : false;
	 $_GET['kernel_version'] = (isset($_GET['kernel_version'])) ? $_GET['kernel_version'] : false;
	
	$routerlist = new Routerlist(false, false, $_GET['status'], $_GET['hardware_name'],
								 $_GET['firmware_version'], $_GET['batman_advanced_version'], $_GET['kernel_version'], 0, -1);
	$smarty->assign('routerlist', $routerlist->getRouterlist());
	
	$smarty->display("header.tpl.html");
	$smarty->display("routerlist.tpl.html");
	$smarty->display("footer.tpl.html");
?>