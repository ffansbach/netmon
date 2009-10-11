<?php

require_once("./lib/classes/core/service.class.php");

class crawl {
	public function receive() {
		$session = login::user_login($_POST['nickname'], $_POST['password']);

		$service_data = Helper::getServiceDataByServiceId($_POST['service_id']);
		//If is owning user or if root
		if(usermanagement::isThisUserOwner($service_data['user_id'], $session['user_id']) OR $session['permission']==120) {
			$current_crawl_data = unserialize(stripslashes($_POST['crawl_data']));
			service::insertStatus($current_crawl_data, $_POST['service_id']);
			service::clearCrawlDatabase($_POST['service_id']);

			service::makeHistoryEntry($current_crawl_data, $_POST['service_id']);
			service::clearHistory($_POST['service_id']);

			service::offlineNotification($_POST['service_id']);
			service::updateNotificationStatus($current_crawl_data['status'], $_POST['service_id']);
			echo "Data for Service $_POST[service_id] inserted\n";
		} else {
			echo "fail";
		}
	}
}

?>