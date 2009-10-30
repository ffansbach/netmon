<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/subnet.class.php');
  require_once('./lib/classes/core/subnetcalculator.class.php');

  $subnet = new subnet;
  
  $smarty->assign('message', message::getMessage());
  $subnet_data = $subnet->getSubnet($_GET['id']);
  $subnet_data['create_date'] = Helper::makeSmoothIplistTime(strtotime($subnet_data['create_date']));
  $smarty->assign('subnet', $subnet_data);
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('isOwner', usermanagement::isThisUserOwner($subnet_data['user_id']));

  $smarty->assign('ipstatus', $subnet->getIPStatus($_GET['id']));
  
  $smarty->assign('iplist', subnet::getPosibleIpsBySubnetId($_GET['id']));

  $smarty->display("header.tpl.php");
  $smarty->display("subnet.tpl.php");
  $smarty->display("footer.tpl.php");
?>