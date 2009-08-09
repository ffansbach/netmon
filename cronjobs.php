<?php

	if (!empty($_SERVER["REQUEST_URI"]))
		$path = "";
	else
		$path = dirname(__FILE__)."/";

	require_once($path.'config/runtime.inc.php');
	require_once($path.'lib/classes/core/service.class.php');

/*
 * Get all Services that have notification on
 */
try {
	$sql = "SELECT id as service_id
	        FROM  services
	        WHERE notify=1";
	$result = DB::getInstance()->query($sql);
	foreach($result as $row) {
		service::offlineNotification($row['service_id']);
	}
}
catch(PDOException $e) {
	echo $e->getMessage();
}

?>