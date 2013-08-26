<?php
  require_once('runtime.php');

  require_once('./lib/api/map.class.php');
  require_once('./lib/api/main.class.php');
  require_once('./lib/api/crawl.class.php');
  require_once('./lib/core/login.class.php');

class Api {
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
