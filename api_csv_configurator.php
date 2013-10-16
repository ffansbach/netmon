<?php

require_once('runtime.php');
require_once('lib/core/login.class.php');
require_once('lib/core/router.class.php');
require_once('lib/core/routersnotassigned.class.php');
require_once('lib/core/rrdtool.class.php');
require_once('lib/core/Networkinterface.class.php');
require_once('lib/core/Ip.class.php');
require_once('lib/core/crawling.class.php');
require_once('lib/core/Event.class.php');
require_once('lib/extern/FwToolHash.class.php');

if($_GET['section']=="test_login_strings") {
	if(!empty($_GET['login_strings'])) {
		$login_strings = explode(";", $_GET['login_strings']);
		$exist=false;
		foreach($login_strings as $login_string) {
			if(!empty($login_string)) {
				$router_data = Router_old::getRouterByAutoAssignLoginString($login_string);
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
	$router_data = Router_old::getRouterByAutoAssignLoginString($_GET['router_auto_assign_login_string']);
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
			$event = new Event(false, (int)$actual_crawl_cycle['id'], 'not_assigned_router', (int)$not_assigned_id, 'new', array('router_auto_assign_login_string'=>$_GET['router_auto_assign_login_string']));
			$event->store();
			
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
		echo "error;already_assigned;$_GET[router_auto_assign_login_string]";
	} else {
		//generate random string
		$hash_generator = new FW_Tool_Hash(32);
		$hash = $hash_generator->getHash();

		//Save hash to DB
		$result = DB::getInstance()->exec("UPDATE routers SET
							router_auto_assign_hash = '$hash'
						WHERE id = '$router_data[router_id]'");

		try {
			$stmt = DB::getInstance()->prepare("SELECT api_key
												FROM routers, users
												WHERE routers.id=? AND users.id=routers.user_id");
			$stmt->execute(array($router_data['router_id']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}

		//Make output
		echo "success;".$router_data['router_id'].";".$hash.";".$router_data['hostname'].";".$row['api_key'];
	}
}

if($_GET['section']=="autoadd_ipv6_address") {
	//first try to determine network of given address
	$ipv6_network = Ip::ipv6NetworkFromAddr($_GET['ip'], (int)$_GET['netmask']);
	$network = new Network(false, false, $ipv6_network, (int)$_GET['netmask'], (int)$_GET['ipv']);
	if($network->fetch()) {
		//if network found, then try to add ip address.
		//first we need to check if the interface we want to add th ip to already exists
		//and if not we need to create it.
		$networkinterface = new Networkinterface(false, (int)$_GET['router_id'], $_GET['networkinterface_name']);
		if(!$networkinterface->fetch()) {
			$networkinterface_id = $networkinterface->store();
			if(!$networkinterface_id) {
				echo "error,new_interface_not_stored";
				die();
			}
		} else {
			$networkinterface_id = $networkinterface->getNetworkinterfaceId();
		}
		
		//then we can create the ip
		$ip = new Ip(false, (int)$networkinterface_id, (int)$network->getNetworkId(), $_GET['ip']);
		if($ip->store()) {
			echo "success,address_does_not_exists,".$ip->getIp();
		} else {
			echo "error,new_ip_not_stored";
			die();
		}
	} else {
		echo "error,network_not_found";
		die();
	}
}

if($_GET['section']=="get_hostname") {
	$router_data = Router_old::getRouterInfo($_GET['router_id']);
	echo "success,".$router_data['hostname'].",";
}

?>