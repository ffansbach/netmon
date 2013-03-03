<?php
	// +---------------------------------------------------------------------------+
	// index.php
	// Netmon, Freifunk Netzverwaltung und Monitoring Software
	//
	// Copyright (c) 2009 Clemens John <clemens-john@gmx.de>
	// +---------------------------------------------------------------------------+
	// This program is free software; you can redistribute it and/or
	// modify it under the terms of the GNU General Public License
	// as published by the Free Software Foundation; either version 3
	// of the License, or any later version.
	// +---------------------------------------------------------------------------+
	// This program is distributed in the hope that it will be useful,
	// but WITHOUT ANY WARRANTY; without even the implied warranty of
	// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	// GNU General Public License for more details.
	// You should have received a copy of the GNU General Public License
	// along with this program; if not, write to the Free Software
	// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
	// +---------------------------------------------------------------------------+/

	/**
	* This file is the runtime configuration of the project
	*
	* @author	Clemens John <clemens-john@gmx.de>
	* @version	0.1
	* @package	Netmon Freifunk Netzverwaltung und Monitoring Software
	*/

	//Set default values
	$GLOBALS['installed'] = false;
	$GLOBALS['template'] = "freifunk_oldenburg";
	$GLOBALS['monitor_root'] = __DIR__."/";
	set_include_path(get_include_path() .PATH_SEPARATOR. __DIR__."/lib/classes/extern/");
	$GLOBALS['community_name'] = "Freifunk Deinestadt";
	$GLOBALS['community_slogan'] = "Die freie WLAN-Community aus deiner Stadt • Freie Netze für alle!";
		
	//check if netmons root path and the config path is writable to created temp dirs and config file
	$check_writable[] = '';
	$check_writable[] = 'config/';
	foreach($check_writable as $path) {
		if (!is_writable($GLOBALS['monitor_root'].$path)) {
			echo $GLOBALS['monitor_root'].$path."<br>";
			$not_writable[] = $path;
		}
	}
	if(!empty($not_writable)) {
		echo "Please set writable permissions to the above files and directories and reload the site.";
		die();
	}
	unset($check_writable);
	
	//copy exemple config files and create needed directories on fresh installation
	if(!file_exists($GLOBALS['monitor_root'].'config/config.local.inc.php')) copy($GLOBALS['monitor_root'].'config/config.local.inc.php.example', $GLOBALS['monitor_root'].'config/config.local.inc.php');	
	
	//create temp directories
	$create_dirs[] = 'templates_c/';
	$create_dirs[] = 'tmp/';
	$create_dirs[] = 'rrdtool/';
	$create_dirs[] = 'rrdtool/databases/';
	foreach($create_dirs as $dir) {
		if(!file_exists($GLOBALS['monitor_root'].$dir))
			@mkdir($GLOBALS['monitor_root'].$dir);
	}

	//check if directories and files are writable
	$check_writable = $create_dirs;
	$check_writable[] = '';
	$check_writable[] = 'config/config.local.inc.php';
	foreach($check_writable as $path) {
		if (!is_writable($GLOBALS['monitor_root'].$path)) {
			echo $GLOBALS['monitor_root'].$path."<br>";
			$not_writable[] = $path;
		}
	}

	if(!empty($not_writable)) {
		echo "Please set writable permissions to the above files and directories and reload the site.";
		die();
	}

	//start php session and set content type and timezone
	session_start();
	header("Content-Type: text/html; charset=UTF-8");
	date_default_timezone_set('Europe/Berlin');
	
	//include important configuration files
	require_once('config/config.local.inc.php');
	require_once('config/release.php');

	//include important classes
	require_once('lib/classes/core/db.class.php');
	require_once('lib/classes/core/config.class.php');
	require_once('lib/classes/core/message.class.php');
	require_once('lib/classes/core/helper.class.php');
	require_once('lib/classes/core/usermanagement.class.php');  
	require_once('lib/classes/core/menus.class.php');

	//load Zend framework
	require_once 'lib/classes/extern/Zend/Loader/Autoloader.php';
	Zend_Loader_Autoloader::getInstance();

	if ($GLOBALS['installed']) {	
		//JABBER
		$GLOBALS['jabber_server'] = Config::getConfigValueByName('jabber_server');
		$GLOBALS['jabber_username'] = Config::getConfigValueByName('jabber_username');
		$GLOBALS['jabber_password'] = Config::getConfigValueByName('jabber_password');
		
		//TWITTER
		$GLOBALS['twitter_consumer_key'] = Config::getConfigValueByName('twitter_consumer_key');
		$GLOBALS['twitter_consumer_secret'] = Config::getConfigValueByName('twitter_consumer_secret');
		$GLOBALS['twitter_username'] = Config::getConfigValueByName('twitter_username');
		
		//MAIL
		$GLOBALS['mail_sending_type'] = Config::getConfigValueByName('mail_sending_type');
		$GLOBALS['mail_sender_adress'] = Config::getConfigValueByName('mail_sender_adress');
		$GLOBALS['mail_sender_name'] = Config::getConfigValueByName('mail_sender_name');
		$GLOBALS['mail_smtp_server'] = Config::getConfigValueByName('mail_smtp_server');
		$GLOBALS['mail_smtp_username'] = Config::getConfigValueByName('mail_smtp_username');
		$GLOBALS['mail_smtp_password'] = Config::getConfigValueByName('mail_smtp_password');
		$GLOBALS['mail_smtp_login_auth'] = Config::getConfigValueByName('mail_smtp_login_auth');
		$GLOBALS['mail_smtp_ssl'] = Config::getConfigValueByName('mail_smtp_ssl');
		
		//NETWORK
		$GLOBALS['community_name'] = Config::getConfigValueByName('community_name');
		$GLOBALS['community_slogan'] = Config::getConfigValueByName('community_slogan');
		$GLOBALS['enable_network_policy'] = Config::getConfigValueByName('enable_network_policy');
		$GLOBALS['network_policy_url'] = Config::getConfigValueByName('network_policy_url');
		
		//PROJEKT
		$GLOBALS['hours_to_keep_mysql_crawl_data'] = Config::getConfigValueByName('hours_to_keep_mysql_crawl_data');
		$GLOBALS['hours_to_keep_history_table'] = Config::getConfigValueByName('hours_to_keep_history_table');
		
		//GOOGLEMAPSAPIKEY
		$GLOBALS['google_maps_api_key'] = Config::getConfigValueByName('google_maps_api_key');
		//CRAWLER
		$GLOBALS['crawl_cycle'] = Config::getConfigValueByName('crawl_cycle_length_in_minutes');
		
		//TEMPLATE
		$GLOBALS['template'] = Config::getConfigValueByName('template');
		
		//WEBSERVER
		$GLOBALS['url_to_netmon'] = Config::getConfigValueByName('url_to_netmon');
	}

	//load smarty template engine
	require_once ('lib/classes/extern/smarty/Smarty.class.php');
	$smarty = new Smarty;
	$smarty->compile_check = true;
	$smarty->debugging = false;
	$smarty->template_dir = "templates/$GLOBALS[template]/html";
	$smarty->compile_dir = 'templates_c';
	$smarty->assign('template', $GLOBALS['template']);
	$smarty->assign('installed', $GLOBALS['installed']);
	$smarty->assign('community_name', $GLOBALS['community_name']);
	$smarty->assign('community_slogan', $GLOBALS['community_slogan']);

	/**
	* Auto Login
	*/
	if ($GLOBALS['installed']) {
		if (!isset($_SESSION['user_id']) OR !UserManagement::isLoggedIn($_SESSION['user_id'])) {
			//Login Class
			require_once('lib/classes/core/login.class.php');
			if(!empty($_COOKIE["nickname"]) AND !empty($_COOKIE["password_hash"])) {
				Login::user_login($_COOKIE["nickname"], $_COOKIE["password_hash"], false, true);
			} elseif (!empty($_COOKIE["openid"])) {
				require_once('lib/classes/extern/class.openid.php');
				if (empty($_SESSION['redirect_url'])) {
					$_SESSION['redirect_url'] = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI'];
				}
			
				// Get identity from user and redirect browser to OpenID Server
				$openid = new SimpleOpenID;
				$openid->SetIdentity($_COOKIE["openid"]);
				$openid->SetTrustRoot('http://' . $_SERVER["HTTP_HOST"]);
				$openid->SetRequiredFields(array('email','fullname'));
				$openid->SetOptionalFields(array('dob','gender','postcode','country','language','timezone'));
				if ($openid->GetOpenIDServer()){
					$openid->SetApprovedURL('http://'.$_SERVER["HTTP_HOST"].dirname($_SERVER['PHP_SELF']).'/login.php?section=openid_login_send');  	// Send Response from OpenID server to this script
					$openid->Redirect(); 	// This will redirect user to OpenID Server
				} else {
					$error = $openid->GetError();
					$messages[] = array("Autologin mit der OpenID ".$_COOKIE["openid"]." fehlgeschlagen.", 2);
					$messages[] = array("ERROR CODE: " . $error['code'], 2);
					$messages[] = array("ERROR DESCRIPTION: " . $error['description'], 2);
					setcookie("openid", "", time() - 60*60*24*14);
					$messages[] = array("Deaktiviere Autologin.", 2);
					Message::setMessage($messages);
				}
			}
		}
	}

	//load menus
	if ($GLOBALS['installed']) {
		$smarty->assign('loginOutMenu', Menus::loginOutMenu());
		$smarty->assign('normal_menu', Menus::normalMenu());
		$smarty->assign('admin_menu', Menus::adminMenu());
		$smarty->assign('root_menu', Menus::rootMenu());

	} else {
		$smarty->assign('installation_menu', Menus::installationMenu());
 	}

	//Give often used variables to smarty
	$smarty->assign('zeit', date("d.m.Y H:i:s", time())." Uhr");

	//This is used for redirection after login
	if(isset($_SERVER['REQUEST_URI']) AND isset($_SERVER['SERVER_NAME']))
		$current_page = Helper::curPageURL();
	//only take actual site if its not an ai site, see http://ticket.freifunk-ol.de/issues/466
	if(strpos($current_page, "api") === false) {
		$_SESSION['last_page'] = "";
		if(isset($_SESSION['current_page']))
			$_SESSION['last_page'] = $_SESSION['current_page'];

		$_SESSION['current_page'] = "";
		if(isset($_SERVER['REQUEST_URI']))
			$_SESSION['current_page'] = $current_page;
	}

/*	if(!isset($messages)) {
		$message = array();
		$smarty->assign('message', $message);
	} else {
		$smarty->assign('message', $messages);
	}*/

	//initialize important variables
	if(!isset($_GET['section'])) $_GET['section'] = null;
	if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = null;

?>