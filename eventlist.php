<?php

/** Include Classes **/
require_once('runtime.php');
require_once('lib/classes/core/Eventlist.class.php');

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
$smarty->display("header.tpl.php");
$smarty->display("eventlist.tpl.php");
$smarty->display("footer.tpl.php");

?>
