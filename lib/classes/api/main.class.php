<?php

require_once('lib/classes/core/interfaces.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/project.class.php');
//require_once('lib/classes/core/ip.class.php');
require_once('lib/classes/core/user.class.php');

class Main {
	public function public_user_info($user_id) {
		return(User::getPlublicUserInfoByID($user_id));
	}

	public function subnet_info($subnet_id) {
		return(Helper::getSubnetById($subnet_id));
	}

	public function project_info($project_id) {
		return(Project::getProjectData($project_id));
	}

	public function crawler_config() {
		return array('crawl_cycle' => $GLOBALS['crawl_cycle']);
	}

	public function getAllServiceIDsByServiceType($type) {
		return(Helper::getAllServiceIDsByServiceType($type));
	}

/*	public function getAFreeIPv4IPByProjectId($project_id) {
		return(Ip::getAFreeIPv4IPByProjectId($project_id));
	}

	public function getFreeIpRangeByProjectId($project_id, $range, $ip="") {
		return(Ip::getFreeIpRangeByProjectId($project_id, $range, $ip));
	}*/

	public function getRoutersForCrawl() {
		return(Router_old::getRoutersForCrawl());
	}
}

?>