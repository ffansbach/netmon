<?php

/** Include Classes **/
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/interfaces.class.php');
require_once('./lib/classes/core/batmanadvanced.class.php');
require_once('./lib/classes/core/olsr.class.php');
require_once('./lib/classes/core/crawling.class.php');
require_once('./lib/classes/core/history.class.php');

/** Get crawl cycles **/
if(!isset($_GET['crawl_cycle_id'])) {
	$last_ended_crawl_cycle = Crawling::getLastEndedCrawlCycle();
} else {
	$last_ended_crawl_cycle = Crawling::getCrawlCycleById($_GET['crawl_cycle_id']);
}

$actual_crawl_cycle = Crawling::getActualCrawlCycle();

//Before next navigation
$online_crawl_before = Router::getSmallerOnlineCrawlRouterByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$smarty->assign('online_crawl_before', $online_crawl_before);

$online_crawl_next = Router::getBiggerOnlineCrawlRouterByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
if ($online_crawl_next['crawl_cycle_id']!=$actual_crawl_cycle['id']) {
	$smarty->assign('online_crawl_next', $online_crawl_next);
}

$next_smaller_crawl_cycle = Crawling::getNextSmallerCrawlCycleById($last_ended_crawl_cycle['id']);
$smarty->assign('next_smaller_crawl_cycle', $next_smaller_crawl_cycle);

$next_bigger_crawl_cycle = Crawling::getNextBiggerCrawlCycleById($last_ended_crawl_cycle['id']);
if ($next_bigger_crawl_cycle['id']!=$actual_crawl_cycle['id']) {
	$smarty->assign('next_bigger_crawl_cycle', $next_bigger_crawl_cycle);
}

/**Set history time window**/
$history_start_12_hours = time()-(60*60*12);
$history_start_1_day = time()-(60*60*24);
$history_start_1_week = time()-(60*60*24*7);
$history_end = strtotime($last_ended_crawl_cycle['crawl_date']);

/** Get and assign global messages **/
$smarty->assign('message', Message::getMessage());

$router_reliability = Router::getRouterReliability($_GET['router_id'], 500);
$smarty->assign('router_reliability', $router_reliability);

/** Get Router History **/
$router_history = History::getRouterHistoryByRouterIdExceptActualCrawlCycle($_GET['router_id'], $actual_crawl_cycle['id'], 6, false);
$smarty->assign('router_history', $router_history);

/** Get and assign Router Informations **/
//Router Status
$router_data = Router::getRouterInfo($_GET['router_id']);
$smarty->assign('router_data', $router_data);
$smarty->assign('crawl_cycle', $GLOBALS['crawl_cycle']);

//Batman advanced interfaces
$router_batman_adv_interfaces = BatmanAdvanced::getBatmanAdvancedInterfacesByRouterId($_GET['router_id']);
$smarty->assign('router_batman_adv_interfaces', $router_batman_adv_interfaces);
//Olsr interfaces
$router_olsr_interfaces = Olsr::getOlsrInterfacesByRouterId($_GET['router_id']);
$smarty->assign('router_olsr_interfaces', $router_olsr_interfaces);

/** Get status status information and history of router **/
$router_last_crawl = Router::getCrawlRouterByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$smarty->assign('router_last_crawl', $router_last_crawl);

$router_crawl_history = Router::getCrawlRouterHistoryExceptActualCrawlCycle($_GET['router_id'], $actual_crawl_cycle['id'], (60*24)/10);
$smarty->assign('router_crawl_history', $router_crawl_history);

//-----------------
	//Set RRD-Database and Image Path
	$rrd_path_memory = __DIR__."/rrdtool/databases/router_$_GET[router_id]_memory.rrd";
	$image_path_memory_12_hours = __DIR__."/tmp/router_$_GET[router_id]_memory_12_hours.png";
	$image_path_memory_1_day = __DIR__."/tmp/router_$_GET[router_id]_memory_1_day.png";
	$image_path_memory_1_week = __DIR__."/tmp/router_$_GET[router_id]_memory_1_week.png";
	//Delete old Image
	@unlink($image_path_memory);

	//Create Image
	exec("rrdtool graph $image_path_memory_12_hours -a PNG --width 270 --title='Memory usage' --vertical-label 'Bytes' --units-exponent 0 --start $history_start_12_hours --end $history_end DEF:probe2=$rrd_path_memory:memory_caching:AVERAGE DEF:probe1=$rrd_path_memory:memory_free:AVERAGE DEF:probe3=$rrd_path_memory:memory_buffering:AVERAGE AREA:probe2#ff0000:'Memory caching' AREA:probe1#0400ff:'Memory free' AREA:probe3#72c2c3:'Memory buffering'");
	exec("rrdtool graph $image_path_memory_1_day -a PNG --width 270 --title='Memory usage' --vertical-label 'Bytes' --units-exponent 0 --start $history_start_1_day --end $history_end DEF:probe2=$rrd_path_memory:memory_caching:AVERAGE DEF:probe1=$rrd_path_memory:memory_free:AVERAGE DEF:probe3=$rrd_path_memory:memory_buffering:AVERAGE AREA:probe2#ff0000:'Memory caching' AREA:probe1#0400ff:'Memory free' AREA:probe3#72c2c3:'Memory buffering'");
	exec("rrdtool graph $image_path_memory_1_week -a PNG --width 270 --title='Memory usage' --vertical-label 'Bytes' --units-exponent 0 --start $history_start_1_week --end $history_end DEF:probe2=$rrd_path_memory:memory_caching:AVERAGE DEF:probe1=$rrd_path_memory:memory_free:AVERAGE DEF:probe3=$rrd_path_memory:memory_buffering:AVERAGE AREA:probe2#ff0000:'Memory caching' AREA:probe1#0400ff:'Memory free' AREA:probe3#72c2c3:'Memory buffering'");
