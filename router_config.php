<?php
  require_once('runtime.php');
  require_once('lib/classes/core/interfaces.class.php');
  require_once('lib/classes/core/router.class.php');
  require_once('lib/classes/core/service.class.php');
  require_once('lib/classes/core/config.class.php');

  $smarty->assign('message', Message::getMessage());
  $router_data = Router_old::getRouterInfo($_GET['router_id']);
  $smarty->assign('router_data', $router_data);
  $interfaces = Interfaces::getInterfacesByRouterId($_GET['router_id']);
  
  $smarty->assign('interfaces', $interfaces);

  $is_logged_id = Permission::isLoggedIn($_SESSION['user_id']);
  if($is_logged_id) {
  	$view='all';
  } else {
  	$view='public';
  }
  $services = Service::getServiceList($view="", false, $_GET['router_id']);
  $smarty->assign('services', $services);

  $smarty->assign('google_maps_api_key', Config::getConfigValueByName("google_maps_api_key"));
  $smarty->assign('network_connection_ipv4', Config::getConfigValueByName("network_connection_ipv4"));
  $smarty->assign('network_connection_ipv6', Config::getConfigValueByName("network_connection_ipv6"));

  if((Router_old::areAddsAllowed($_GET['router_id']) AND isThisUserOwner($router_data['user_id'])) OR Permission::checkPermission(64)) {
    $smarty->assign('show_add_link', true);
  } else {
    $smarty->assign('show_add_link', false);
  }

  $smarty->display("header.tpl.php");
  $smarty->display("router_config.tpl.php");
  $smarty->display("footer.tpl.php");
?>