<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/subnetlist.class.php');
  
  $subnetlist = new subnetlist;
  
  $smarty->assign('subnetlist', $subnetlist->getList());
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);

  $smarty->display("header.tpl.php");
  $smarty->display("subnetlist.tpl.php");
  $smarty->display("footer.tpl.php");
?>