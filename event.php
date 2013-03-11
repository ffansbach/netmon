<?php

require_once('runtime.php');
require_once('lib/classes/core/event.class.php');

//get messages of the message system
$smarty->assign('message', Message::getMessage());

//get data
$smarty->assign('event', Event::getEvent($_GET['event_id']));

//load the temlate
$smarty->display("header.tpl.php");
$smarty->display("event.tpl.php");
$smarty->display("footer.tpl.php");

?>