<?php

/** Include Classes **/
require_once('runtime.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/crawling.class.php');

/** Get and assign global messages **/
$smarty->assign('message', Message::getMessage());

/** Get and assign crawler status **/
$last_ended_crawl_cycle = Crawling::getLastEndedCrawlCycle();

if(!empty($last_ended_crawl_cycle)) {
	$last_ended_crawl_cycle['crawl_date_end'] = strtotime($last_ended_crawl_cycle['crawl_date'])+$GLOBALS['crawl_cycle']*60;
	
	$actual_crawl_cycle = Crawling::getActualCrawlCycle();
	$actual_crawl_cycle['crawl_date_end'] = strtotime($actual_crawl_cycle['crawl_date'])+$GLOBALS['crawl_cycle']*60;
	$actual_crawl_cycle['crawl_date_end_minutes'] = floor(($actual_crawl_cycle['crawl_date_end']-time())/60).':'.(($actual_crawl_cycle['crawl_date_end']-time()) % 60);
	
	$smarty->assign('last_ended_crawl_cycle', $last_ended_crawl_cycle);
	$smarty->assign('actual_crawl_cycle', $actual_crawl_cycle);
	
	/**Get and assign routers By Chipset **/
	$chipsets = Helper::getChipsets();
	foreach ($chipsets as $key=>$chipset) {
		$router_chipsets[$key]['chipset_name'] = $chipset['name'];
		$router_chipsets[$key]['count'] = Router::countRoutersByChipsetId($chipset['id']);;
	}
	
	$smarty->assign('router_chipsets', $router_chipsets);
	
	/**Set history time window**/
	$history_start_1_day = time()-(60*60*24*1);
	$history_start_7_days = time()-(60*60*24*7);
	$history_start_1_month = time()-(60*60*24*30);
	
	$history_end = time()-(10*60);
	
	//Set RRD-Database and Image Path
	$rrd_path_status = __DIR__."/rrdtool/databases/netmon_history_router_status.rrd";
	
	$image_path_status_1_day = __DIR__."/tmp/netmon_history_router_status_1_day.png";
	$image_path_status_7_days = __DIR__."/tmp/netmon_history_router_status_7_days.png";
	$image_path_status_1_month = __DIR__."/tmp/netmon_history_router_status_1_month.png";
	
	//Create Image
	exec("rrdtool graph $image_path_status_1_day -a PNG --title='Router status' --vertical-label 'routers' --units-exponent 0 --start $history_start_1_day --end $history_end DEF:probe1=$rrd_path_status:online:AVERAGE DEF:probe2=$rrd_path_status:offline:AVERAGE DEF:probe3=$rrd_path_status:unknown:AVERAGE DEF:probe4=$rrd_path_status:total:AVERAGE LINE1:probe1#007B0F:'online' LINE1:probe2#CB0000:'offline' LINE1:probe3#F8C901:'unknown' LINE1:probe4#696969:'total'");
	exec("rrdtool graph $image_path_status_7_days -a PNG --title='Router status' --vertical-label 'routers' --units-exponent 0 --start $history_start_7_days --end $history_end DEF:probe1=$rrd_path_status:online:AVERAGE DEF:probe2=$rrd_path_status:offline:AVERAGE DEF:probe3=$rrd_path_status:unknown:AVERAGE DEF:probe4=$rrd_path_status:total:AVERAGE LINE1:probe1#007B0F:'online' LINE1:probe2#CB0000:'offline' LINE1:probe3#F8C901:'unknown' LINE1:probe4#696969:'total'");
	exec("rrdtool graph $image_path_status_1_month -a PNG --title='Router status' --vertical-label 'routers' --units-exponent 0 --start $history_start_1_month --end $history_end DEF:probe1=$rrd_path_status:online:AVERAGE DEF:probe2=$rrd_path_status:offline:AVERAGE DEF:probe3=$rrd_path_status:unknown:AVERAGE DEF:probe4=$rrd_path_status:total:AVERAGE LINE1:probe1#007B0F:'online' LINE1:probe2#CB0000:'offline' LINE1:probe3#F8C901:'unknown' LINE1:probe4#696969:'total'");
	
	$online = Router::countRoutersByCrawlCycleIdAndStatus($last_ended_crawl_cycle['id'], 'online');
	$offline = Router::countRoutersByCrawlCycleIdAndStatus($last_ended_crawl_cycle['id'], 'offline');
	$unknown = (Router::countRoutersByTime(strtotime($last_ended_crawl_cycle['crawl_date'])))-($offline+$online);
	$total = $unknown+$offline+$online;
	
	$smarty->assign('router_status_online', $online);
	$smarty->assign('router_status_offline', $offline);
	$smarty->assign('router_status_unknown', $unknown);
	$smarty->assign('router_status_total', $total);

	//Clients
	//Set RRD-Database and Image Path
	$rrd_path_status = __DIR__."/rrdtool/databases/netmon_hisory_client_count.rrd";
	
	$image_path_status_1_day = __DIR__."/tmp/netmon_history_client_count_1_day.png";
	$image_path_status_7_days = __DIR__."/tmp/netmon_history_client_count_7_days.png";
	$image_path_status_1_month = __DIR__."/tmp/netmon_history_client_count_1_month.png";
	
	//Create Image
	exec("rrdtool graph $image_path_status_1_day -a PNG --title='Verbundene Clients' --vertical-label 'clients' --units-exponent 0 --start $history_start_1_day --end $history_end DEF:probe1=$rrd_path_status:clients:AVERAGE LINE1:probe1#007B0F:'clients'");
	exec("rrdtool graph $image_path_status_7_days -a PNG --title='Verbundene Clients' --vertical-label 'clients' --units-exponent 0 --start $history_start_7_days --end $history_end DEF:probe1=$rrd_path_status:clients:AVERAGE LINE1:probe1#007B0F:'clients'");
	exec("rrdtool graph $image_path_status_1_month -a PNG --title='Verbundene Clients' --vertical-label 'clients' --units-exponent 0 --start $history_start_1_month --end $history_end DEF:probe1=$rrd_path_status:clients:AVERAGE LINE1:probe1#007B0F:'clients'");
}
/** Display templates **/
$smarty->display("header.tpl.php");
$smarty->display("networkstatistic.tpl.php");
$smarty->display("footer.tpl.php");

?>
