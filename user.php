<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  
  $smarty->assign('message', Message::getMessage());
  
  $smarty->assign('user', Helper::getUserByID($_GET['id']));
  $smarty->assign('iplist', Helper::getIplistByUserID($_GET['id']));
  $smarty->assign('subnetlist', Helper::getSubnetlistByUserID($_GET['id']));
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);

  $smarty->display("header.tpl.php");
  $smarty->display("user.tpl.php");
  $smarty->display("footer.tpl.php");
?>