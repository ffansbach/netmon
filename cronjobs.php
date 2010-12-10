<?php
/**
* IF Netmon is called by the server/cronjob
*/
if (empty($_SERVER["REQUEST_URI"])) {
	$path = dirname(__FILE__)."/";
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	$GLOBALS['netmon_root_path'] = $path."/";
}

require_once('config/runtime.inc.php');
require_once('lib/classes/core/service.class.php');
require_once('lib/classes/core/crawling.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/rrdtool.class.php');

/**
* Crawl cycles and offline crawls
**/

$actual_crawl_cycle = Crawling::getActualCrawlCycle();

if(strtotime($actual_crawl_cycle['crawl_date'])+(($GLOBALS['crawl_cycle']-1)*60)<time()) {
	//End actual crawl
	$routers = Router::getRouters();
	foreach ($routers as $router) {
		$crawl = Router::getCrawlRouterByCrawlCycleId($actual_crawl_cycle['id'], $router['id']);
		if(empty($crawl)) {
			$crawl_data['status'] = "offline";
			Crawling::insertRouterCrawl($router['id'], $crawl_data);
		}
	}

	$online = Router::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'online');
	$offline = Router::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'offline');
	$unknown = (Router::countRoutersByTime(strtotime($actual_crawl_cycle['crawl_date'])))-($offline+$online);
	$total = $unknown+$offline+$online;
	RrdTool::updateNetmonHistoryRouterStatus($online, $offline, $unknown, $total);
	//Initialise new crawl cycle
	Crawling::newCrawlCycle();
}

/**
* Service Crawls
**/

require_once($GLOBALS['netmon_root_path'].'lib/classes/crawler/json_service_crawler.php');

/**
* Clean database
**/

Crawling::deleteOldCrawlData($GLOBALS['days_to_keep_mysql_crawl_data']);

/**
* Remove old generated images
**/

$files = scandir($GLOBALS['netmon_root_path'].'scripts/imagemaker/tmp/');
foreach($files as $file) {
	if ($file!=".." AND $file!=".") {
		$exploded_name = explode("_", $file);
		if(!empty($exploded_name[2]) AND is_numeric($exploded_name[2]) AND $exploded_name[2]<(time()-1800)) {
			exec("rm -Rf $GLOBALS[netmon_root_path]/scripts/imgbuild/dest/$file");
		}
	}
}

?>