<?php
	require_once('runtime.php');
	
	if(isset($_GET['embed']) AND $_GET['embed']) {
		if(isset($_GET['key']))
			$smarty->assign('key', $_GET['key']);
		else
			$smarty->assign('key', false);
				
		if(!isset($_GET['longitude'])
		   OR !isset($_GET['latitude'])
		   OR !isset($_GET['zoom'])) {
			$smarty->assign('community_location_longitude', Config::getConfigValueByName('community_location_longitude'));
			$smarty->assign('community_location_latitude', Config::getConfigValueByName('community_location_latitude'));
			$smarty->assign('community_location_zoom', Config::getConfigValueByName('community_location_zoom'));
		} else {
			$smarty->assign('community_location_longitude', $_GET['longitude']);
			$smarty->assign('community_location_latitude', $_GET['latitude']);
			$smarty->assign('community_location_zoom', $_GET['zoom']);
		}
		
		$smarty->assign('google_maps_api_key', $GLOBALS['google_maps_api_key']);
		
		$smarty->display("map_embed.tpl.html");
	} else {
		$smarty->assign('community_location_longitude', Config::getConfigValueByName('community_location_longitude'));
		$smarty->assign('community_location_latitude', Config::getConfigValueByName('community_location_latitude'));
		$smarty->assign('community_location_zoom', Config::getConfigValueByName('community_location_zoom'));
		$smarty->assign('google_maps_api_key', $GLOBALS['google_maps_api_key']);
		
		$smarty->display("header.tpl.html");
		$smarty->display("map.tpl.html");
		$smarty->display("footer.tpl.html");
	}
?>