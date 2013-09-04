<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	
	if(!isset($_GET['section']) AND isset($_GET['router_id'])) {
		$smarty->assign('message', Message::getMessage());
		$smarty->assign('google_maps_api_key', Config::getConfigValueByName('google_maps_api_key'));

		$router = new Router((int)$_GET['router_id']);
		$router->fetch();
		$smarty->assign('router', $router);
		
		$smarty->display("header.tpl.html");
		$smarty->display("router.tpl.html");
		$smarty->display("footer.tpl.html");
	} else {
	
	}
?>