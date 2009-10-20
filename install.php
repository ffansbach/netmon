<?php
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/register.class.php');

if ($GLOBALS['installed']) {
	$message[] = array("Die Intallation wurde per /config/config.local.inc.php gesperrt.", 2);
	message::setMessage($message);
    $smarty->assign('message', message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("login.tpl.php");
    $smarty->display("footer.tpl.php");
} else {
    $smarty->assign('netmon_version', $GLOBALS['netmon_version']);
    $smarty->assign('netmon_codename', $GLOBALS['netmon_codename']);

    $smarty->assign('pdo_loaded', extension_loaded('pdo'));
    $smarty->assign('pdo_mysql_loaded', extension_loaded('pdo_mysql'));
    $smarty->assign('json_loaded', extension_loaded('json'));
    $smarty->assign('curl_loaded', extension_loaded('curl'));	
    $smarty->assign('gd_loaded', extension_loaded('gd'));

    $smarty->display("header.tpl.php");
    $smarty->display("install_step_one.tpl.php");
    $smarty->display("footer.tpl.php");
}
?>