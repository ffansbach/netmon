<?php
  require_once('runtime.php');
  require_once('./lib/classes/core/iplist.class.php');
  
  $IpList = new IpList;
  
  $smarty->assign('iplist', $IpList->getIpList());
  $smarty->assign('vpnlist', $IpList->getVpnList());
  $smarty->assign('servicelist', $IpList->getServiceList());
  $smarty->assign('clientlist', $IpList->getClientList());
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);

  $smarty->display("header.tpl.php");
  $smarty->display("iplist.tpl.php");
  $smarty->display("footer.tpl.php");
?>