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
	
	/**Get number of routers by chipset **/
	$chipsets = Helper::getChipsets();
	foreach ($chipsets as $key=>$chipset) {
		$router_chipsets[$key]['chipset_id'] = $chipset['id'];
		$router_chipsets[$key]['chipset_name'] = $chipset['name'];
		$router_chipsets[$key]['hardware_name'] = $chipset['hardware_name'];
		$router_chipsets[$key]['count'] = Router::countRoutersByChipsetId($chipset['id']);;
	}
	
	$smarty->assign('router_chipsets', $router_chipsets);

	/**Get number of routers by batman advanced version **/
	try {
		$sql = "SELECT  batman_advanced_version 
				FROM crawl_routers
			WHERE crawl_cycle_id=$last_ended_crawl_cycle[id]
			GROUP BY batman_advanced_version";
		$result = DB::getInstance()->query($sql);
		foreach($result as $row) {
			if(!empty($row['batman_advanced_version'])) {
				$batman_advanced_versions[] = $row;
			}
		}
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}

	foreach($batman_advanced_versions as $key=>$batman_advanced_version) {
		$batman_advanced_versions_count[$key]['batman_advanced_version'] = $batman_advanced_version['batman_advanced_version'];

		try {
			$sql = "SELECT  COUNT(*) as count
					FROM crawl_routers
				WHERE crawl_cycle_id=$last_ended_crawl_cycle[id] AND status='online' AND batman_advanced_version='".$batman_advanced_versions_count[$key]['batman_advanced_version']."'";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		$batman_advanced_versions_count[$key]['count'] = $count['count'];
	}

	$smarty->assign('batman_advanced_versions_count', $batman_advanced_versions_count);

	/**Get number of routers by kernel version **/
	try {
		$sql = "SELECT  kernel_version 
				FROM crawl_routers
			WHERE crawl_cycle_id=$last_ended_crawl_cycle[id]
			GROUP BY kernel_version";
		$result = DB::getInstance()->query($sql);
		foreach($result as $row) {
			if(!empty($row['kernel_version'])) {
				$kernel_versions[] = $row;
			}
		}
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}

	foreach($kernel_versions as $key=>$kernel_version) {
		$kernel_versions_count[$key]['kernel_version'] = $kernel_version['kernel_version'];

		try {
			$sql = "SELECT  COUNT(*) as count
					FROM crawl_routers
				WHERE crawl_cycle_id=$last_ended_crawl_cycle[id] AND status='online' AND kernel_version='".$kernel_versions_count[$key]['kernel_version']."'";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		$kernel_versions_count[$key]['count'] = $count['count'];
	}

	$smarty->assign('kernel_versions_count', $kernel_versions_count);

	/**Get number of routers by firmware version **/
	try {
		$sql = "SELECT  firmware_version  
				FROM crawl_routers
			WHERE crawl_cycle_id=$last_ended_crawl_cycle[id]
			GROUP BY firmware_version";
		$result = DB::getInstance()->query($sql);
		foreach($result as $row) {
			if(!empty($row['firmware_version'])) {
				$firmware_versions[] = $row;
			}
		}
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}

	foreach($firmware_versions as $key=>$firmware_version) {
		$firmware_versions_count[$key]['firmware_version'] = $firmware_version['firmware_version'];

		try {
			$sql = "SELECT  COUNT(*) as count
					FROM crawl_routers
				WHERE crawl_cycle_id=$last_ended_crawl_cycle[id] AND status='online' AND firmware_version='".$firmware_versions_count[$key]['firmware_version']."'";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		$firmware_versions_count[$key]['count'] = $count['count'];
	}

	$smarty->assign('firmware_versions_count', $firmware_versions_count);

	/**Get number of routers by nodewatcher version **/
	try {
		$sql = "SELECT  nodewatcher_version  
				FROM crawl_routers
			WHERE crawl_cycle_id=$last_ended_crawl_cycle[id]
			GROUP BY nodewatcher_version";
		$result = DB::getInstance()->query($sql);
		foreach($result as $row) {
			if(!empty($row['nodewatcher_version'])) {
				$nodewatcher_versions[] = $row;
			}
		}
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}

	foreach($nodewatcher_versions as $key=>$nodewatcher_version) {
		$nodewatcher_versions_count[$key]['nodewatcher_version'] = $nodewatcher_version['nodewatcher_version'];

		try {
			$sql = "SELECT  COUNT(*) as count
					FROM crawl_routers
				WHERE crawl_cycle_id=$last_ended_crawl_cycle[id] AND status='online' AND nodewatcher_version='".$nodewatcher_versions_count[$key]['nodewatcher_version']."'";
			$result = DB::getInstance()->query($sql);
			$count = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		$nodewatcher_versions_count[$key]['count'] = $count['count'];
	}

	$smarty->assign('nodewatcher_versions_count', $nodewatcher_versions_count);
	
	 //Count router statuses
	$online = Router::countRoutersByCrawlCycleIdAndStatus($last_ended_crawl_cycle['id'], 'online');
	$offline = Router::countRoutersByCrawlCycleIdAndStatus($last_ended_crawl_cycle['id'], 'offline');
	$unknown = Router::countRoutersByCrawlCycleIdAndStatus($last_ended_crawl_cycle['id'], 'unknown');
	$total = $unknown+$offline+$online;
	
	$smarty->assign('router_status_online', $online);
	$smarty->assign('router_status_offline', $offline);
	$smarty->assign('router_status_unknown', $unknown);
	$smarty->assign('router_status_total', $total);
}

/** Display templates **/
$smarty->display("header.tpl.php");
$smarty->display("networkstatistic.tpl.php");
$smarty->display("footer.tpl.php");

?>
