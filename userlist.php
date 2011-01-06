<?php

  require_once('runtime.php');
  require_once('./lib/classes/core/userlist.class.php');
  
  $userlist = new userlist;

  $smarty->assign('userlist', $userlist->getList());

  $smarty->display("header.tpl.php");
  $smarty->display("userlist.tpl.php");
  $smarty->display("footer.tpl.php");
?>