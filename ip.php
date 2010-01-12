<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/ip.class.php');
  require_once('./lib/classes/core/service.class.php');

  $Ip = new Ip;

  $smarty->assign('message', Message::getMessage());
  
  $ip_data = Helper::getIpInfo($_GET['id']);
  $smarty->assign('ip', $ip_data);

	$services = Helper::getServicesByIpId($_GET['id']);
	foreach ($services as $index=>$service) {
		//Fetch current crawl data
		$current_crawl = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
		if(is_array($current_crawl['olsrd_neighbors'])) {
			foreach ($current_crawl['olsrd_neighbors'] as $key=>$olsrd_neighbors) {
				$tmp2 = 'IP address';
				$id = Helper::linkIp2IpId($olsrd_neighbors[$tmp2]);
				$current_crawl['olsrd_neighbors'][$key]['netmon_ip_id'] = $id['id'];

				$exploded_prefix = explode(".", $GLOBALS['net_prefix']);
				$exploded_neighbour = explode(".", $olsrd_neighbors[$tmp2]);
				$current_crawl['olsrd_neighbors'][$key]['netmon_is_client'] = false;
				
				if($exploded_prefix[0]==$exploded_neighbour[0] AND $exploded_prefix[1]==$exploded_neighbour[1]) {
					$neighbour = Helper::checkIfIpIsRegisteredAndGetServices("$exploded_neighbour[2].$exploded_neighbour[3]");
					foreach ($neighbour as $neight) {
						if($neight['typ']=='client') {
							$current_crawl['olsrd_neighbors'][$key]['netmon_is_client'] = true;
							break;
						}
					}
				}
			}
		}
		$services[$index]['current_crawl'] = $current_crawl;

		//Fetch last online crawl data
		$services[$index]['last_online_crawl'] = Helper::getLastOnlineCrawlDataByServiceId($service['service_id']);

		//Fetch crawl history
		$services[$index]['crawl_history'] = Service::getCrawlHistory($service['service_id'], 35);
	
		//Make array with available crawl types
		$service_types[$index] = $service['crawler'];
	}

	//Select the service_key from which the data is shown
	if(array_search('json', $service_types)) {
		$service_key = array_search('json', $service_types);
	} elseif(array_search('ping', $service_types)) {
		$service_key = array_search('json', $service_types);
	} else {
		$service_key = 0;
	}
	
	$smarty->assign('service_data', $services[$service_key]);
	$smarty->assign('services', $services);

//Graphics
foreach ($services[$service_key]['crawl_history'] as $hist) {
	$time = date("H:i", strtotime($hist['crawl_time']));
	$ping_array[$time] = $hist['ping'];
	$load_array[$time] = $hist['loadavg'];
	$memory_free_array[$time] = $hist['memory_free'];
	if(!empty($hist['loadavg']))
		$dont_show_indicator[] = $hist['loadavg'];
}

  $smarty->assign('dont_show_indicator', $dont_show_indicator);

if(!empty($ping_array)) {
	try {
		$graph = new ezcGraphLineChart();
		$graph->driver = new ezcGraphGdDriver(); 
		$graph->options->font = './templates/fonts/verdana.ttf';
		$graph->options->fillLines = 210;
		$graph->title = 'Ping';
		$graph->legend = false;
		
		$graph->xAxis = new ezcGraphChartElementDateAxis();
		
		// Add data
		$graph->data['Machine 1'] = new ezcGraphArrayDataSet( $ping_array );
		$graph->data['Machine 1']->symbol = ezcGraph::BULLET;
		$graph->render( 400, 150, './tmp/service_ping_history.png' );
	}
	catch(ezcGraphFontRenderingException $e) {
		$smarty->assign('ping_exception', $e->getMessage());
	}
}

if(!empty($load_array)) {
	try {
		$graph = new ezcGraphLineChart();
		$graph->driver = new ezcGraphGdDriver(); 
		$graph->options->font = './templates/fonts/verdana.ttf';
		$graph->options->fillLines = 210;
		$graph->title = 'Loadaverage';
		$graph->legend = false;
		$graph->xAxis = new ezcGraphChartElementDateAxis();
		
		// Add data
		$graph->data['Machine 1'] = new ezcGraphArrayDataSet( $load_array );
		$graph->data['Machine 1']->symbol = ezcGraph::BULLET;
		$graph->render( 400, 150, './tmp/loadaverage_history.png' ); 
	}
	catch(ezcGraphFontRenderingException $e) {
		$smarty->assign('loadaverage_exception', $e->getMessage());
	}
}

if(!empty($memory_free_array)) {
	try {
		$graph = new ezcGraphLineChart();
		$graph->driver = new ezcGraphGdDriver(); 
		$graph->options->font = './templates/fonts/verdana.ttf';
		$graph->options->fillLines = 210;
		$graph->title = 'Free Memory';
		$graph->legend = false;
		$graph->xAxis = new ezcGraphChartElementDateAxis();
		
		// Add data
		$graph->data['Machine 1'] = new ezcGraphArrayDataSet( $memory_free_array );
		$graph->data['Machine 1']->symbol = ezcGraph::BULLET;
		$graph->render( 400, 150, './tmp/memory_free_history.png' );
	}
	catch(ezcGraphFontRenderingException $e) {
		$smarty->assign('memory_free_exception', $e->getMessage());
	}
}
//-------------------------------------

  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('is_ip_owner', $Ip->is_ip_owner);
  $smarty->assign('subnet_data', Helper::getSubnetById($ip_data['subnet_id']));

  $smarty->assign('servicelist', $Ip->getServiceList($_GET['id']));
  $smarty->display("header.tpl.php");
  $smarty->display("ip.tpl.php");
  $smarty->display("footer.tpl.php");
?>