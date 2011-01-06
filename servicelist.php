<?php

require_once('runtime.php');
require_once('lib/classes/core/service.class.php');

$servicelist = Service::getServiceList();
$smarty->assign('servicelist', $servicelist);

$smarty->display("header.tpl.php");
$smarty->display("servicelist.tpl.php");
$smarty->display("footer.tpl.php");

?>