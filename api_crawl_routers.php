<?php
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/login.class.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/crawling.class.php');

if($_GET['section']=="insert_router") {
	$session = login::user_login($_GET['nickname'], $_GET['password']);

	$router_data = Router::getRouterInfo($_GET['router_id']);

	//If is owning user or if root
	if(UserManagement::isThisUserOwner($router_data['user_id'], $session['user_id']) OR $session['permission']==120) {
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

	} else {
		echo "you are not permitted";
	}
}

?>