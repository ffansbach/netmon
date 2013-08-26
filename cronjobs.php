<?php
	//This script can only be called by the server
	if(!empty($_SERVER['REMOTE_ADDR'])) {
		die("This script can only be run by the server directly.");
	}
	
	/**
	 * Necessary includes
	 **/
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	require_once(ROOT_DIR.'/lib/core/router.class.php');
	require_once(ROOT_DIR.'/lib/core/rrdtool.class.php');
	require_once(ROOT_DIR.'/lib/core/Eventlist.class.php');
	require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/EventNotificationList.class.php');
	
	/**
	 * Crawlcycle organisation
	 **/
	echo "Organizing crawl cycles\n";
	//Get crawl cycle data
	$actual_crawl_cycle = Crawling::getActualCrawlCycle();
	$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
	
	//Create new crawl cycle and close old crawl cycle
	if(empty($actual_crawl_cycle) OR strtotime($actual_crawl_cycle['crawl_date'])+(($GLOBALS['crawl_cycle']-1)*60)<=time()) {
		//Create new crawl cycle
		echo "Close old crawl cycle and create new one\n";
		Crawling::newCrawlCycle();
		
		//Close old Crawl cycle
		Crawling::closeCrawlCycle($actual_crawl_cycle['id']);
		
		echo "Mark all routers that could not be reached in last crawl cycle as offline\n";
		//Set all routers in old crawl cycle that have not been crawled yet to status offline
		$routers = Router_old::getRouters();
		foreach ($routers as $router) {
			$crawl = Router_old::getCrawlRouterByCrawlCycleId($actual_crawl_cycle['id'], $router['id']);
			if(empty($crawl)) {
				$router_status = New RouterStatus(false, (int)$actual_crawl_cycle['id'], (int)$router['id'], "offline");
				$router_status->store();
				
				//make router history
				$router_status_tmp = new RouterStatus(false, (int)$last_endet_crawl_cycle['id'], (int)$router['id']);
				$router_status_tmp->fetch();
				$eventlist = $router_status->compare($router_status_tmp);
				$eventlist->store();
			}
		}
		
		echo "Create graph statistics\n";
		//Make statistic graphs
		$online = Router_old::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'online');
		$offline = Router_old::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'offline');
		$unknown = Router_old::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'unknown');
		$total = $unknown+$offline+$online;
		RrdTool::updateNetmonHistoryRouterStatus($online, $offline, $unknown, $total);
		
		$client_count = Router_old::countRoutersByCrawlCycleId($actual_crawl_cycle['id']);
		RrdTool::updateNetmonClientCount($client_count);
	} else {
		echo "There is an crawl cycle running actually. Doing nothing.\n";
	}
	
	/**
	 * Clean database
	 */
	echo "Clean database\n";
	
	//Delete old Crawls
	echo "Remove old crawl data\n";
	Crawling::deleteOldCrawlDataExceptLastOnlineCrawl(($GLOBALS['hours_to_keep_mysql_crawl_data']*60*60));
	
	//Remove old events
	echo "Remove old events\n";
	//Get number of total events in db
	$total_count = new Eventlist();
	$total_count->init(false, false, false, 0, 0);
	$total_count = $total_count->getTotalCount();
	//Fetch the 50 oldest events from db and check if they need to be deleted.
	//Then fetch the next 50 oldest events until you get to an event that is not old enough to delete it
	//or if you looped through all events.
	for($offset=0; $offset<$total_count; $offset+=50) {
		$eventlist = new Eventlist();
		$eventlist->init(false, false, false, $offset, 50, 'create_date', 'asc');
		foreach($eventlist->getEventlist() as $event) {
			if($event->getCreateDate() < time()-60*60*$GLOBALS['hours_to_keep_history_table']) {
				$event->delete();
			} else {
				$offset=$total_count;
				break;
			}
		}
	}
	
	//Remove old not assigned routers
	echo "Remove not assigned routers that haven´t been updated for a while\n";
	DB::getInstance()->exec("DELETE FROM routers_not_assigned WHERE TO_DAYS(update_date) < TO_DAYS(NOW())-2");
	
	/**
	* Crawl
	**/
	echo "Do crawling\n";
	
	//Crawl routers
	echo "Crawl routers\n";
	$routers_count = Router_old::countRouters();
	for ($i=0; $i<=$routers_count; $i+=10) {
		//start an independet crawl process for each 10 routers to crawl routers simultaniously
		echo "Initializing crawl process to crawl routers ".$i." to ".($i+10)."\n";
		
		$return = array();
		$cmd = "php ".ROOT_DIR."/integrated_xml_ipv6_crawler.php -f".$i." -t10  &> /dev/null & echo $!";
		echo "Running command: $cmd\n";
		exec($cmd, $return);
		echo "The initialized crawl process has the pid $return[0]\n";
	}
	
	/**
	 * Notifications
	 */
	echo "Sending notifications\n";
	$event_notification_list = new EventNotificationList();
	$event_notification_list->notify();
	
	echo "Done\n";
?>