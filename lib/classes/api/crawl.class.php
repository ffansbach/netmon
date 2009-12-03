<?php

require_once("./lib/classes/core/service.class.php");
require_once("./lib/classes/core/login.class.php");

class Crawl {
	public function receive($nickname, $password, $service_id, $crawl_data) {
		$session = login::user_login($nickname, $password);
		
		$service_data = Helper::getServiceDataByServiceId($service_id);
		//If is owning user or if root
		if(UserManagement::isThisUserOwner($service_data['user_id'], $session['user_id']) OR $session['permission']==120) {
			service::insertStatus($crawl_data, $service_id);
			service::clearCrawlDatabase($service_id);

			service::makeHistoryEntry($crawl_data, $service_id);
			service::clearHistory($service_id);

			service::offlineNotification($service_id);
			service::updateNotificationStatus($crawl_data['status'], $service_id);
			return true;
		} else {
			return false;
		}
	}
}

?>