<?php
  require_once('runtime.php');
  require_once('lib/core/routersnotassigned.class.php');
  
  $routerlist = RoutersNotAssigned::getRouters();
  $smarty->assign('routerlist', $routerlist);

  $smarty->display("header.tpl.php");
  $smarty->display("routers_trying_to_assign.tpl.php");
  $smarty->display("footer.tpl.php");
?>