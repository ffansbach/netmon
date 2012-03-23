<?php

require_once('runtime.php');
require_once('lib/classes/core/service.class.php');
require_once('lib/classes/core/usermanagement.class.php');

$is_logged_id = Usermanagement::isLoggedIn($_SESSION['user_id']);
if($is_logged_id) {
	$view='all';
} else {
	$view='public';
}

$servicelist = Service::getServiceList($view);
$servicelist_all = Service::getServiceList('all');
$smarty->assign('servicelist', $servicelist);

$hidden_service_count = count($servicelist_all)-count($servicelist);
$smarty->assign('hidden_service_count', $hidden_service_count);

$smarty->display("header.tpl.php");
$smarty->display("servicelist.tpl.php");
$smarty->display("footer.tpl.php");

?>