<?php

require_once(ROOT_DIR.'/lib/core/router_old.class.php');
require_once(ROOT_DIR.'/lib/core/crawling.class.php');
require_once(ROOT_DIR.'/lib/core/rrdtool.class.php');
require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');
require_once(ROOT_DIR.'/lib/core/Networkinterface.class.php');
require_once(ROOT_DIR.'/lib/core/NetworkinterfaceStatus.class.php');
require_once(ROOT_DIR.'/lib/core/Ip.class.php');
require_once(ROOT_DIR.'/lib/core/Validation.class.php');

class Crawl {
	public function insertCrawlData($data) {
		if (!is_int($data['router_id']))
			return;

		$router_data = Router_old::getRouterInfo($data['router_id']);
		$actual_crawl_cycle = Crawling::getActualCrawlCycle();
		$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();

		/**Insert Router Interfaces*/
		foreach($data['interface_data'] as $sendet_interface) {
			if (!Validation::isValidInterfaceName($sendet_interface['name']))
				continue;

			//Update RRD Graph DB
			$interface_last_endet_crawl = Interfaces::getInterfaceCrawlByCrawlCycleAndRouterIdAndInterfaceName($last_endet_crawl_cycle['id'], $data['router_id'], $sendet_interface['name']);

			$traffic_rx_bps = round(($sendet_interface['traffic_rx']-$interface_last_endet_crawl['traffic_rx'])/$GLOBALS['crawl_cycle']/60);
			$traffic_rx_bps = ($traffic_rx_bps<0) ? 0 : $traffic_rx_bps;

			$traffic_tx_bps = round(($sendet_interface['traffic_tx']-$interface_last_endet_crawl['traffic_tx'])/$GLOBALS['crawl_cycle']/60);
			$traffic_tx_bps = ($traffic_tx_bps<0) ? 0 : $traffic_tx_bps;

			//Set default indizies to prevent from warnings
			$sendet_interface['wlan_frequency'] = (isset($sendet_interface['wlan_frequency'])) ? preg_replace("/([A-Za-z])/","",$sendet_interface['wlan_frequency']) : "";
			$sendet_interface['wlan_mode'] = (isset($sendet_interface['wlan_mode'])) ? $sendet_interface['wlan_mode'] : "";
			$sendet_interface['wlan_essid'] = (isset($sendet_interface['wlan_essid'])) ? $sendet_interface['wlan_essid'] : "";
			$sendet_interface['wlan_bssid'] = (isset($sendet_interface['wlan_bssid'])) ? $sendet_interface['wlan_bssid'] : "";
			$sendet_interface['wlan_tx_power'] = (isset($sendet_interface['wlan_tx_power'])) ? $sendet_interface['wlan_tx_power'] : 0;

			//check if interface already exists
			$networkinterface_test = new Networkinterface(false, (int)$data['router_id'], $sendet_interface['name']);
			//if interface not exist, create new
			if(!$networkinterface_test->fetch()) {
				$networkinterface_new = new Networkinterface(false, (int)$data['router_id'], $sendet_interface['name']);
				$networkinterface_id = $networkinterface_new->store();
			} else {
				$networkinterface_id = $networkinterface_test->getNetworkinterfaceId();
			}

			//save crawl data for interface
			$networkinterface_status = new NetworkinterfaceStatus(false, (int)$actual_crawl_cycle['id'], (int)$networkinterface_id, (int)$data['router_id'],
																  $sendet_interface['name'], $sendet_interface['mac_addr'], (int)$sendet_interface['mtu'],
																  (int)$sendet_interface['traffic_rx'], (int)$traffic_rx_bps,
																  (int)$sendet_interface['traffic_tx'], (int)$traffic_tx_bps,
																  $sendet_interface['wlan_mode'], $sendet_interface['wlan_frequency'], $sendet_interface['wlan_essid'], $sendet_interface['wlan_bssid'],
																  (int)$sendet_interface['wlan_tx_power'], false);
			$networkinterface_status->store();

			//Update RRDDatabase
			$rrd_path_traffic_rx = ROOT_DIR."/rrdtool/databases/router_$data[router_id]_interface_$sendet_interface[name]_traffic_rx.rrd";
			if(!file_exists($rrd_path_traffic_rx)) {
				exec("rrdtool create $rrd_path_traffic_rx --step 600 --start ".time()." DS:traffic_rx:GAUGE:700:U:U DS:traffic_tx:GAUGE:900:U:U RRA:AVERAGE:0:1:144 RRA:AVERAGE:0:6:168 RRA:AVERAGE:0:18:240");
			}
			exec("rrdtool update $rrd_path_traffic_rx ".time().":".round($traffic_rx_bps/1000, 2).":".round($traffic_tx_bps/1000, 2));

			//add unknown ipv6 link local addresses to netmon
			//prepare data
            if (isset($sendet_interface['ipv6_link_local_addr']))
    			$ipv6_link_local_addr = explode("/", $sendet_interface['ipv6_link_local_addr']);
            else
    			$ipv6_link_local_addr[0] = "";
			$ipv6_link_local_netmask = (isset($ipv6_link_local_addr[1])) ? (int)$ipv6_link_local_addr[1] : 64;
			$ipv6_link_local_addr = Ip::ipv6Expand($ipv6_link_local_addr[0]);

			//first try to determine network of given address
			$ipv6_link_local_network = Ip::ipv6NetworkFromAddr($ipv6_link_local_addr, (int)$ipv6_link_local_netmask);
			$network = new Network(false, false, $ipv6_link_local_network, (int)$ipv6_link_local_netmask, 6);
			if($network->fetch()) {
				//if network found, then try to add ip address
				$ip = new Ip(false, (int)$networkinterface_id, $network->getNetworkId(), $ipv6_link_local_addr);
				$ip->store();
			}
		}

		RrdTool::updateRouterClientCountHistory($data['router_id'], $data['client_count']);
	}
}

?>