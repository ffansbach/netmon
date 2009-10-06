<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/ip.class.php');
  
  $ip = new ip;

  $smarty->assign('message', message::getMessage());
  
  $ip_data = Helper::getIpInfo($_GET['id']);
  $smarty->assign('ip', $ip_data);

  $online=exec("ping $GLOBALS[net_prefix].$ip_data[subnet_ip].$ip_data[ip_ip] -c 1 -w 1");
  if ($online)
    $ping = substr($online, -15, -9);
  else
	$ping =  false;

  $smarty->assign('ping', $ping);

  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('is_ip_owner', $ip->is_ip_owner);
  $smarty->assign('servicelist', $ip->getServiceList($_GET['id']));
  $smarty->display("header.tpl.php");
  $smarty->display("ip.tpl.php");
  $smarty->display("footer.tpl.php");
?>