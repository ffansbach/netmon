<?php
  require_once('./config/runtime.inc.php');
  
  $smarty->assign('message', message::getMessage());
  
  $smarty->display("header.tpl.php");
  $smarty->display("desktop.tpl.php");
  $smarty->display("footer.tpl.php");
?>