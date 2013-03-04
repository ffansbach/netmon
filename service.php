<?php
  require_once('runtime.php');
  require_once('./lib/classes/core/service.class.php');

  $Service = new service;

  $smarty->assign('message', Message::getMessage());

  $service_data = Helper::getServiceDataByServiceId($_GET['service_id']);
  $service_data['create_date'] = Helper::makeSmoothIplistTime(strtotime($service_data['create_date']));

  $smarty->assign('service_data', $service_data);
  $current_crawl = Helper::getCurrentCrawlDataByServiceId($_GET['service_id']);
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
  $smarty->assign('current_crawl', $current_crawl);




  $smarty->assign('last_online_crawl', Helper::getLastOnlineCrawlDataByServiceId($_GET['service_id']));
  $crawl_history = $Service->getCrawlHistory($_GET['service_id'], 35);
  $smarty->assign('crawl_history', $crawl_history);
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('isOwner', Permission::isThisUserOwner($service_data['user_id']));

foreach ($crawl_history as $hist) {
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


  $smarty->display("header.tpl.php");
  $smarty->display("service.tpl.php");
  $smarty->display("footer.tpl.php");
?>