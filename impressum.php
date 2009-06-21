<?php
  require_once('./config/runtime.inc.php');

  $smarty->display("header.tpl.php");
  $smarty->display("impressum.tpl.php");
  $smarty->display("footer.tpl.php");
?>