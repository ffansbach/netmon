<?php

require_once('/var/kunden/webs/freifunk/netmon/config/runtime.inc.php');
require_once('/var/kunden/webs/freifunk/netmon/lib/classes/core/service.class.php');

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