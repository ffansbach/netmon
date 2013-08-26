<?php

require_once('runtime.php');
require_once(ROOT_DIR.'/lib/core/Servicelist.class.php');

$servicelist = new Servicelist();
$smarty->assign('servicelist', $servicelist->getServicelist());

$smarty->display("header.tpl.html");
$smarty->display("servicelist.tpl.html");
$smarty->display("footer.tpl.html");

?>