//-----------------------------------


/** Get and assign actual B.A.T.M.A.N advanced status **/
$crawl_batman_adv_interfaces = BatmanAdvanced::getCrawlBatmanAdvInterfacesByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$smarty->assign('crawl_batman_adv_interfaces', $crawl_batman_adv_interfaces);
$batman_adv_originators = BatmanAdvanced::getCrawlBatmanAdvOriginatorsByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$batman_adv_originators['originators'] = unserialize($batman_adv_originators['originators']);
$smarty->assign('batman_adv_originators', $batman_adv_originators);


/** Make B.A.T.M.A.N advanced history graph **/
$batman_adv_history = BatmanAdvanced::getCrawlBatmanAdvancedHistoryExceptActualCrawlCycle($_GET['router_id'], $actual_crawl_cycle['id'], (60*24)/10);

//Set RRD-Database and Image Path
$rrd_path_originators = __DIR__."/rrdtool/databases/router_$_GET[router_id]_originators.rrd";
$image_path_originators_12_hours = __DIR__."/tmp/router_$_GET[router_id]_originators_12_hours.png";
$image_path_originators_1_day = __DIR__."/tmp/router_$_GET[router_id]_originators_1_day.png";
$image_path_originators_1_week = __DIR__."/tmp/router_$_GET[router_id]_originators_1_week.png";

//Delete old Image
@unlink($image_path_originators);

//Create Image
exec("rrdtool graph $image_path_originators_12_hours -a PNG --width 270 --title='B.A.T.M.A.N advanced Originators' --vertical-label 'Originators' --units-exponent 0 --start $history_start_12_hours --end $history_end DEF:probe2=$rrd_path_originators:originators:AVERAGE LINE2:probe2#72c2c3:'Originators'");
exec("rrdtool graph $image_path_originators_1_day -a PNG --width 270 --title='B.A.T.M.A.N advanced Originators' --vertical-label 'Originators' --units-exponent 0 --start $history_start_1_day --end $history_end DEF:probe2=$rrd_path_originators:originators:AVERAGE LINE2:probe2#72c2c3:'Originators'");
exec("rrdtool graph $image_path_originators_1_week -a PNG --width 270 --title='B.A.T.M.A.N advanced Originators' --vertical-label 'Originators' --units-exponent 0 --start $history_start_1_week --end $history_end DEF:probe2=$rrd_path_originators:originators:AVERAGE LINE2:probe2#72c2c3:'Originators'");

/** Get and assign actual Olsrd status **/
$olsrd_crawl_data = Olsr::getCrawlOlsrDataByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$olsrd_crawl_data['olsrd_links'] = unserialize($olsrd_crawl_data['olsrd_links']);
$smarty->assign('olsrd_crawl_data', $olsrd_crawl_data);

/** Make Olsr history graph **/
$olsr_history = Olsr::getCrawlOlsrHistoryExceptActualCrawlCycle($_GET['router_id'], $actual_crawl_cycle['id'], (60*24)/10);

//Set RRD-Database and Image Path
$rrd_path_olsrd_links = __DIR__."/tmp/router_$_GET[router_id]_olsrd_links.rrd";
$image_path_olsrd_links = __DIR__."/tmp/router_$_GET[router_id]_olsrd_links.png";

//Delete old RRD-Database and Image
@unlink($rrd_path_olsrd_links);
@unlink($image_path_olsrd_links);

$history_count = count($olsr_history);

//Create new RRD-Database
exec("rrdtool create $rrd_path_olsrd_links --step 600 --start $history_start_1_day DS:olsrd_links:GAUGE:900:U:U RRA:AVERAGE:0:1:".($history_count-1));

for ($i=$history_count-1; $i>=0; $i--) {
	$olsrd_links = unserialize($olsr_history[$i]['olsrd_links']);
	$olsrd_links = count($olsrd_links);

	//Update Database
	$crawl_time = strtotime($olsr_history[$i]['crawl_date']);
	exec("rrdtool update $rrd_path_olsrd_links $crawl_time:".$olsrd_links);
}

//Create Image
exec("rrdtool graph $image_path_olsrd_links -a PNG --title='Olsr Links' --units-exponent 0 --start $history_start_1_day --end $history_end DEF:probe2=$rrd_path_olsrd_links:olsrd_links:AVERAGE LINE2:probe2#72c2c3:'Links'");


