<?php
  /**
  * IF Netmon is called by the server/cronjob
  */
	if (!empty($_SERVER["REQUEST_URI"]))
		$path = "";
	else
		$path = dirname(__FILE__)."/";

	require_once($path.'config/runtime.inc.php');
	require_once($path.'lib/classes/core/service.class.php');
	require_once($path.'lib/classes/core/crawling.class.php');
	require_once($path.'lib/classes/core/router.class.php');

/*
 * Get all Services that have notification on
 */
/*try {
	$sql = "SELECT id as service_id
	        FROM  services
	        WHERE notify=1";
	$result = DB::getInstance()->query($sql);
	foreach($result as $row) {
		service::offlineNotification($row['service_id']);
	}
}
catch(PDOException $e) {
	echo $e->getMessage();
}

//Remove old generated images
$files = scandir($path."scripts/imgbuild/dest/");
foreach($files as $file) {
	if ($file!=".." AND $file!=".") {
		$exploded_name = explode("_", $file);
		if(!empty($exploded_name[1]) AND $exploded_name[1]<(time()-1800)) {
			exec("rm -Rf $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/dest/$file");
		}
	}
}*/

//Initialisiere neuen Crawl Cycle
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

if(strtotime($last_crawl_cycle['crawl_date'])+(($GLOBALS['crawl_cycle']-1)*60)<time()) {
	try {
		DB::getInstance()->exec("INSERT INTO crawl_cycle (crawl_date)
						      VALUES (NOW());");
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
}

//Schließe alten Crawl Cycle
$last_ended_crawl_cycle = Crawling::getLastEndedCrawlCycle();

$routers = Router::getRouters();
foreach ($routers as $router) {
	$crawl = Router::getCrawlRouterByCrawlCycleId($last_ended_crawl_cycle['id'], $router['id']);
	if(empty($crawl)) {
		try {
			DB::getInstance()->exec("INSERT INTO crawl_routers (router_id, crawl_cycle_id, crawl_date, status)
						 VALUES ('$router[id]', '$last_ended_crawl_cycle[id]', NOW(), 'offline');");
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
	}
}


?>