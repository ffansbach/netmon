<?php
	require_once('runtime.php');
	require_once('lib/core/Event.class.php');
	
	//get messages of the message system
	$smarty->assign('message', Message::getMessage());
	
	//get data
	$event = new Event((int)$_GET['event_id']);
	$event->fetch();
	$smarty->assign('event', $event);
	
	//load the template
	$smarty->display("header.tpl.html");
	$smarty->display("event.tpl.html");
	$smarty->display("footer.tpl.html");
?>