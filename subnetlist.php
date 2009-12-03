<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/subnetlist.class.php');
  
  $SubnetList = new SubnetList;
  
  $smarty->assign('subnetlist', $SubnetList->getList());
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);

  $smarty->display("header.tpl.php");
  $smarty->display("subnetlist.tpl.php");
  $smarty->display("footer.tpl.php");
?>