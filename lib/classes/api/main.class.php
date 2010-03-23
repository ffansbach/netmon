<?php

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

	public function getAllServiceIDsByServiceType($type) {
		return(Helper::getAllServiceIDsByServiceType($type));
	}
	public function getImages() {
		return(Helper::getImages());
	}

	public function getImageByImageId($image_id) {
		return(Helper::getImageByImageId($image_id));
	}

	public function getImageConfigsByImageId($image_id) {
		return(Helper::getImageConfigsByImageId($image_id));
	}

	public function getImageConfigByConfigId($config_id) {
		return(Helper::getImageConfigByConfigId($config_id));
	}

}

?>