<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/iplist.class.php');
  
  $iplist = new iplist;
  
  $smarty->assign('iplist', $iplist->getIpList());
  $smarty->assign('vpnlist', $iplist->getVpnList());
  $smarty->assign('servicelist', $iplist->getServiceList());
  $smarty->assign('clientlist', $iplist->getClientList());
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);

  $smarty->display("header.tpl.php");
  $smarty->display("iplist.tpl.php");
  $smarty->display("footer.tpl.php");
?>