<?php
  require_once('runtime.php');
  require_once('./lib/api/map.class.php');

class Api {
	function __construct() {
		if (isset($_REQUEST['class']) AND isset($_REQUEST['section'])) {
			if(method_exists($_REQUEST['class'], $_REQUEST['section'])) {
				eval($_REQUEST['class']."::".$_REQUEST['section']."();");
			} else {
				echo "Class or section does not exist.";
			}
		} else {
			echo "Data incomplete.";
		}
		die();
	}
}

new Api;

?>