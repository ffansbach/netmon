<?php
  require_once('runtime.php');

  $smarty->assign('community_location_longitude', Config::getConfigValueByName('community_location_longitude'));
  $smarty->assign('community_location_latitude', Config::getConfigValueByName('community_location_latitude'));
  $smarty->assign('community_location_zoom', Config::getConfigValueByName('community_location_zoom'));
  $smarty->assign('google_maps_api_key', $GLOBALS['google_maps_api_key']);
  
  $smarty->display("header.tpl.html");
  $smarty->display("map.tpl.html");
  $smarty->display("footer.tpl.html");
?>