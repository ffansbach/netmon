<?php

require_once("./lib/classes/core/service.class.php");
require_once("./lib/classes/core/ip.class.php");
require_once("./lib/classes/core/login.class.php");
require_once("./lib/classes/core/olsr.class.php");

class Crawl {
	public function config() {
		return array('crawler_ping_timeout'=>$GLOBALS['crawler_ping_timeout'], 'crawler_curl_timeout'=>$GLOBALS['crawler_curl_timeout']);
	}

	public function receive($nickname, $password, $service_id, $current_crawl_data) {
		$session = login::user_login($nickname, $password);
		
		$service_data = Helper::getServiceDataByServiceId($service_id);

		//If is owning user or if root
		if(UserManagement::isThisUserOwner($service_data['user_id'], $session['user_id']) OR $session['permission']==120) {
			$last_crawl_data = Helper::getLastCrawlDataByServiceId($service_id);
			if($last_crawl_data['status'] == 'online') {
				$last_online_crawl_data = $last_crawl_data;
			} elseif($last_crawl_data['status'] == 'offline') {
				$last_online_crawl_data = Helper::getLastOnlineCrawlDataByServiceId($service_id);
			}

			$crawl_id = service::insertStatus($current_crawl_data, $service_id);
			Olsr::insertOlsrData($crawl_id, $service_id, $current_crawl_data);
			service::clearCrawlDatabase($service_id);

			if($service_data['crawler']=='json') {
				Ip::insertStatus($current_crawl_data, $service_data['ip_id']);
			}

		try {
			service::makeHistoryEntry($current_crawl_data, $last_crawl_data, $last_online_crawl_data, $service_id);
			service::clearHistory($service_id);
		}
		catch(Exception $e) {
			echo $e->getMessage();
		}

			service::offlineNotification($service_id);
			service::updateNotificationStatus($current_crawl_data['status'], $service_id);
			return true;
		} else {
			return false;
		}
	}
}

?>