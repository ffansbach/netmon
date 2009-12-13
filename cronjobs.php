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

//Remove old generated images
$files = scandir("scripts/imgbuild/dest/");
foreach($files as $file) {
	if ($file!=".." AND $file!=".") {
		$exploded_name = explode("_", $file);
		if(!empty($exploded_name[1]) AND $exploded_name[1]<(time()-1800)) {
			exec("rm -Rf $_SERVER[DOCUMENT_ROOT]/scripts/imgbuild/dest/$file");
		}
	}
}

?>