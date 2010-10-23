<?php

require_once('./config/runtime.inc.php');
require_once('lib/classes/core/router.class.php');


if($_GET['section']=="update") {
	header("Content-Type: text/plain");
	header("Content-Disposition: attachment; filename=nodewatcher_script");

	echo file_get_contents('./scripts/nodewatcher/nodewatcher_script');
}

if($_GET['section']=="version") {
	echo 5;
}

if($_GET['section']=="router_auto_assign") {
	$router_data = Router::getRouterByAutoAssignLoginString($_GET['router_auto_assign_login_string']);

//	$router_data = Router::getRouterByHostname($_GET['hostname']);
	if(empty($router_data)) {
		echo "error: router $_GET[router_auto_assign_login_string] does not exist";
	} elseif ($router_data['allow_router_auto_assign']==0) {
		echo "error: router $_GET[router_auto_assign_login_string] does not allow autoassign";
	} elseif(!empty($router_data['router_auto_assign_hash'])) {
		echo "error: router $_GET[router_auto_assign_login_string] cannot be assignet a second time";
	} else {

		//generate random string
		$hash = md5(
                          uniqid(
                                  (string)microtime(true)
                                  +sha1(
                                        (string)rand(0,10000)  //100% Zufall
                                        +$thumb_tmp_name
                                        )
                                )
                          +md5($orig_name)
                          );

		//Save hash to DB
		$result = DB::getInstance()->exec("UPDATE routers SET
							router_auto_assign_hash = '$hash'
						WHERE id = '$router_data[router_id]'");

		//Make output
		echo $router_data['router_id'].";".$hash.";".$router_data['hostname'];
	}
}


?>