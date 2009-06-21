<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/nodelist.class.php');
  
  $nodelist = new nodelist;
  
  $smarty->assign('nodelist', $nodelist->getNodeList());
  $smarty->assign('vpnlist', $nodelist->getVpnList());
  $smarty->assign('servicelist', $nodelist->getServiceList());
  $smarty->assign('clientlist', $nodelist->getClientList());
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);

  $smarty->display("header.tpl.php");
  $smarty->display("nodelist.tpl.php");
  $smarty->display("footer.tpl.php");
?>