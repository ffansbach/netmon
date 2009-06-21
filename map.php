<?php
  require_once('./config/runtime.inc.php');
  
  $smarty->display("header.tpl.php");
  $smarty->display("map.tpl.php");
  $smarty->display("footer.tpl.php");
?>