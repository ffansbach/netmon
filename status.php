<?php
  require_once('runtime.php');
  require_once('./lib/classes/core/status.class.php');
  
  $Status = new Status;
  
//Deprecated
//  $smarty->assign('status', $Status->getCrawlerStatus());
  $smarty->assign('newest_user', $Status->getNewestUser());
  $smarty->assign('newest_ip', $Status->getNewestIp());
  $smarty->assign('newest_service', $Status->getNewestService());

  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);

  $smarty->display("header.tpl.php");
  $smarty->display("status.tpl.php");
  $smarty->display("footer.tpl.php");
?>