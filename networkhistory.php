<?php

/** Include Classes **/
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/history.class.php');

/** Get and assign global messages **/
$smarty->assign('message', Message::getMessage());

/** History **/
$history = History::getHistory(false, $GLOBALS['portal_history_hours']);

$smarty->assign('history_hours', $GLOBALS['portal_history_hours']);
$smarty->assign('history', $history);

/** Display templates **/
$smarty->display("header.tpl.php");
$smarty->display("networkhistory.tpl.php");
$smarty->display("footer.tpl.php");

?>
