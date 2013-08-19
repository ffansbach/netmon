<?php

/** Include Classes **/
require_once('runtime.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/interfaces.class.php');
require_once('./lib/classes/core/batmanadvanced.class.php');
require_once('./lib/classes/core/olsr.class.php');
require_once('./lib/classes/core/crawling.class.php');
require_once('./lib/classes/core/Eventlist.class.php');


if (!is_numeric($_GET['router_id'])) die('invalid router id');

if(!isset($_GET['embed'])) {
/** Get crawl cycles **/
if(!isset($_GET['crawl_cycle_id'])) {
	$last_ended_crawl_cycle = Crawling::getLastEndedCrawlCycle();
} else {
	$last_ended_crawl_cycle = Crawling::getCrawlCycleById($_GET['crawl_cycle_id']);
}

$actual_crawl_cycle = Crawling::getActualCrawlCycle();

//Before next navigation
$online_crawl_before = Router_old::getSmallerOnlineCrawlRouterByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$smarty->assign('online_crawl_before', $online_crawl_before);

$online_crawl_next = Router_old::getBiggerOnlineCrawlRouterByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
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

$router_reliability = Router_old::getRouterReliability($_GET['router_id'], 500);
$smarty->assign('router_reliability', $router_reliability);

/** Get Router Events **/
$eventlist = new Eventlist();
$eventlist->init('router', $_GET['router_id'], false, 0, 6, 'create_date', 'desc');
$smarty->assign('eventlist', $eventlist->getEventlist());

/** Get and assign Router Informations **/
//Router Status
$router_data = Router_old::getRouterInfo($_GET['router_id']);
$smarty->assign('router_data', $router_data);
$smarty->assign('crawl_cycle', $GLOBALS['crawl_cycle']);

//Batman advanced interfaces
$router_batman_adv_interfaces = BatmanAdvanced::getBatmanAdvancedInterfacesByRouterId($_GET['router_id']);
$smarty->assign('router_batman_adv_interfaces', $router_batman_adv_interfaces);
//Olsr interfaces
$router_olsr_interfaces = Olsr::getOlsrInterfacesByRouterId($_GET['router_id']);
$smarty->assign('router_olsr_interfaces', $router_olsr_interfaces);

/** Get status status information and history of router **/
$router_last_crawl = Router_old::getCrawlRouterByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$smarty->assign('router_last_crawl', $router_last_crawl);

$router_crawl_history = Router_old::getCrawlRouterHistoryExceptActualCrawlCycle($_GET['router_id'], $actual_crawl_cycle['id'], (60*24)/10);
$smarty->assign('router_crawl_history', $router_crawl_history);

/** Get and assign actual B.A.T.M.A.N advanced status **/
$crawl_batman_adv_interfaces = BatmanAdvanced::getCrawlBatmanAdvInterfacesByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$smarty->assign('crawl_batman_adv_interfaces', $crawl_batman_adv_interfaces);
$batman_adv_originators = BatmanAdvanced::getCrawlBatmanAdvOriginatorsByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$smarty->assign('batman_adv_originators', $batman_adv_originators);

/** Set settings for B.A.T.M.A.N advanced graphs **/
//Set RRD-Database and Image Path
$rrd_path_originators = __DIR__."/rrdtool/databases/router_$_GET[router_id]_originators.rrd";
if(file_exists($rrd_path_originators)) {
	$smarty->assign('rrd_originators_db_exists', true);
}

$rrd_link_quality_db_exists = false;
if(!empty($batman_adv_originators)) {
	foreach($batman_adv_originators as $originator) {
		//Set RRD-Database and Image Path
		$rrd_path_batman_adv_link_quality = __DIR__."/rrdtool/databases/router_".$_GET['router_id']."_batman_adv_link_quality_".$originator['originator_file_path'].".rrd";

		if(!$rrd_link_quality_db_exists AND file_exists($rrd_path_batman_adv_link_quality)) {
			$rrd_link_quality_db_exists = true;
			$smarty->assign('rrd_link_quality_db_exists', true);
		}
	}
}

/** Get and assign actual Olsrd status **/
/*$olsrd_crawl_data = Olsr::getCrawlOlsrDataByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
$olsrd_crawl_data['olsrd_links'] = unserialize($olsrd_crawl_data['olsrd_links']);
$smarty->assign('olsrd_crawl_data', $olsrd_crawl_data);*/

/** Get and assign Crawled interfaces **/
//Get actual crawled interfaces
$interface_crawl_data = Interfaces::getInterfacesCrawlByCrawlCycle($last_ended_crawl_cycle['id'], $_GET['router_id']);

//add traffic informations to 
foreach($interface_crawl_data as $key=>$interface) {
	//Get interface history
	$if_crawl_data_now = Interfaces::getInterfaceCrawlByCrawlCycleAndRouterIdAndInterfaceName($last_ended_crawl_cycle['id'], $_GET['router_id'], $interface['name']);
	$if_crawl_data_previous = Interfaces::getInterfaceCrawlByCrawlCycleAndRouterIdAndInterfaceName($last_ended_crawl_cycle['id']-1, $_GET['router_id'], $interface['name']);

	//add RECEIVED traffic informations
	$interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte'] = ($if_crawl_data_now['traffic_rx']-$if_crawl_data_previous['traffic_rx'])/$GLOBALS['crawl_cycle']/60;

	//Set negative values to 0
	if ($interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte']<0) {
		$interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte']=0;
	}
	$interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_kibibyte'] = round($interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte']/1024, 2);
	$interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_kilobyte'] = round($interface_crawl_data[$key]['traffic_info']['traffic_rx_per_second_byte']/1000, 2);

	//add TRANSMITTED traffic informations
	$interface_crawl_data[$key]['traffic_info']['traffic_tx_per_second_byte'] = ($if_crawl_data_now['traffic_tx']-$if_crawl_data_previous['traffic_tx'])/$GLOBALS['crawl_cycle']/60;
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
}

$smarty->assign('interface_crawl_data', $interface_crawl_data);

/** Get Clients */
$smarty->assign('clients_rrd_file_exists', file_exists("./rrdtool/databases/router_$_GET[router_id]_clients.rrd"));

/**Memory */
$smarty->assign('memory_rrd_file_exists', file_exists("./rrdtool/databases/router_$_GET[router_id]_memory.rrd"));
$smarty->assign('processes_rrd_file_exists', file_exists("./rrdtool/databases/router_$_GET[router_id]_processes.rrd"));

$smarty->assign('google_maps_api_key', Config::getConfigValueByName('google_maps_api_key'));

//Display Templates
$smarty->display("header.tpl.php");
$smarty->display("router_status.tpl.php");
$smarty->display("footer.tpl.php");
} else {
	$smarty->assign('community_essid', Config::getConfigValueByName('community_essid'));
	
	$last_ended_crawl_cycle = Crawling::getLastEndedCrawlCycle();
	$router_last_crawl = Router_old::getCrawlRouterByCrawlCycleId($last_ended_crawl_cycle['id'], $_GET['router_id']);
	$smarty->assign('router_status', $router_last_crawl);
	
	
	$router_data = Router_old::getRouterInfo($_GET['router_id']);
	$smarty->assign('router_data', $router_data);
	
	$smarty->display("router_status_embed.tpl.php");
}
?>
