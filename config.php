<?php

require_once('runtime.php');
require_once('lib/core/install.class.php');
require_once('lib/core/Chipsetlist.class.php');
require_once('lib/core/Chipset.class.php');
require_once('lib/core/DnsZone.class.php');
require_once('lib/core/DnsZoneList.class.php');
require_once('lib/core/Network.class.php');
require_once('lib/core/Networklist.class.php');
require_once('lib/core/ConfigLine.class.php');
require_once('lib/extern/Zend/Oauth/Consumer.php');


$config_path = "./config/config.local.inc.php";

if(Permission::checkPermission(PERM_ROOT)) {
	if ($_GET['section']=="edit") {
		//INSTALLATION-LOCK
		$smarty->assign('installed', $GLOBALS['installed']);
		
		//WEBSERVER
		$smarty->assign('url_to_netmon', ConfigLine::configByName('url_to_netmon'));
		
		//MYSQL
		$smarty->assign('mysql_host', $GLOBALS['mysql_host']);
		$smarty->assign('mysql_db', $GLOBALS['mysql_db']);
		$smarty->assign('mysql_user', $GLOBALS['mysql_user']);
		$smarty->assign('mysql_password', $GLOBALS['mysql_password']);
		
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("config.tpl.html");
		$smarty->display("footer.tpl.html");
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
		$smarty->assign('url_to_netmon', ConfigLine::configByName('url_to_netmon'));
		$smarty->assign('google_maps_api_key', ConfigLine::configByName('google_maps_api_key'));
		$smarty->assign('template_name', ConfigLine::configByName('template'));
		$smarty->assign('template', ConfigLine::configByName('template'));
		$smarty->assign('hours_to_keep_mysql_crawl_data', ConfigLine::configByName('hours_to_keep_mysql_crawl_data'));
		$smarty->assign('hours_to_keep_history_table', ConfigLine::configByName('hours_to_keep_history_table'));
		$smarty->assign('crawl_cycle_length_in_minutes', ConfigLine::configByName('crawl_cycle_length_in_minutes'));
		$smarty->assign('crawl_interfaces', ConfigLine::configByName('crawl_interfaces'));
		$smarty->assign('crawl_range', ConfigLine::configByName('crawl_range'));
		$smarty->assign('router_status_interface_whitelist', ConfigLine::configByName('router_status_interface_whitelist'));
	
		
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("config_netmon.tpl.html");
		$smarty->display("footer.tpl.html");
	} elseif($_GET['section']=="insert_edit_netmon") {
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
		Config::writeConfigLine('crawl_interfaces', $_POST['crawl_interfaces']);
		Config::writeConfigLine('crawl_range', $_POST['crawl_range']);
		Config::writeConfigLine('router_status_interface_whitelist', $_POST['router_status_interface_whitelist']);
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_netmon');
	} elseif($_GET['section']=="edit_community") {
		$smarty->assign('google_maps_api_key', ConfigLine::configByName('google_maps_api_key'));
		$smarty->assign('community_name', ConfigLine::configByName('community_name'));
		$smarty->assign('community_slogan', ConfigLine::configByName('community_slogan'));
		$smarty->assign('community_location_longitude', ConfigLine::configByName('community_location_longitude'));
		$smarty->assign('community_location_latitude', ConfigLine::configByName('community_location_latitude'));
		$smarty->assign('community_location_zoom', ConfigLine::configByName('community_location_zoom'));
		$smarty->assign('enable_network_policy', ConfigLine::configByName('enable_network_policy'));
		$smarty->assign('network_policy_url', ConfigLine::configByName('network_policy_url'));
		$smarty->assign('community_essid', ConfigLine::configByName('community_essid'));
		
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("config_community.tpl.html");
		$smarty->display("footer.tpl.html");
	} elseif($_GET['section']=="insert_edit_community") {
		Config::writeConfigLine('community_name', $_POST['community_name']);
		Config::writeConfigLine('community_slogan', $_POST['community_slogan']);
		Config::writeConfigLine('community_location_longitude', $_POST['community_location_longitude']);
		Config::writeConfigLine('community_location_latitude', $_POST['community_location_latitude']);
		Config::writeConfigLine('community_location_zoom', $_POST['community_location_zoom']);
		Config::writeConfigLine('community_essid', $_POST['community_essid']);
		
		if(empty($_POST['enable_network_policy']))
			$_POST['enable_network_policy'] = 'false';
		Config::writeConfigLine('enable_network_policy', $_POST['enable_network_policy']);
		Config::writeConfigLine('network_policy_url', $_POST['network_policy_url']);

		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);

		header('Location: ./config.php?section=edit_community');
	} elseif($_GET['section']=="edit_network_connection") {
		$smarty->assign('network_connection_ipv4', ConfigLine::configByName('network_connection_ipv4'));
		$smarty->assign('network_connection_ipv6', ConfigLine::configByName('network_connection_ipv6'));
		$smarty->assign('network_connection_ipv6_interface', ConfigLine::configByName('network_connection_ipv6_interface'));
		
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("config_network_connection.tpl.html");
		$smarty->display("footer.tpl.html");
	} elseif($_GET['section']=="insert_edit_network_connection") {
		if(empty($_POST['network_connection_ipv4']))
			Config::writeConfigLine('network_connection_ipv4', 'false');
		else
			Config::writeConfigLine('network_connection_ipv4', $_POST['network_connection_ipv4']);
		
		if(empty($_POST['network_connection_ipv6']))
			Config::writeConfigLine('network_connection_ipv6', 'false');
		else
			Config::writeConfigLine('network_connection_ipv6', $_POST['network_connection_ipv6']);
		
		Config::writeConfigLine('network_connection_ipv6_interface', $_POST['network_connection_ipv6_interface']);
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_network_connection');
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
		$smarty->display("header.tpl.html");
		$smarty->display("config_email.tpl.html");
		$smarty->display("footer.tpl.html");
	} elseif($_GET['section']=="insert_edit_email") {
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
	} elseif($_GET['section']=="edit_jabber") {
		$smarty->assign('jabber_server', Config::getConfigValueByName('jabber_server'));
		$smarty->assign('jabber_username', Config::getConfigValueByName('jabber_username'));
		$smarty->assign('jabber_password', Config::getConfigValueByName('jabber_password'));

		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("config_jabber.tpl.html");
		$smarty->display("footer.tpl.html");
	} elseif($_GET['section']=="insert_edit_jabber") {
		Config::writeConfigLine('jabber_server', $_POST['jabber_server']);
		Config::writeConfigLine('jabber_username', $_POST['jabber_username']);
		Config::writeConfigLine('jabber_password', $_POST['jabber_password']);
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_jabber');
	} elseif($_GET['section']=="edit_twitter") {
		$smarty->assign('twitter_token', Config::getConfigValueByName('twitter_token'));

		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("config_twitter.tpl.html");
		$smarty->display("footer.tpl.html");
	} elseif($_GET['section']=="delete_twitter_token") {
		Config::writeConfigLine('twitter_token', "");
		
		$message[] = array('Die Daten wurden gespeichert.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_twitter');
	} elseif($_GET['section']=="recieve_twitter_token") {
		$config = array(
			'callbackUrl' => Config::getConfigValueByName('url_to_netmon').'/config.php?section=recieve_twitter_token',
			'siteUrl' => 'https://api.twitter.com/oauth',
			'consumerKey' => ConfigLine::configByName('twitter_consumer_key'),
			'consumerSecret' => ConfigLine::configByName('twitter_consumer_secret')
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
		
		Config::writeConfigLine('twitter_username', $token->getParam( 'screen_name' ));
		Config::writeConfigLine('twitter_token', $_SESSION['TWITTER_ACCESS_TOKEN']);
		
		$message[] = array('Netmon wurde an den Twitteraccount <a href="https://twitter.com/'.$token->getParam( 'screen_name' ).'">'.$token->getParam( 'screen_name' ).'</a> angebunden.', 1);
		Message::setMessage($message);
		
		header('Location: ./config.php?section=edit_twitter');
	} elseif($_GET['section']=="get_twitter_token") {
		$config = array(
			'callbackUrl' => Config::getConfigValueByName('url_to_netmon').'/config.php?section=recieve_twitter_token',
			'siteUrl' => 'https://api.twitter.com/oauth',
			'consumerKey' => ConfigLine::configByName('twitter_consumer_key'),
			'consumerSecret' => ConfigLine::configByName('twitter_consumer_secret')
		);
		$consumer = new Zend_Oauth_Consumer($config);
		
		// fetch a request token
		$token = $consumer->getRequestToken();
		
		// persist the token to storage
		$_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);
		
		// redirect the user
		$consumer->redirect();
	} elseif($_GET['section']=="edit_hardware") {
		$chipsetlist = new Chipsetlist();
		$smarty->assign('chipsetlist', $chipsetlist->getList());
		
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("config_hardware.tpl.html");
		$smarty->display("footer.tpl.html");
	} elseif($_GET['section']=="add_hardware") {
		$chipset = new Chipset(false, $_POST['name'], $_POST['hardware_name']);
		if($chipset->store()) {
			$message[] = array("Der Chipsatz wurde gespeichert.", 1);
		} else {
			$message[] = array("Der Chipsatz konnte nicht gespeichert werden.", 2);
		}
		Message::setMessage($message);
		header('Location: ./config.php?section=edit_hardware');
	} elseif($_GET['section']=="edit_hardware_name") {
		$chipset = new Chipset((int)$_GET['chipset_id']);
		$chipset->fetch();
		$smarty->assign('chipset', $chipset);
		
		
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("config_edit_hardware_name.tpl.html");
		$smarty->display("footer.tpl.html");
	} elseif($_GET['section']=="insert_edit_chipset_name") {
		$chipset = new Chipset((int)$_GET['chipset_id'], $_POST['name'], $_POST['hardware_name']);
		if($chipset->store()) {
			$message[] = array("Der Chipsatz wurde gespeichert.", 1);
		} else {
			$message[] = array("Der Chipsatz konnte nicht gespeichert werden.", 2);
		}
		Message::setMessage($message);
		header('Location: ./config.php?section=edit_hardware');
	} elseif($_GET['section']=="insert_delete_chipset") {
		if($_POST['really_delete']==1) {
			$message[] = array("Diese Funktion ist aktuell nicht implementiert", 0);
			Message::setMessage($message);
			header('Location: ./config.php?section=edit_hardware');
		} else {
			$message[] = array("Zum löschen des Chipsets ist eine Bestätigung erforderlich!", 2);
			Message::setMessage($message);
			header('Location: ./config.php?section=edit_hardware_name&chipset_id='.$_GET['chipset_id']);
		}
	}
} else {
	Permission::denyAccess(PERM_ROOT);
}

?>