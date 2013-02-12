<?php

$GLOBALS['installation_mode'] = true;

require_once('runtime.php');
require_once('lib/classes/core/install.class.php');

$smarty->assign('installation_mode', $GLOBALS['installation_mode']);

if ($GLOBALS['installed']) {
	$message[] = array("Die Intallation wurde per /config/config.local.inc.php gesperrt.", 2);
	Message::setMessage($message);
    $smarty->assign('message', Message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("login.tpl.php");
    $smarty->display("footer.tpl.php");
} elseif (!isset($_GET['section'])) {
    $smarty->assign('netmon_version', $GLOBALS['netmon_version']);
    $smarty->assign('netmon_codename', $GLOBALS['netmon_codename']);

    if (strnatcmp(phpversion(),'5.2') >= 0)
    {
	$smarty->assign('php_version', true);
    }
    else
    {
	$smarty->assign('php_version', false);
    } 

    $smarty->assign('pdo_loaded', extension_loaded('pdo'));
    $smarty->assign('pdo_mysql_loaded', extension_loaded('pdo_mysql'));
    $smarty->assign('json_loaded', extension_loaded('json'));
    $smarty->assign('curl_loaded', extension_loaded('curl'));	
    $smarty->assign('gd_loaded', extension_loaded('gd'));
    $smarty->assign('zip_loaded', extension_loaded('zip'));
    $smarty->assign('openssl', extension_loaded('openssl'));
    $smarty->assign('ftp', extension_loaded('ftp'));
    $smarty->assign('exec', function_exists('exec'));

    $to = "noreply@noreply.org";
    $header = "From: {$to}";
    $subject = "Netmon Mailtest";
    $body = "This is a Mail that was send by netmon Mailtest";
    if (mail($to, $subject, $body, $header)) {
        $smarty->assign('mail', true);
	$_SESSION['mail'] = "php_mail";
    } else {
        $smarty->assign('mail', false);
	$_SESSION['mail'] = "smtp";
    }

    try {
       $smarty->display("header.tpl.php");
       $smarty->display("install_info.tpl.php");
       $smarty->display("footer.tpl.php");
    }
    catch(Exception $e) {
	print $e->getMessage();
    }
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
		Message::setMessage($message);
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.php");
		$smarty->display("install_db_data.tpl.php");
		$smarty->display("footer.tpl.php");
	} else {
		$config_path = "./config/config.local.inc.php";
		$file = Install::getFileLineByLine($config_path);

		$configs[0] = '$GLOBALS[\'mysql_host\'] = "'.$_POST['host'].'";';
		$configs[1] = '$GLOBALS[\'mysql_db\'] = "'.$_POST['database'].'";';
		$configs[2] = '$GLOBALS[\'mysql_user\'] = "'.$_POST['user'].'";';
		$configs[3] = '$GLOBALS[\'mysql_password\'] = "'.$_POST['password'].'";';

		$file = Install::changeConfigSection('//MYSQL', $file, $configs);

		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);
		header('Location: ./install.php?section=db_insert_method');
	}
} elseif ($_GET['section']=="db_insert_method") {
	if(Install::checkIfDbIsEmpty()) {
		header('Location: ./install.php?section=db_insert');
	} else {
		$smarty->display("header.tpl.php");
		$smarty->display("install_db_insert_method.tpl.php");
		$smarty->display("footer.tpl.php");
	}
} elseif ($_GET['section']=="db_insert") {
	Install::insertDB();
	header('Location: ./install.php?section=messages');
} elseif ($_GET['section']=="messages") {

        $smarty->assign('mail_sending_type', $_SESSION['mail']);
        $smarty->assign('url_to_netmon', "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
        $smarty->assign('enable_network_policy', false);

	$smarty->display("header.tpl.php");
	$smarty->display("install_messages.tpl.php");
	$smarty->display("footer.tpl.php");

} elseif ($_GET['section']=="messages_insert") {
	$config_path = "./config/config.local.inc.php";

/*	$file = Install::getFileLineByLine($config_path);
	if ($_POST['installed'])
		$configs[0] = '$GLOBALS[\'installed\'] = true;';
	else
		$configs[0] = '$GLOBALS[\'installed\'] = false;';
	$file = Install::changeConfigSection('//INSTALLATION-LOCK', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);
	unset($configs);*/

	$file = Install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'url_to_netmon\'] = "'.$_POST['url_to_netmon'].'";';
	$file = Install::changeConfigSection('//WEBSERVER', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);
	unset($configs);

/*	$file = Install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'mysql_host\'] = "'.$_POST['mysql_host'].'";';
	$configs[1] = '$GLOBALS[\'mysql_db\'] = "'.$_POST['mysql_db'].'";';
	$configs[2] = '$GLOBALS[\'mysql_user\'] = "'.$_POST['mysql_user'].'";';
	$configs[3] = '$GLOBALS[\'mysql_password\'] = "'.$_POST['mysql_password'].'";';
	$file = Install::changeConfigSection('//MYSQL', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);
	unset($configs);*/

	$file = Install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'jabber_server\'] = "'.$_POST['jabber_server'].'";';
	$configs[1] = '$GLOBALS[\'jabber_username\'] = "'.$_POST['jabber_username'].'";';
	$configs[2] = '$GLOBALS[\'jabber_password\'] = "'.$_POST['jabber_password'].'";';
	$file = Install::changeConfigSection('//JABBER', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);
	unset($configs);

	$file = Install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'mail_sending_type\'] = "'.$_POST['mail_sending_type'].'";';
	$configs[1] = '$GLOBALS[\'mail_sender_adress\'] = "'.$_POST['mail_sender_adress'].'";';
	$configs[2] = '$GLOBALS[\'mail_sender_name\'] = "'.$_POST['mail_sender_name'].'";';
	$configs[3] = '$GLOBALS[\'mail_smtp_server\'] = "'.$_POST['mail_smtp_server'].'";';
	$configs[4] = '$GLOBALS[\'mail_smtp_username\'] = "'.$_POST['mail_smtp_username'].'";';
	$configs[5] = '$GLOBALS[\'mail_smtp_password\'] = "'.$_POST['mail_smtp_password'].'";';
	$configs[6] = '$GLOBALS[\'mail_smtp_login_auth\'] = "'.$_POST['mail_smtp_login_auth'].'";';
	$configs[7] = '$GLOBALS[\'mail_smtp_ssl\'] = "'.$_POST['mail_smtp_ssl'].'";';
	$file = Install::changeConfigSection('//MAIL', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);
	unset($configs);

	$file = Install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'net_prefix\'] = "'.$_POST['net_prefix'].'";';
	$configs[1] = '$GLOBALS[\'community_name\'] = "'.$_POST['community_name'].'";';
	$configs[2] = '$GLOBALS[\'community_website\'] = "'.$_POST['community_website'].'";';
	if ($_POST['enable_network_policy'])
		$configs[3] = '$GLOBALS[\'enable_network_policy\'] = true;';
	else
		$configs[3] = '$GLOBALS[\'enable_network_policy\'] = false;';
	$configs[4] = '$GLOBALS[\'networkPolicy\'] = "'.$_POST['networkPolicy'].'";';
	$file = Install::changeConfigSection('//NETWORK', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);
	unset($configs);

	$file = Install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'days_to_keep_mysql_crawl_data\'] = '.$_POST['days_to_keep_mysql_crawl_data'].';';
	$file = Install::changeConfigSection('//PROJEKT', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);
	unset($configs);

	$file = Install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'google_maps_api_key\'] = "'.$_POST['google_maps_api_key'].'";';
	$file = Install::changeConfigSection('//GOOGLEMAPSAPIKEY', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);
	unset($configs);

	header('Location: ./install.php?section=finish');
} elseif ($_GET['section']=="finish") {
	$config_path = "./config/config.local.inc.php";
	$file = Install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'installed\'] = true;';
	$file = Install::changeConfigSection('//INSTALLATION-LOCK', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);

	$message[] = array('Netmon wurde erfolgreich installiert.', 1);
	$message[] = array('Die Installationsroutine wurde für weitere Zugriffe gesperrt.', 1);
	$message[] = array('Bitte registrieren Sie sich, der erste registrierte Benutzer bekommt automatisch volle Root-Rechte!', 1);

	Message::setMessage($message);
	header('Location: ./register.php');
}

?>
