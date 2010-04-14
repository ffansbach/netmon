<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/ip.class.php');
  require_once('./lib/classes/core/service.class.php');

  $Ip = new Ip;

  $smarty->assign('message', Message::getMessage());
  
  $ip_data = Helper::getIpInfo($_GET['id']);
  $smarty->assign('ip', $ip_data);

  $history_count = 500;

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
		$services[$index]['crawl_history'] = Service::getCrawlHistory($service['service_id'], $history_count);
	
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

//Stability
$services[$service_key]['stability']['online'] = 0;
$services[$service_key]['stability']['offline'] = 0;

foreach($services[$service_key]['crawl_history'] as $hist) {
	if ($hist['status']=='online')
		$services[$service_key]['stability']['online']++;
	else
		$services[$service_key]['stability']['offline']++;
}

$services[$service_key]['stability']['gesammt'] = $services[$service_key]['stability']['online']+$services[$service_key]['stability']['offline'];

$services[$service_key]['stability']['percent'] = 100/$services[$service_key]['stability']['gesammt']*$services[$service_key]['stability']['online'];
//-------------
	
	$smarty->assign('service_data', $services[$service_key]);
	$smarty->assign('services', $services);

//Graphics
$crawl_history = $services[$service_key]['crawl_history'];
asort($crawl_history);

/*echo "<pre>";
print_r($crawl_history);
echo "</pre>";*/

$rrd_path_ping = __DIR__."/tmp/ip_$_GET[id]_ping.rrd";
$image_path_ping = __DIR__."/tmp/ip_$_GET[id]_ping.png";
@unlink($rrd_path_ping);
@unlink($image_path_ping);

$rrd_path_loadavg = __DIR__."/tmp/ip_$_GET[id]_loadavg.rrd";
$image_path_loadavg = __DIR__."/tmp/ip_$_GET[id]_loadavg.png";
@unlink($rrd_path_loadavg);
@unlink($image_path_loadavg);


exec("rrdtool create $rrd_path_ping --step 600 --start ".strtotime($crawl_history[$history_count-1]['crawl_time'])." DS:ping:GAUGE:900:U:U RRA:AVERAGE:0:1:".($history_count-1));
exec("rrdtool create $rrd_path_loadavg --step 600 --start ".strtotime($crawl_history[$history_count-1]['crawl_time'])." DS:loadavg:GAUGE:900:U:U RRA:AVERAGE:0:1:".($history_count-1));

foreach ($crawl_history as $key=>$hist) {
	$crawl_time = strtotime($hist['crawl_time']);
	$exec = exec("rrdtool update $rrd_path_ping $crawl_time:$hist[ping]");
	$exec = exec("rrdtool update $rrd_path_loadavg $crawl_time:$hist[loadavg]");
}


$exec = exec("rrdtool graph $image_path_ping -a PNG --title='Ping' --vertical-label 'Ping in ms' --start ".strtotime($crawl_history[$history_count-1]['crawl_time'])." --end ".strtotime($crawl_history[0]['crawl_time'])." DEF:myspeed=$rrd_path_ping:ping:AVERAGE LINE1:myspeed#ff0000");
$exec = exec("rrdtool graph $image_path_loadavg -a PNG --title='Loadavg' --vertical-label 'Loadavg' --start ".strtotime($crawl_history[$history_count-1]['crawl_time'])." --end ".strtotime($crawl_history[0]['crawl_time'])." DEF:myspeed=$rrd_path_loadavg:loadavg:AVERAGE LINE1:myspeed#ff0000");

//-------------------------------------

  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('is_ip_owner', $Ip->is_ip_owner);
  $smarty->assign('subnet_data', Helper::getSubnetById($ip_data['subnet_id']));

  $smarty->assign('servicelist', $Ip->getServiceList($_GET['id']));
  $smarty->display("header.tpl.php");
  $smarty->display("ip.tpl.php");
  $smarty->display("footer.tpl.php");
?>