<?php
  require_once('./config/runtime.inc.php');

  require_once('./lib/classes/api/map.class.php');
  require_once('./lib/classes/api/main.class.php');
  require_once('./lib/classes/core/login.class.php');

class api {
  function __construct() {
	if (isset($_GET['class']) AND isset($_GET['section']))
		eval($_GET['class']."::".$_GET['section']."();");
	elseif(isset($_POST['class']) AND isset($_POST['section']))
		eval($_POST['class']."::".$_POST['section']."();");
	else
		echo "data incomplete";
	die();
  }
}

new api;

?> 
