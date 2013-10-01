<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterfacelist.class.php');
	require_once(ROOT_DIR.'/lib/core/OriginatorStatusList.class.php');
	require_once(ROOT_DIR.'/lib/core/Chipsetlist.class.php');
	require_once(ROOT_DIR.'/lib/core/ConfigLine.class.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	
	if(isset($_GET['router_id']) AND isset($_GET['embed']) AND $_GET['embed']) {
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
		
		echo "status crawl cycle id: ".$router->getStatusdata()->getCrawlCycleId()."<br>";
		echo "last endet: ".Crawling::getLastEndedCrawlCycle()['id'];
		
		$networkinterfacelist = new Networkinterfacelist(false, $router->getRouterId(),
														 0, -1, 'name', 'asc');
		$smarty->assign('networkinterfacelist', $networkinterfacelist);
		
		$originator_status_list = new OriginatorStatusList($router->getRouterId(), $router->getStatusdata()->getCrawlCycleId(), 0, -1);
		$smarty->assign('originator_status_list', $originator_status_list);

		
		$smarty->display("header.tpl.html");
		$smarty->display("router.tpl.html");
		$smarty->display("footer.tpl.html");
	} elseif(isset($_GET['section']) AND $_GET['section'] == 'add') {
		//Logged in users can add a new router
		if(Permission::checkPermission(PERM_USER)) {
			$smarty->assign('message', Message::getMessage());
			
			$smarty->assign('google_maps_api_key', ConfigLine::configByName('google_maps_api_key'));
			$smarty->assign('community_location_longitude', ConfigLine::configByName('community_location_longitude'));
			$smarty->assign('community_location_latitude', ConfigLine::configByName('community_location_latitude'));
			$smarty->assign('community_location_zoom', ConfigLine::configByName('community_location_zoom'));
			$smarty->assign('twitter_token', ConfigLine::configByName('twitter_token'));
			
			$chipsetlist = new Chipsetlist(false, false, false, 0, -1);
			$smarty->assign('chipsetlist', $chipsetlist->getList());
			
			$smarty->display("header.tpl.html");
			$smarty->display("router_new.tpl.html");
			$smarty->display("footer.tpl.html");
		} else {
			Permission::denyAccess(PERM_USER);
		}
	} elseif($_GET['section'] == "store") {
		
	
	}
?>