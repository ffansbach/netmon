<?php
  require_once('runtime.php');
  require_once('./lib/classes/core/interfaces.class.php');
  require_once('./lib/classes/core/router.class.php');
  require_once('./lib/classes/core/service.class.php');

  $smarty->assign('message', Message::getMessage());
  $router_data = Router::getRouterInfo($_GET['router_id']);
  $smarty->assign('router_data', $router_data);
  $interfaces = Interfaces::getInterfacesByRouterId($_GET['router_id']);

  $smarty->assign('interfaces', $interfaces);

  $is_logged_id = Usermanagement::isLoggedIn($_SESSION['user_id']);
  if($is_logged_id) {
  	$view='all';
  } else {
  	$view='public';
  }
  $services = Service::getServiceList($view="", false, $_GET['router_id']);
  $smarty->assign('services', $services);

  $smarty->assign('google_maps_api_key', $GLOBALS['google_maps_api_key']);
  $smarty->assign('netmon_is_connected_to_network_by_ipv6', $GLOBALS['netmon_is_connected_to_network_by_ipv6']);
  $smarty->assign('netmon_is_connected_to_network_by_ipv4', $GLOBALS['netmon_is_connected_to_network_by_ipv4']);

  if((Router::areAddsAllowed($_GET['router_id']) AND isThisUserOwner($router_data['user_id'])) OR UserManagement::checkPermission(64)) {
    $smarty->assign('show_add_link', true);
  } else {
    $smarty->assign('show_add_link', false);
  }

  $smarty->display("header.tpl.php");
  $smarty->display("router_config.tpl.php");
  $smarty->display("footer.tpl.php");
?>