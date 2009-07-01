<?php
  require_once('./config/runtime.inc.php');

  require_once('./lib/classes/api/map.class.php');

class api {
  function __construct() {
    eval($_GET['class']."::".$_GET['section']."();");
    die();
  }
}

new api;

?> 
