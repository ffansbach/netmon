<?php

require_once('runtime.php');
require_once('./lib/classes/core/config.class.php');
require_once('lib/classes/core/install.class.php');

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
		$smarty->assign('php_version', true);
	else
		$smarty->assign('php_version', false);
	
	$smarty->assign('pdo_loaded', extension_loaded('pdo'));
	$smarty->assign('pdo_mysql_loaded', extension_loaded('pdo_mysql'));
	$smarty->assign('json_loaded', extension_loaded('json'));
	$smarty->assign('curl_loaded', extension_loaded('curl'));	
	$smarty->assign('gd_loaded', extension_loaded('gd'));
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
	Config::writeConfigLine('url_to_netmon', $_POST['url_to_netmon']);
	Config::writeConfigLine('community_name', $_POST['community_name']);

	Config::writeConfigLine('mail_sender_adress', $_POST['mail_sender_adress']);
	Config::writeConfigLine('mail_sender_name', $_POST['mail_sender_name']);
	Config::writeConfigLine('mail_sending_type', $_POST['mail_sending_type']);
	Config::writeConfigLine('mail_smtp_server', $_POST['mail_smtp_server']);
	Config::writeConfigLine('mail_smtp_username', $_POST['mail_smtp_username']);
	Config::writeConfigLine('mail_smtp_password', $_POST['mail_smtp_password']);
	Config::writeConfigLine('mail_smtp_login_auth', $_POST['mail_smtp_login_auth']);
	Config::writeConfigLine('mail_smtp_ssl', $_POST['mail_smtp_ssl']);

	Config::writeConfigLine('enable_network_policy', 0);
	Config::writeConfigLine('network_policy_url', "");
	Config::writeConfigLine('template', "freifunk_oldenburg");
	Config::writeConfigLine('hours_to_keep_mysql_crawl_data', 5);
	Config::writeConfigLine('hours_to_keep_history_table', 72);
	Config::writeConfigLine('crawl_cycle_length_in_minutes', 10);

	header('Location: ./install.php?section=finish');
} elseif ($_GET['section']=="finish") {
	Config::writeConfigLine('installed', true);

	$message[] = array('Netmon wurde erfolgreich installiert.', 1);
	$message[] = array('Die Installationsroutine wurde für weitere Zugriffe gesperrt.', 1);
	$message[] = array('Bitte registrieren Sie sich, der erste registrierte Benutzer bekommt automatisch volle Root-Rechte!', 1);

	Message::setMessage($message);
	header('Location: ./register.php');
}

?>
