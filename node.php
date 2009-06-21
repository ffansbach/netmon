<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/node.class.php');
  
  $node = new node;

  $smarty->assign('message', message::getMessage());
  
  $smarty->assign('node', $node->getNodeInfo($_GET['id']));
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('is_node_owner', $node->is_node_owner);
  $smarty->assign('servicelist', $node->getServiceList($_GET['id']));
  $smarty->display("header.tpl.php");
  $smarty->display("node.tpl.php");
  $smarty->display("footer.tpl.php");
?>