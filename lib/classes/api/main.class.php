<?php

class main {

	public function login(){
		$session_id = login::user_login($_POST['nickname'], $_POST['password']);
		if($session_id)
			echo json_encode($session_id);
		else
			echo json_encode(false);
	}

	public function public_user_info() {
		echo json_encode(Helper::getPlublicUserInfoByID($_POST['user_id']));
	}

	public function project_info() {
		echo json_encode(Helper::getProjectInfo());
	}

	public function get_services_by_type() {
		echo serialize(Helper::getServicesByType($_GET['type']));
	}

}

?>