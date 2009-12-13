<?php

class Main {
	public function login($nickname, $password){
		return(login::user_login($nickname, $password));
	}

	public function public_user_info($user_id) {
		return(Helper::getPlublicUserInfoByID($user_id));
	}

	public function project_info() {
		return(Helper::getProjectInfo());
	}

	public function subnet_info($subnet_id) {
		return(Helper::getSubnetDataBySubnetID($subnet_id));
	}

	public function getAllServiceIDsByServiceType($type) {
		return(Helper::getAllServiceIDsByServiceType($type));
	}
	
	public function add($a, $b) {
		return($a+$b);
	}

	public function bla($string) {
		return($string);
	}

	public function writeSomething($something) {
		throw new Exception('writeSomething method is not available for RPC');
	}

}

?>