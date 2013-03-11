<?php

require_once('runtime.php');
require_once('lib/classes/core/login.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/routersnotassigned.class.php');
require_once('lib/classes/core/rrdtool.class.php');
require_once('lib/classes/core/interfaces.class.php');
require_once('lib/classes/core/ip.class.php');
require_once('lib/classes/core/crawling.class.php');
require_once('lib/classes/core/chipsets.class.php');
require_once('lib/classes/core/event.class.php');
require_once('lib/classes/extern/FwToolHash.class.php');

if($_GET['section']=="test_login_strings") {
	if(!empty($_GET['login_strings'])) {
		$login_strings = explode(";", $_GET['login_strings']);
		$exist=false;
		foreach($login_strings as $login_string) {
			if(!empty($login_string)) {
				$router_data = Router::getRouterByAutoAssignLoginString($login_string);
				if(!empty($router_data)) {
					$exist=true;
					echo "success;$login_string";
					break;
				}
			}
		}
		if(!$exist) {
			echo "error;login_string_not_found";
		}
	} else
		echo "error;no_login_strings_given";
}

if($_GET['section']=="router_auto_assign") {
	$router_data = Router::getRouterByAutoAssignLoginString($_GET['router_auto_assign_login_string']);
	if(empty($router_data)) {
		$router = RoutersNotAssigned::getRouterByAutoAssignLoginString($_GET['router_auto_assign_login_string']);
		if (empty($router)) {
			//Make DB Insert
			try {
				DB::getInstance()->exec("INSERT INTO routers_not_assigned (create_date, update_date, hostname, router_auto_assign_login_string, interface)
							 VALUES (NOW(), NOW(), '$_GET[hostname]', '$_GET[router_auto_assign_login_string]', '$_GET[interface]');");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			$not_assigned_id = DB::getInstance()->lastInsertId();
			
			//Make history
			$actual_crawl_cycle = Crawling::getActualCrawlCycle();
			$history_data = serialize(array('router_auto_assign_login_string'=>$_GET['router_auto_assign_login_string'], 'action'=>'new'));
			Event::addEvent('not_assigned_router', $not_assigned_id, $history_data);
			
			echo "error;new_not_assigned;;$_GET[router_auto_assign_login_string]";
		} else {
			try {
				$result = DB::getInstance()->exec("UPDATE routers_not_assigned SET
									  update_date = NOW()
								   WHERE id = '$router[id]'");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			echo "error;updated_not_assigned;;$_GET[router_auto_assign_login_string]";
		}
	} elseif ($router_data['allow_router_auto_assign']==0) {
		echo "error;autoassign_not_allowed;$_GET[router_auto_assign_login_string]";
	} elseif(!empty($router_data['router_auto_assign_hash'])) {
		if(!empty($_GET['router_auto_assign_login_string']) AND ($router_data['trying_to_assign_notified']!=1 OR strtotime($router_data['trying_to_assign_last_notification_time'])<=(time()-60*60*$GLOBALS['hours_to_prevent_from_sending_new_auto_assign_mail']))) {
			$user_data = User::getUserByID($router_data['user_id']);
			Router::NotifyAboutRouterTryingToAssign($router_data, $user_data);
			try {
				$result = DB::getInstance()->exec("UPDATE routers SET
									  trying_to_assign_notified = 1,
									  trying_to_assign_last_notification_time = NOW()
								   WHERE id = '$router_data[router_id]'");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
		echo "error;already_assigned;$_GET[router_auto_assign_login_string]";
	} else {
		//generate random string
		$hash_generator = new FW_Tool_Hash(32);
		$hash = $hash_generator->getHash();

		//Save hash to DB
		$result = DB::getInstance()->exec("UPDATE routers SET
							router_auto_assign_hash = '$hash'
						WHERE id = '$router_data[router_id]'");

		//Make output
		echo "success;".$router_data['router_id'].";".$hash.";".$router_data['hostname'];
	}
}

if($_GET['section']=="autoadd_ipv6_address") {
	$ip = Ip::getIpByIp($_GET['ip']);
	if(empty($ip)) {
		echo "success,address_does_not_exists,$_GET[ip]";
		Interfaces::addNewInterface($_GET['router_id'], 11, "configurator_ipv6", "", $_GET['ip']);
	} else {
		echo "error,address_exists,$ip[router_id]";
	}
}

if($_GET['section']=="get_hostname") {
	$router_data = Router::getRouterInfo($_GET['router_id']);
	echo "success,".$router_data['hostname'].",";
}

?>