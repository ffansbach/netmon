<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/subnet.class.php');

  $subnet = new subnet;
  
  $smarty->assign('message', message::getMessage());

      $smarty->assign('subnet', $subnet->getSubnet($_GET['id']));
      $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
      $smarty->assign('ipstatus', $subnet->getIPStatus($_GET['id']));

	$smarty->display("header.tpl.php");
	$smarty->display("subnet.tpl.php");
	$smarty->display("footer.tpl.php");

?>