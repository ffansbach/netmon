<?php

require_once('runtime.php');
require_once('lib/classes/core/install.class.php');
require_once('lib/classes/core/config.class.php');
require_once('lib/classes/core/chipsets.class.php');
require_once('lib/classes/extern/Zend/Oauth/Consumer.php');


$config_path = "./config/config.local.inc.php";

if ($_GET['section']=="edit") {
	//INSTALLATION-LOCK
	$smarty->assign('installed', $GLOBALS['installed']);

	//WEBSERVER
	$smarty->assign('url_to_netmon', $GLOBALS['url_to_netmon']);
	
	//MYSQL
	$smarty->assign('mysql_host', $GLOBALS['mysql_host']);
	$smarty->assign('mysql_db', $GLOBALS['mysql_db']);
	$smarty->assign('mysql_user', $GLOBALS['mysql_user']);
	$smarty->assign('mysql_password', $GLOBALS['mysql_password']);
	
	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("config.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif ($_GET['section']=="insert_edit") {
		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'mysql_host\'] = "'.$_POST['mysql_host'].'";';
		$configs[1] = '$GLOBALS[\'mysql_db\'] = "'.$_POST['mysql_db'].'";';
		$configs[2] = '$GLOBALS[\'mysql_user\'] = "'.$_POST['mysql_user'].'";';
		$configs[3] = '$GLOBALS[\'mysql_password\'] = "'.$_POST['mysql_password'].'";';
		$file = Install::changeConfigSection('//MYSQL', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);
		unset($configs);

		$message[] = array('Die Konfiguration wurde geändert.', 1);
		Message::setMessage($message);

		header('Location: ./config.php?section=edit');
} elseif($_GET['section']=="edit_netmon") {
	$smarty->assign('installed', $GLOBALS['installed']);
	$smarty->assign('url_to_netmon', Config::getConfigValueByName("url_to_netmon"));
	$smarty->assign('google_maps_api_key', Config::getConfigValueByName("google_maps_api_key"));
	$smarty->assign('template_name', Config::getConfigValueByName("template"));
	$smarty->assign('template', Config::getConfigValueByName("template"));
	$smarty->assign('hours_to_keep_mysql_crawl_data', Config::getConfigValueByName("hours_to_keep_mysql_crawl_data"));
	$smarty->assign('hours_to_keep_history_table', Config::getConfigValueByName("hours_to_keep_history_table"));
	$smarty->assign('crawl_cycle_length_in_minutes', Config::getConfigValueByName("crawl_cycle_length_in_minutes"));

	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("config_netmon.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif($_GET['section']=="insert_edit_netmon") {
	if(UserManagement::checkPermission(120)) {
		if(empty($_POST['installed']))
			$_POST['installed'] = 'false';
		else
			$_POST['installed'] = 'true';

		$file = Install::getFileLineByLine($config_path);
		$configs[0] = '$GLOBALS[\'installed\'] = '.$_POST['installed'].';';
		$file = Install::changeConfigSection('//INSTALLED', $file, $configs);
		Install::writeEmptyFileLineByLine($config_path, $file);

		Config::writeConfigLine('url_to_netmon', $_POST['url_to_netmon']);
		Config::writeConfigLine('google_maps_api_key', $_POST['google_maps_api_key']);
		Config::writeConfigLine('template', $_POST['template']);
		Config::writeConfigLine('hours_to_keep_mysql_crawl_data', $_POST['hours_to_keep_mysql_crawl_data']);
		Config::writeConfigLine('hours_to_keep_history_table', $_POST['hours_to_keep_history_table']);
		Config::writeConfigLine('crawl_cycle_length_in_minutes', $_POST['crawl_cycle_length_in_minutes']);
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_netmon');
	} else {
		$message[] = array('Nur Root kann die Daten ändern.', 2);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_netmon');
	}
} elseif($_GET['section']=="edit_community") {
	$smarty->assign('community_name', Config::getConfigValueByName("community_name"));
	$smarty->assign('community_slogan', Config::getConfigValueByName("community_slogan"));
	$smarty->assign('community_location_longitude', Config::getConfigValueByName("community_location_longitude"));
	$smarty->assign('community_location_latitude', Config::getConfigValueByName("community_location_latitude"));
	$smarty->assign('community_location_zoom', Config::getConfigValueByName("community_location_zoom"));
	$smarty->assign('enable_network_policy', Config::getConfigValueByName("enable_network_policy"));
	$smarty->assign('network_policy_url', Config::getConfigValueByName("network_policy_url"));

	
	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("config_community.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif($_GET['section']=="insert_edit_community") {
	if(UserManagement::checkPermission(120)) {
		Config::writeConfigLine('community_name', $_POST['community_name']);
		Config::writeConfigLine('community_slogan', $_POST['community_slogan']);
		Config::writeConfigLine('community_location_longitude', $_POST['community_location_longitude']);
		Config::writeConfigLine('community_location_latitude', $_POST['community_location_latitude']);
		Config::writeConfigLine('community_location_zoom', $_POST['community_location_zoom']);

		if(empty($_POST['enable_network_policy']))
			$_POST['enable_network_policy'] = 'false';
		Config::writeConfigLine('enable_network_policy', $_POST['enable_network_policy']);
		Config::writeConfigLine('network_policy_url', $_POST['network_policy_url']);

		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);

		header('Location: ./config.php?section=edit_community');
	} else {
		$message[] = array('Nur Root kann die Daten ändern.', 2);
		Message::setMessage($message);

		header('Location: ./config.php?section=edit_community');
	}
} elseif($_GET['section']=="edit_email") {
	$smarty->assign('mail_sender_adress', Config::getConfigValueByName('mail_sender_adress'));
	$smarty->assign('mail_sender_name', Config::getConfigValueByName('mail_sender_name'));
	$smarty->assign('mail_sending_type', Config::getConfigValueByName('mail_sending_type'));
	$smarty->assign('mail_smtp_server', Config::getConfigValueByName('mail_smtp_server'));
	$smarty->assign('mail_smtp_username', Config::getConfigValueByName('mail_smtp_username'));
	$smarty->assign('mail_smtp_password', Config::getConfigValueByName('mail_smtp_password'));
	$smarty->assign('mail_smtp_login_auth', Config::getConfigValueByName('mail_smtp_login_auth'));
	$smarty->assign('mail_smtp_ssl', Config::getConfigValueByName('mail_smtp_ssl'));

	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("config_email.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif($_GET['section']=="insert_edit_email") {
	if(UserManagement::checkPermission(120)) {
		Config::writeConfigLine('mail_sender_adress', $_POST['mail_sender_adress']);
		Config::writeConfigLine('mail_sender_name', $_POST['mail_sender_name']);
		Config::writeConfigLine('mail_sending_type', $_POST['mail_sending_type']);
		Config::writeConfigLine('mail_smtp_server', $_POST['mail_smtp_server']);
		Config::writeConfigLine('mail_smtp_username', $_POST['mail_smtp_username']);
		Config::writeConfigLine('mail_smtp_password', $_POST['mail_smtp_password']);
		Config::writeConfigLine('mail_smtp_login_auth', $_POST['mail_smtp_login_auth']);
		Config::writeConfigLine('mail_smtp_ssl', $_POST['mail_smtp_ssl']);
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_email');
	} else {
		$message[] = array('Nur Root kann die Daten ändern.', 2);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_email');
	}
} elseif($_GET['section']=="edit_jabber") {
	$smarty->assign('jabber_server', Config::getConfigValueByName('jabber_server'));
	$smarty->assign('jabber_username', Config::getConfigValueByName('jabber_username'));
	$smarty->assign('jabber_password', Config::getConfigValueByName('jabber_password'));

	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("config_jabber.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif($_GET['section']=="insert_edit_jabber") {
	if(UserManagement::checkPermission(120)) {
		Config::writeConfigLine('jabber_server', $_POST['jabber_server']);
		Config::writeConfigLine('jabber_username', $_POST['jabber_username']);
		Config::writeConfigLine('jabber_password', $_POST['jabber_password']);
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_jabber');
	} else {
		$message[] = array('Nur Root kann die Daten ändern.', 2);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_jabber');
	}
} elseif($_GET['section']=="edit_twitter") {
	$smarty->assign('twitter_consumer_key', Config::getConfigValueByName('twitter_consumer_key'));
	$smarty->assign('twitter_consumer_secret', Config::getConfigValueByName('twitter_consumer_secret'));
	$smarty->assign('twitter_username', Config::getConfigValueByName('twitter_username'));

	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("config_twitter.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif($_GET['section']=="insert_edit_twitter_application_data") {
	if(UserManagement::checkPermission(120)) {
		Config::writeConfigLine('twitter_consumer_key', $_POST['twitter_consumer_key']);
		Config::writeConfigLine('twitter_consumer_secret', $_POST['twitter_consumer_secret']);
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_twitter');
	} else {
		$message[] = array('Nur Root kann die Daten ändern.', 2);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_twitter');
	}
} elseif($_GET['section']=="insert_edit_twitter_username") {
	if(UserManagement::checkPermission(120)) {
		Config::writeConfigLine('twitter_username', $_POST['twitter_username']);
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_twitter');
	} else {
		$message[] = array('Nur Root kann die Daten ändern.', 2);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_twitter');
	}
} elseif($_GET['section']=="recieve_twitter_token") {
	$config = array(
		'callbackUrl' => 'http://netmon.freifunk-ol.de/config.php?section=recieve_twitter_token',
		'siteUrl' => 'http://twitter.com/oauth',
		'consumerKey' => $GLOBALS['twitter_consumer_key'],
		'consumerSecret' => $GLOBALS['twitter_consumer_secret']
	);
	$consumer = new Zend_Oauth_Consumer($config);
	
	if (!empty($_GET) && isset($_SESSION['TWITTER_REQUEST_TOKEN'])) {
		$token = $consumer->getAccessToken(
			$_GET,
				unserialize($_SESSION['TWITTER_REQUEST_TOKEN'])
			);
		$_SESSION['TWITTER_ACCESS_TOKEN'] = serialize($token);
		
		// Now that we have an Access Token, we can discard the Request Token
		$_SESSION['TWITTER_REQUEST_TOKEN'] = null;
	} else {
		// Mistaken request? Some malfeasant trying something?
 		exit('Invalid callback request. Oops. Sorry.');
	}

	if(UserManagement::checkPermission(120)) {
		Config::writeConfigLine('twitter_username', $_SESSION['TWITTER_ACCESS_TOKEN']);
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_twitter');
	} else {
		$message[] = array('Nur Root kann die Daten ändern.', 2);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_twitter');
	}
} elseif($_GET['section']=="get_twitter_token") {
	$config = array(
		'callbackUrl' => 'http://netmon.freifunk-ol.de/config.php?section=recieve_twitter_token',
		'siteUrl' => 'http://twitter.com/oauth',
		'consumerKey' => $GLOBALS['twitter_consumer_key'],
		'consumerSecret' => $GLOBALS['twitter_consumer_secret']
	);
	$consumer = new Zend_Oauth_Consumer($config);
	
	// fetch a request token
	$token = $consumer->getRequestToken();
	
	// persist the token to storage
	$_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);
	
	// redirect the user
	$consumer->redirect();
} elseif($_GET['section']=="edit_hardware") {
	$smarty->assign('chipsets_with_name', Chipsets::getChipsetsWithName());
	$smarty->assign('chipsets_without_name', Chipsets::getChipsetsWithoutName());
	
	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("config_hardware.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif($_GET['section']=="edit_hardware_name") {
	$smarty->assign('chipset_data', Chipsets::getChipsetById($_GET['chipset_id']));
	
	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("config_edit_hardware_name.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif($_GET['section']=="insert_edit_chipset_name") {
	Chipsets::editChipset($_GET['chipset_id'], $_POST['hardware_name']);
	header('Location: ./config.php?section=edit_hardware');
} elseif($_GET['section']=="insert_delete_chipset") {
	if($_POST['really_delete']==1) {
		Chipsets::deleteChipset($_GET['chipset_id']);
		header('Location: ./config.php?section=edit_hardware');
	} else {
		$message[] = array("Zum löschen des Chipsets ist eine Bestätigung erforderlich!", 2);
		Message::setMessage($message);
		header('Location: ./config.php?section=edit_hardware_name&chipset_id='.$_GET['chipset_id']);
	}
}

?>
