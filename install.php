<?php

require_once('runtime.php');
require_once(ROOT_DIR.'/lib/classes/core/config.class.php');
require_once(ROOT_DIR.'/lib/classes/core/install.class.php');
require_once(ROOT_DIR.'/lib/classes/core/crawling.class.php');
require_once(ROOT_DIR.'/lib/classes/extern/Zend/Mail.php');
require_once(ROOT_DIR.'/lib/classes/extern/Zend/Mail/Transport/Smtp.php');

if ($GLOBALS['installed']) {
	$message[] = array("Die Intallation wurde gesperrt.", 2);
	Message::setMessage($message);
	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("login.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif (!isset($_GET['section'])) {
	$smarty->assign('netmon_version', $GLOBALS['netmon_version']);
	$smarty->assign('netmon_codename', $GLOBALS['netmon_codename']);

	if (strnatcmp(phpversion(),'5.3') >= 0) $smarty->assign('php_version', true);
	else $smarty->assign('php_version', false);

	$smarty->assign('openssl_loaded', extension_loaded('openssl'));
	$smarty->assign('pdo_loaded', extension_loaded('pdo'));
	$smarty->assign('pdo_mysql_loaded', extension_loaded('pdo_mysql'));
	$smarty->assign('json_loaded', extension_loaded('json'));
	$smarty->assign('curl_loaded', extension_loaded('curl'));	
	$smarty->assign('gd_loaded', extension_loaded('gd'));
	$smarty->assign('gmp_loaded', extension_loaded('gmp'));
	$smarty->assign('exec', function_exists('exec'));
	$smarty->assign('iconv_loaded', function_exists('iconv'));
	
	exec('rrdtool', $rrdToolCheckOutput, $rrdToolCheckReturn);
	$smarty->assign('rrdtool_installed', ($rrdToolCheckReturn == 127) ? false : true);
	
	if (mail("noreply@noreply.org", "Netmon Mailtest", "This is a Mail that was send by netmon Mailtest", "From: noreply@noreply.org")) {
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
		new PDO("mysql:host=".$_POST['mysql_host'].";dbname=".$_POST['mysql_db'], $_POST['mysql_user'], $_POST['mysql_password']);
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
		$configs[0] = '$GLOBALS[\'mysql_host\'] = "'.$_POST['mysql_host'].'";';
		$configs[1] = '$GLOBALS[\'mysql_db\'] = "'.$_POST['mysql_db'].'";';
		$configs[2] = '$GLOBALS[\'mysql_user\'] = "'.$_POST['mysql_user'].'";';
		$configs[3] = '$GLOBALS[\'mysql_password\'] = "'.$_POST['mysql_password'].'";';
		$file = Install::changeConfigSection('//MYSQL', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);

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
	$smarty->assign('message', Message::getMessage());

	$smarty->display("header.tpl.php");
	$smarty->display("install_messages.tpl.php");
	$smarty->display("footer.tpl.php");

} elseif ($_GET['section']=="messages_insert") {
	//test mail
	if ($_POST['mail_sending_type']=='smtp') {
		$config = array('username' => $_POST['mail_smtp_username'],
				'password' => $_POST['mail_smtp_password']);
		if(!empty($_POST['mail_smtp_ssl'])) $config['ssl'] = $_POST['mail_smtp_ssl'];
		if(!empty($_POST['mail_smtp_login_auth'])) $config['auth'] = $_POST['mail_smtp_login_auth'];
		$transport = new Zend_Mail_Transport_Smtp($_POST['mail_smtp_server'], $config);
	}
	
	$mail = new Zend_Mail();
	$mail->setFrom($_POST['mail_sender_adress'], $_POST['mail_sender_name']);
	$mail->addTo($_POST['mail_sender_adress']);
	$mail->setSubject("Testmail");
	$mail->setBodyText("This is a testmail from Netmon");
	
	try {
		$mail->send($transport);
	} catch(Exception $e) {
		$exception = $e->getMessage();
		print_r($exception);
	}

	if($exception) {
		$message[] = array("Das Senden der Testmail an ".$_POST['mail_sender_adress']." ist fehlgeschlagen.", 2);
		$message[] = array($exception, 2);
		Message::setMessage($message);
		header('Location: ./install.php?section=messages');
	} else {
		$message[] = array("Das Senden der Testmail an ".$_POST['mail_sender_adress']." war erfolgreich.", 2);
		Config::writeConfigLine('url_to_netmon', $_POST['url_to_netmon']);
		Config::writeConfigLine('community_name', "Freifunk Deinestadt");
		Config::writeConfigLine('community_slogan', "Die freie WLAN-Community aus deiner Stadt • Freie Netze für alle!");
		Config::writeConfigLine('community_location_longitude', '8.2284163781917');
		Config::writeConfigLine('community_location_latitude', '53.14416891433');
		Config::writeConfigLine('community_location_zoom', '11');

		Config::writeConfigLine('mail_sender_adress', $_POST['mail_sender_adress']);
		Config::writeConfigLine('mail_sender_name', $_POST['mail_sender_name']);
		Config::writeConfigLine('mail_sending_type', $_POST['mail_sending_type']);
		Config::writeConfigLine('mail_smtp_server', $_POST['mail_smtp_server']);
		Config::writeConfigLine('mail_smtp_username', $_POST['mail_smtp_username']);
		Config::writeConfigLine('mail_smtp_password', $_POST['mail_smtp_password']);
		Config::writeConfigLine('mail_smtp_login_auth', $_POST['mail_smtp_login_auth']);
		Config::writeConfigLine('mail_smtp_ssl', $_POST['mail_smtp_ssl']);

		Config::writeConfigLine('twitter_consumer_key', "dRWT5eeIn9UiHJgcjgpPQ");
		Config::writeConfigLine('twitter_consumer_secret', "QxcnltPX2sTH8E7eZlxyZeqTIVoIoRjlrmUfkCzGSA");
		
		Config::writeConfigLine('enable_network_policy', 'false');
		Config::writeConfigLine('network_policy_url', 'http://picopeer.net/PPA-de.html');
		Config::writeConfigLine('template', "freifunk_oldenburg");
		Config::writeConfigLine('hours_to_keep_mysql_crawl_data', 5);
		Config::writeConfigLine('hours_to_keep_history_table', 72);
		Config::writeConfigLine('crawl_cycle_length_in_minutes', 10);
		Config::writeConfigLine('event_notification_router_offline_crawl_cycles', 6);
		
		Config::writeConfigLine('community_essid', 'deinestadt.freifunk.net');
		
		//create an initial crawl cycle
		$crawl_cycle_id = Crawling::newCrawlCycle(10);
		require_once(ROOT_DIR.'/cronjobs.php');
		header('Location: ./install.php?section=finish');
	}
} elseif ($_GET['section']=="finish") {
	$config_path = "./config/config.local.inc.php";
	$file = Install::getFileLineByLine($config_path);
	$configs[0] = '$GLOBALS[\'installed\'] = true;';
	$file = Install::changeConfigSection('//INSTALLED', $file, $configs);
	Install::writeEmptyFileLineByLine($config_path, $file);

	$message[] = array('Netmon wurde erfolgreich installiert.', 1);
	$message[] = array('Die Installationsroutine wurde für weitere Zugriffe gesperrt.', 1);
	$message[] = array('Bitte registrieren Sie sich, der erste registrierte Benutzer bekommt automatisch volle Root-Rechte!', 1);

	Message::setMessage($message);
	header('Location: ./register.php');
}

?>
