<?php
  require_once('runtime.php');
  require_once('lib/core/routersnotassigned.class.php');
  
  $routerlist = RoutersNotAssigned::getRouters();
  $smarty->assign('routerlist', $routerlist);

  $smarty->display("header.tpl.html");
  $smarty->display("routers_trying_to_assign.tpl.html");
  $smarty->display("footer.tpl.html");
?>