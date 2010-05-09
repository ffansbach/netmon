<?php
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/login.class.php');
require_once('./lib/classes/core/router.class.php');

if($_GET['section']=="insert_router") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);

	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root
	if(UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120) {
		try {
			$sql = "SELECT *
			        FROM  crawl_cycle
	        		ORDER BY crawl_date desc
				LIMIT 1;";
			$result = DB::getInstance()->query($sql);
			$last_crawl_cycle = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
	
		try {
			$sql = "SELECT *
	        		FROM  crawl_routers
				WHERE router_id='$_GET[router_id]' AND crawl_cycle_id=$last_crawl_cycle[id]";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$crawl_router[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
	
		if(empty($crawl_router)) {
			$description = rawurldecode($_GET['description']);
			$location = rawurldecode($_GET['location']);
			
			try {
				DB::getInstance()->exec("INSERT INTO crawl_routers (router_id, crawl_cycle_id, crawl_date, status, ping, hostname, description, location, latitude, longitude, luciname, luciversion, distname, distversion, chipset, cpu, memory_total, memory_caching, memory_buffering, memory_free, loadavg, processes, uptime, idletime, local_time, community_essid, community_nickname, community_email, community_prefix)
							 VALUES ('$_GET[router_id]', '$last_crawl_cycle[id]', NOW(), '$_GET[status]', '$_GET[ping]', '$_GET[hostname]', '$description', '$location', '$_GET[latitude]', '$_GET[longitude]', '$_GET[luciname]', '$_GET[luciversion]', '$_GET[distname]', '$_GET[distversion]', '$_GET[chipset]', '$_GET[cpu]', '$_GET[memory_total]', '$_GET[memory_caching]', '$_GET[memory_buffering]', '$_GET[memory_free]', '$_GET[loadavg]', '$_GET[processes]', '$_GET[uptime]', '$_GET[idletime]', '$_GET[local_time]', '$_GET[community_essid]', '$_GET[community_nickname]', '$_GET[community_email]', '$_GET[community_prefix]');");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
	} else {
		echo "you are not permitted";
	}
}

?>