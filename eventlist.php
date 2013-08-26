<?php

/** Include Classes **/
require_once('runtime.php');
require_once('lib/core/Eventlist.class.php');
require_once('lib/core/RouterStatus.class.php');

/** Get and assign global messages **/
$smarty->assign('message', Message::getMessage());

/** History **/
if(empty($_POST['event_count'])) {
	$event_count = 30;
} else {
	$event_count = $_POST['event_count'];
}

$smarty->assign('event_count', $event_count);
$eventlist = new Eventlist();
$eventlist->init(false, false, false, 0, $event_count, 'create_date', 'desc');
$smarty->assign('eventlist', $eventlist->getEventlist());

/** Display templates **/
$smarty->display("header.tpl.html");
$smarty->display("eventlist.tpl.html");
$smarty->display("footer.tpl.html");

?>
