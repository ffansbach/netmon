<?php
  require_once('runtime.php');

  $smarty->assign('google_maps_api_key', $GLOBALS['google_maps_api_key']);
  
  $smarty->display("header.tpl.php");
  $smarty->display("map.tpl.php");
  $smarty->display("footer.tpl.php");
?>