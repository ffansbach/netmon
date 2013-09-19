<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterfacelist.class.php');
	require_once(ROOT_DIR.'/lib/core/OriginatorStatusList.class.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	
	if(isset($_GET['embed']) AND $_GET['embed']) {
		$smarty->assign('community_essid', Config::getConfigValueByName('community_essid'));
		$smarty->assign('google_maps_api_key', Config::getConfigValueByName('google_maps_api_key'));

		$router = new Router((int)$_GET['router_id']);
		$router->fetch();
		$smarty->assign('router', $router);
		
		$smarty->display("router_embed.tpl.html");
	} elseif(!isset($_GET['section']) AND isset($_GET['router_id'])) {
		$smarty->assign('message', Message::getMessage());
		$smarty->assign('google_maps_api_key', Config::getConfigValueByName('google_maps_api_key'));

		$router = new Router((int)$_GET['router_id']);
		$router->fetch();
		$smarty->assign('router', $router);
		
		$networkinterfacelist = new Networkinterfacelist(false, $router->getRouterId(),
														 0, -1, 'name', 'asc');
		$smarty->assign('networkinterfacelist', $networkinterfacelist);
		
		$originator_status_list = new OriginatorStatusList($router->getRouterId(), $router->getStatusdata()->getCrawlCycleId(), 0, -1);
		$smarty->assign('originator_status_list', $originator_status_list);

		
		$smarty->display("header.tpl.html");
		$smarty->display("router.tpl.html");
		$smarty->display("footer.tpl.html");
	}
?>