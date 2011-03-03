<?php

require_once('runtime.php');
require_once('lib/classes/core/service.class.php');
require_once('lib/classes/core/usermanagement.class.php');

$is_logged_id = Usermanagement::isLoggedIn($SESSION['user_id']);
if($is_logged_id) {
	$view='all';
} else {
	$view='public';
}

$servicelist = Service::getServiceList($view);
$smarty->assign('servicelist', $servicelist);

$smarty->display("header.tpl.php");
$smarty->display("servicelist.tpl.php");
$smarty->display("footer.tpl.php");

?>