<?php

/** Include Classes **/
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/crawling.class.php');

/** Get and assign global messages **/
$smarty->assign('message', Message::getMessage());

/**Get and assign routers By Chipset **/
$chipsets = Helper::getChipsets();
foreach ($chipsets as $key=>$chipset) {
	$router_chipsets[$key]['chipset_name'] = $chipset['name'];
	$router_chipsets[$key]['count'] = Router::countRoutersByChipsetId($chipset['id']);;
}

$smarty->assign('router_chipsets', $router_chipsets);

/**Set history time window**/
$history_start = time()-(60*60*48);
$history_end = time()-(10*60);

/** Get and assign routers status history**/
$crawl_cycle_history = Crawling::getCrawlCycleHistory($history_start, $history_end);
$history_count = count($crawl_cycle_history);

foreach($crawl_cycle_history as $key=>$crawl_cycle) {
	$router_status_history[$key]['crawl_date'] = $crawl_cycle['crawl_date'];
	$router_status_history[$key]['online'] = Router::countRoutersByCrawlCycleIdAndStatus($crawl_cycle['id'], 'online');
	$router_status_history[$key]['offline'] = Router::countRoutersByCrawlCycleIdAndStatus($crawl_cycle['id'], 'offline');
	$router_status_history[$key]['unknown'] = (Router::countRoutersByTime(strtotime($crawl_cycle['crawl_date'])))-($router_status_history[$key]['offline']+$router_status_history[$key]['online']);
	$router_status_history[$key]['all'] = $router_status_history[$key]['unknown']+$router_status_history[$key]['offline']+$router_status_history[$key]['online'];
}

//Set RRD-Database and Image Path
$rrd_path_status = __DIR__."/tmp/networkstatistic_status.rrd";
$image_path_status = __DIR__."/tmp/networkstatistic_status.png";

//Delete old RRD-Database and Image
@unlink($rrd_path_status);
@unlink($image_path_status);

//Create new RRD-Database
exec("rrdtool create $rrd_path_status --step 600 --start $history_start DS:online:GAUGE:900:U:U DS:offline:GAUGE:900:U:U DS:unknown:GAUGE:900:U:U DS:all:GAUGE:900:U:U RRA:AVERAGE:0:1:".($history_count+20));

for ($i=$history_count-1; $i>=0; $i--) {
	//Update Database
	$crawl_time = strtotime($router_status_history[$i]['crawl_date']);
	exec("rrdtool update $rrd_path_status $crawl_time:".$router_status_history[$i]['online'].":".$router_status_history[$i]['offline'].":".$router_status_history[$i]['unknown'].":".$router_status_history[$i]['all']);
}

//Create Image
exec("rrdtool graph $image_path_status -a PNG --title='Router status' --vertical-label 'routers' --units-exponent 0 --start $history_start --end $history_end DEF:probe1=$rrd_path_status:online:AVERAGE DEF:probe2=$rrd_path_status:offline:AVERAGE DEF:probe3=$rrd_path_status:unknown:AVERAGE DEF:probe4=$rrd_path_status:all:AVERAGE LINE1:probe1#007B0F:'online' LINE1:probe2#CB0000:'offline' LINE1:probe3#F8C901:'unknown' LINE1:probe4#696969:'all'");

$smarty->assign('router_status_history', $router_status_history);

/** Display templates **/
$smarty->display("header.tpl.php");
$smarty->display("networkstatistic.tpl.php");
$smarty->display("footer.tpl.php");

?>
