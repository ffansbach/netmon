<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/status.class.php');
  
  $status = new status;
  
  $smarty->assign('status', $status->getCrawlerStatus());
  $smarty->assign('newest_user', $status->getNewestUser());
  $smarty->assign('newest_node', $status->getNewestNode());
  $smarty->assign('newest_service', $status->getNewestService());

  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('zeit', date("H:i:s", time()));

  $smarty->display("header.tpl.php");
  $smarty->display("status.tpl.php");
  $smarty->display("footer.tpl.php");
?>