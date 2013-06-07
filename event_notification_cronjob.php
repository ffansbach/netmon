<?php
	/**
	* IF Netmon is called by the server/cronjob
	*/
	if (empty($_SERVER["REQUEST_URI"])) {
		$path = dirname(__FILE__)."/";
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		$GLOBALS['netmon_root_path'] = $path."/";
	}
	
	if(!empty($_SERVER['REMOTE_ADDR'])) {
		die("This script can only be run by the server directly.");
	}
	
	$GLOBALS['cronjob'] = true;
	
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/classes/core/EventNotificationList.class.php');

	$event_notification_list = new EventNotificationList();
	$event_notification_list->notify();

?>