/** Get and assign Crawled interfaces **/
//Get actual crawled interfaces
$interface_crawl_data = Helper::getInterfacesCrawlByCrawlCycle($last_ended_crawl_cycle['id'], $_GET['router_id']);

//add traffic informations to 
foreach($interface_crawl_data as $key=>$interface) {
	//Get interface history
	$interface_crawl_data[$key]['crawl_history'] = Helper::getCrawlInterfaceHistoryByRouterIdAndInterfaceNameExceptActualCrawlCycle($_GET['router_id'], $actual_crawl_cycle['id'], $interface['name'], (60*24*1)/10);

	//add RECEIVED traffic informations
	$interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte'] = ($interface_crawl_data[$key]['crawl_history'][0]['traffic_rx']-$interface_crawl_data[$key]['crawl_history'][1]['traffic_rx'])/$GLOBALS['crawl_cycle']/60;
	//Set negative values to 0
	if ($interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte']<0) {
		$interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte']=0;
	}
	$interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_kibibyte'] = round($interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte']/1024, 2);
	$interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_kilobyte'] = round($interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte']/1000, 2);

	//add TRANSMITTED traffic informations
	$interface_crawl_data[$key]['traffic_info']['traffic_tx_per_second_byte'] = ($interface_crawl_data[$key]['crawl_history'][0]['traffic_tx']-$interface_crawl_data[$key]['crawl_history'][1]['traffic_tx'])/$GLOBALS['crawl_cycle']/60;
	//Set negative values to 0
	if ($interface_crawl_data[$key]['traffic_info']['traffic_tx_per_second_byte']<0) {
		$interface_crawl_data[$key]['traffic_info']['traffic_tx_per_second_byte']=0;
	}
	$interface_crawl_data[$key]['traffic_info']['traffic_tx_per_second_kibibyte'] = round($interface_crawl_data[$key]['traffic_info']['traffic_tx_per_second_byte']/1024, 2);
	$interface_crawl_data[$key]['traffic_info']['traffic_tx_per_second_kilobyte'] = round($interface_crawl_data[$key]['traffic_info']['traffic_tx_per_second_byte']/1000, 2);

	//Add some information about interface type
	if (stristr($interface['name'], 'tap') === FALSE AND stristr($interface['name'], 'tun') === FALSE) {
		$interface_crawl_data[$key]['is_vpn'] = false;
	} else {
		$interface_crawl_data[$key]['is_vpn'] = true;
	}

	
	//Set RRD-Database and Image Path
	$rrd_path_traffic_rx = __DIR__."/rrdtool/databases/router_$_GET[router_id]_interface_$interface[name]_traffic_rx.rrd";
	$image_path_traffic_rx_12_hours = __DIR__."/tmp/router_$_GET[router_id]_interface_$interface[name]_traffic_rx_12_hours.png";
	$image_path_traffic_rx_1_day = __DIR__."/tmp/router_$_GET[router_id]_interface_$interface[name]_traffic_rx_1_day.png";
	$image_path_traffic_rx_1_week = __DIR__."/tmp/router_$_GET[router_id]_interface_$interface[name]_traffic_rx_1_week.png";

	//Delete old Image
	@unlink($image_path_traffic_rx);

	//Create Image
	exec("rrdtool graph $image_path_traffic_rx_12_hours -a PNG --width 270 --title='Traffic $interface[name]' --vertical-label 'Kilobytes/s' --units-exponent 0 --start $history_start_12_hours --end $history_end DEF:probe1=$rrd_path_traffic_rx:traffic_rx:AVERAGE DEF:probe2=$rrd_path_traffic_rx:traffic_tx:AVERAGE LINE2:probe1#0400ff:'Traffic received' LINE2:probe2#ff0000:'Traffic transmitted'");
	exec("rrdtool graph $image_path_traffic_rx_1_day -a PNG --width 270 --title='Traffic $interface[name]' --vertical-label 'Kilobytes/s' --units-exponent 0 --start $history_start_1_day --end $history_end DEF:probe1=$rrd_path_traffic_rx:traffic_rx:AVERAGE DEF:probe2=$rrd_path_traffic_rx:traffic_tx:AVERAGE LINE2:probe1#0400ff:'Traffic received' LINE2:probe2#ff0000:'Traffic transmitted'");
	exec("rrdtool graph $image_path_traffic_rx_1_week -a PNG --width 270 --title='Traffic $interface[name]' --vertical-label 'Kilobytes/s' --units-exponent 0 --start $history_start_1_week --end $history_end DEF:probe1=$rrd_path_traffic_rx:traffic_rx:AVERAGE DEF:probe2=$rrd_path_traffic_rx:traffic_tx:AVERAGE LINE2:probe1#0400ff:'Traffic received' LINE2:probe2#ff0000:'Traffic transmitted'");
}

$smarty->assign('interface_crawl_data', $interface_crawl_data);

//Display Templates
$smarty->display("header.tpl.php");
$smarty->display("router_status.tpl.php");
$smarty->display("footer.tpl.php");

?>