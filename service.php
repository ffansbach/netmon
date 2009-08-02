<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/service.class.php');
  
  $service = new service;

  $smarty->assign('message', message::getMessage());
  
  $smarty->assign('service_data', Helper::getServiceDataByServiceId($_GET['service_id']));
  $smarty->assign('current_crawl', Helper::getCurrentCrawlDataByServiceId($_GET['service_id']));
  $smarty->assign('last_online_crawl', Helper::getLastOnlineCrawlDataByServiceId($_GET['service_id']));
  $smarty->assign('crawl_history', $service->getCrawlHistory($_GET['service_id'], 15));
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('is_node_owner', $service->is_node_owner);

  $smarty->display("header.tpl.php");
  $smarty->display("service.tpl.php");
  $smarty->display("footer.tpl.php");
?>