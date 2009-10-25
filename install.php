<?php
require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/install.class.php');

if ($GLOBALS['installed']) {
	$message[] = array("Die Intallation wurde per /config/config.local.inc.php gesperrt.", 2);
	message::setMessage($message);
    $smarty->assign('message', message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("login.tpl.php");
    $smarty->display("footer.tpl.php");
} elseif (!isset($_GET['section'])) {
    $smarty->assign('netmon_version', $GLOBALS['netmon_version']);
    $smarty->assign('netmon_codename', $GLOBALS['netmon_codename']);

    $smarty->assign('pdo_loaded', extension_loaded('pdo'));
    $smarty->assign('pdo_mysql_loaded', extension_loaded('pdo_mysql'));
    $smarty->assign('json_loaded', extension_loaded('json'));
    $smarty->assign('curl_loaded', extension_loaded('curl'));	
    $smarty->assign('gd_loaded', extension_loaded('gd'));
    $smarty->assign('zip_loaded', extension_loaded('zip'));
    $smarty->assign('exec', function_exists('exec'));

    $smarty->assign('ezcomponents', class_exists('ezcBase'));

    $smarty->display("header.tpl.php");
    $smarty->display("install_info.tpl.php");
    $smarty->display("footer.tpl.php");
} elseif ($_GET['section']=="db") {
    $smarty->display("header.tpl.php");
    $smarty->display("install_db_data.tpl.php");
    $smarty->display("footer.tpl.php");
} elseif ($_GET['section']=="check_connection") {
	try {
		new PDO("mysql:host=".$_POST['host'].";dbname=".$_POST['database'], $_POST['user'], $_POST['password']);
	}
	catch(PDOException $e) {
		$exception = $e->getMessage();
	}
	if($exception) {
		$message[] = array($exception, 2);
		message::setMessage($message);
		$smarty->assign('message', message::getMessage());
		$smarty->display("header.tpl.php");
		$smarty->display("install_db_data.tpl.php");
		$smarty->display("footer.tpl.php");
	} else {
		$config_path = "./config/config.local.inc.php";
		$file = install::getFileLineByLine($config_path);

		$configs[0] = '$GLOBALS[\'mysql_host\'] = "'.$_POST['host'].'";';
		$configs[1] = '$GLOBALS[\'mysql_db\'] = "'.$_POST['database'].'";';
		$configs[2] = '$GLOBALS[\'mysql_user\'] = "'.$_POST['user'].'";';
		$configs[3] = '$GLOBALS[\'mysql_password\'] = "'.$_POST['password'].'";';

		$file = install::changeConfigSection('//MYSQL', $file, $configs);

		install::writeEmptyFileLineByLine($config_path, $file);
		header('Location: ./install.php?section=db_insert_method');
	}
} elseif ($_GET['section']=="db_insert_method") {
	if(install::checkIfDbIsEmpty()) {
		header('Location: ./install.php?section=db_insert');
	} else {
		$smarty->display("header.tpl.php");
		$smarty->display("install_db_insert_method.tpl.php");
		$smarty->display("footer.tpl.php");
	}
} elseif ($_GET['section']=="db_insert") {
	install::insertDB();
	header('Location: ./install.php?section=finish');
} elseif ($_GET['section']=="finish") {
	$config_path = "./config/config.local.inc.php";
	$file = install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'installed\'] = true;';
	$file = install::changeConfigSection('//INSTALLATION-LOCK', $file, $configs);
	install::writeEmptyFileLineByLine($config_path, $file);

	$message[] = array('Netmon wurde erfolgreich installiert.', 1);
	$message[] = array('Die Installationsroutine wurde für weitere Zugriffe gesperrt.', 1);
	$message[] = array('Die Konfigurationsdatei wurde unter config/config.local.inc.php abgelegt.', 1);
	$message[] = array('Bitte ergänzen sie zuerst die Konfigurationsdatei!', 1);
	$message[] = array('Bitte registrieren Sie sich, der erste registrierte Benutzer bekommt automatisch volle Root-Rechte!', 1);

	message::setMessage($message);
	header('Location: ./register.php');
}

?>