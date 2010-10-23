<?php
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/login.class.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/crawling.class.php');
require_once('./lib/classes/core/rrdtool.class.php');

if($_GET['section']=="insert_router") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);

	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root
	if((($_GET['authentificationmethod']=='user') AND (UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120)) OR (($_GET['authentificationmethod']=='hash') AND ($router_data['allow_router_auto_assign']==1 AND !empty($router_data['router_auto_assign_hash']) AND $router_data['router_auto_assign_hash']==$_GET['router_auto_update_hash']))) {
		$last_crawl_cycle = Crawling::getActualCrawlCycle();
		$crawl_router = Router::getCrawlRouterByCrawlCycleId($last_crawl_cycle['id'], $_GET['router_id']);
	
echo "sucess\n";
echo "router_id: ".$_GET['router_id'];
echo "get authentificationmethod: ".$_GET['authentificationmethod'];
echo "netmon router_auto_assign_hash: ".$router_data['router_auto_assign_hash'];
echo "get router_auto_update_hash: ".$_GET['router_auto_update_hash'];

		if(empty($crawl_router)) {
			$crawl_data['status'] = $_GET['status'];
			$crawl_data['ping'] = $_GET['ping'];
			$crawl_data['hostname'] = $_GET['hostname'];
			$crawl_data['description'] = rawurldecode($_GET['description']);
			$crawl_data['location'] = rawurldecode($_GET['location']);
			$crawl_data['latitude'] = $_GET['latitude'];
			$crawl_data['longitude'] = $_GET['longitude'];
			$crawl_data['luciname'] = $_GET['luciname'];
			$crawl_data['luciversion'] = $_GET['luciversion'];
			$crawl_data['distname'] = $_GET['distname'];
			$crawl_data['distversion'] = $_GET['distversion'];
			$crawl_data['chipset'] = $_GET['chipset'];
			$crawl_data['cpu'] = $_GET['cpu'];
			$crawl_data['memory_total'] = $_GET['memory_total'];
			$crawl_data['memory_caching'] = $_GET['memory_caching'];
			$crawl_data['memory_buffering'] = $_GET['memory_buffering'];
			$crawl_data['memory_free'] = $_GET['memory_free'];
			$crawl_data['loadavg'] = $_GET['loadavg'];
			$crawl_data['processes'] = $_GET['processes'];
			$crawl_data['uptime'] = $_GET['uptime'];
			$crawl_data['idletime'] = $_GET['idletime'];
			$crawl_data['local_time'] = $_GET['local_time'];
			$crawl_data['community_essid'] = $_GET['community_essid'];
			$crawl_data['community_nickname'] = $_GET['community_nickname'];
			$crawl_data['community_email'] = $_GET['community_email'];
			$crawl_data['community_prefix'] = $_GET['community_prefix'];
			
			Crawling::insertRouterCrawl($_GET['router_id'], $crawl_data);
			RrdTool::updateRouterMemoryHistory($_GET['router_id'], $_GET['memory_free'], $_GET['memory_caching'], $_GET['memory_buffering']);
		}
	} else {
		echo "you are not permitted";
echo "get authentificationmethod: ".$_GET['authentificationmethod'];
echo "netmon router_auto_assign_hash: ".$router_data['router_auto_assign_hash'];
echo "get router_auto_update_hash: ".$_GET['router_auto_update_hash'];
	}
}

?>