<?php

class RouterEditor {
	public function insertNewRouter() {
		DB::getInstance()->exec("INSERT INTO routers (user_id, create_date, update_date, crawl_method, hostname, description, location, latitude, longitude, chipset_id)
						      VALUES ('$_SESSION[user_id]', NOW(), NOW(), '$_POST[crawl_method]', '$_POST[hostname]', '$_POST[description]', '$_POST[location]', '$_POST[latitude]', '$_POST[longitude]', '$_POST[chipset_id]');");
		$router_id = DB::getInstance()->lastInsertId();
		
		$message[] = array("Der Router $_POST[hostname] wurde angelegt.", 1);
		Message::setMessage($message);
		
		return array("result"=>true, "router_id"=>$router_id);
	}
}

?>