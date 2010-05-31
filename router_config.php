<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/interfaces.class.php');
  require_once('./lib/classes/core/router.class.php');
  require_once('./lib/classes/core/service.class.php');

  $smarty->assign('message', Message::getMessage());
  $router_data = Router::getRouterInfo($_GET['router_id']);
  $smarty->assign('router_data', $router_data);
  $interfaces = Interfaces::getInterfacesByRouterId($_GET['router_id']);
  $smarty->assign('interfaces', $interfaces);
  $services = Service::getServicesByRouterId($_GET['router_id']);
  $smarty->assign('services', $services);

  $smarty->display("header.tpl.php");
  $smarty->display("router_config.tpl.php");
  $smarty->display("footer.tpl.php");
?>