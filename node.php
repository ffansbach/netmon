<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/node.class.php');
  
  $node = new node;

  $smarty->assign('message', message::getMessage());
  
  $node_data = Helper::getNodeInfo($_GET['id']);
  $smarty->assign('node', $node_data);

  $online=exec("ping $GLOBALS[net_prefix].$node_data[subnet_ip].$node_data[node_ip] -c 1 -w 1");
  if ($online)
    $ping = substr($online, -15, -9);
  else
	$ping =  false;

  $smarty->assign('ping', $ping);

  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('is_node_owner', $node->is_node_owner);
  $smarty->assign('servicelist', $node->getServiceList($_GET['id']));
  $smarty->display("header.tpl.php");
  $smarty->display("node.tpl.php");
  $smarty->display("footer.tpl.php");
?>