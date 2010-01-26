<?php

class Main {
	public function login($nickname, $password){
		return(login::user_login($nickname, $password));
	}

	public function public_user_info($user_id) {
		return(Helper::getPlublicUserInfoByID($user_id));
	}

	public function subnet_info($subnet_id) {
		return(Helper::getSubnetDataBySubnetID($subnet_id));
	}

	public function getAllServiceIDsByServiceType($type) {
		return(Helper::getAllServiceIDsByServiceType($type));
	}
}

?>