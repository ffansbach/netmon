<?php
	require_once('runtime.php');
	require_once('lib/core/interfaces.class.php');
	require_once('lib/core/router.class.php');
	require_once('lib/core/config.class.php');
	require_once('lib/core/Networkinterfacelist.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	$router_data = Router_old::getRouterInfo($_GET['router_id']);
	$smarty->assign('router_data', $router_data);
	
	$networkinterfacelist = new Networkinterfacelist($_GET['router_id'], 0, 50);
	$smarty->assign('networkinterfacelist', $networkinterfacelist->getNetworkinterfacelist());

  $smarty->assign('google_maps_api_key', Config::getConfigValueByName("google_maps_api_key"));
  $smarty->assign('network_connection_ipv4', Config::getConfigValueByName("network_connection_ipv4"));
  $smarty->assign('network_connection_ipv6', Config::getConfigValueByName("network_connection_ipv6"));

  if((Router_old::areAddsAllowed($_GET['router_id']) AND isThisUserOwner($router_data['user_id'])) OR Permission::checkPermission(64)) {
    $smarty->assign('show_add_link', true);
  } else {
    $smarty->assign('show_add_link', false);
  }

  $smarty->display("header.tpl.html");
  $smarty->display("router_config.tpl.html");
  $smarty->display("footer.tpl.html");
?>