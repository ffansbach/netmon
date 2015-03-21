<?php
  require_once('runtime.php');
  require_once('./lib/api/map.class.php');

class Api {
  function __construct() {
	if (isset($_GET['class']) AND class_exists($_GET['class'])
            AND isset($_GET['section']) AND method_exists($_GET['class'], $_GET['section']))
		eval($_GET['class']."::".$_GET['section']."();");
	elseif(isset($_POST['class']) AND class_exists($_POST['class'])
            AND isset($_POST['section']) AND method_exists($_POST['class'], $_POST['section']))
		eval($_POST['class']."::".$_POST['section']."();");
	else
		echo "data incomplete";
	die();
  }
}

new api;

?> 
