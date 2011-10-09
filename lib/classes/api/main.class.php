<?php

require_once('lib/classes/core/interfaces.class.php');
require_once('lib/classes/core/router.class.php');
require_once('lib/classes/core/project.class.php');
require_once('lib/classes/core/imagemaker.class.php');
require_once('lib/classes/core/editinghelper.class.php');
require_once('lib/classes/core/ip.class.php');

class Main {
	public function login($nickname, $password){
		return(login::user_login($nickname, $password));
	}

	public function public_user_info($user_id) {
		return(Helper::getPlublicUserInfoByID($user_id));
	}

	public function subnet_info($subnet_id) {
		return(Helper::getSubnetById($subnet_id));
	}

	public function project_info($project_id) {
		return(Project::getProjectData($project_id));
	}

	public function crawler_config() {
		return array('crawler_ping_timeout' => $GLOBALS['crawler_ping_timeout'],
			     'crawler_curl_timeout' => $GLOBALS['crawler_curl_timeout'],
			     'crawl_cycle' => $GLOBALS['crawl_cycle']);
	}

	public function getAllServiceIDsByServiceType($type) {
		return(Helper::getAllServiceIDsByServiceType($type));
	}
	public function getImages() {
		return(Imagemaker::getImages());
	}

	public function getImageByImageId($image_id) {
		return(Imagemaker::getImageByImageId($image_id));
	}

	public function getImageConfigsByImageId($image_id) {
		return(Imagemaker::getImageConfigsByImageId($image_id));
	}

	public function getImageConfigByConfigId($config_id) {
		return(Imagemaker::getImageConfigByConfigId($config_id));
	}

	public function getAFreeIPv4IPByProjectId($project_id) {
		return(Ip::getAFreeIPv4IPByProjectId($project_id));
	}

	public function getFreeIpRangeByProjectId($project_id, $range, $ip="") {
		return(Ip::getFreeIpRangeByProjectId($project_id, $range, $ip));
	}

	public function getRoutersForCrawl() {
		return(Router::getRoutersForCrawl());
	}

	public function getIPv4InterfacesByRouterId($router_id) {
		return(Interfaces::getIPv4InterfacesByRouterId($router_id));
	}
	


}

?>