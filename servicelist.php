<?php

require_once('runtime.php');
require_once(ROOT_DIR.'/lib/core/Servicelist.class.php');

$servicelist = new Servicelist();
$smarty->assign('servicelist', $servicelist->getServicelist());

$smarty->display("header.tpl.php");
$smarty->display("servicelist.tpl.php");
$smarty->display("footer.tpl.php");

?>