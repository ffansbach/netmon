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
	
	define('ROOT_DIR', dirname(__FILE__));
	
	//Define permission constants
	define('PERM_ROOT', pow(2,6));
	define('PERM_ADMIN', pow(2,5));
	define('PERM_MOD', pow(2,4));
	define('PERM_USER', pow(2,3));
	define('PERM_LOGGEDIN', pow(2,2));
	define('PERM_NOTLOGGEDIN', pow(2,1));
	
	//Set default values (only used if no values can be found in database)
	$GLOBALS['installed'] = false;
	$GLOBALS['template'] = "freifunk_oldenburg";
	$GLOBALS['monitor_root'] = __DIR__."/";
	set_include_path(get_include_path() .PATH_SEPARATOR. __DIR__."/lib/extern/");
	$GLOBALS['community_name'] = "Freifunk Deinestadt";
	$GLOBALS['community_slogan'] = "Die freie WLAN-Community aus deiner Stadt • Freie Netze für alle!";
		
	if (!$crawler) {
		//check if netmons root path and the config path is writable to created temp dirs and config file
		$check_writable[] = '/';
		$check_writable[] = '/config/';
		foreach($check_writable as $path) {
			if (!is_writable(ROOT_DIR.$path)) {
				echo ROOT_DIR.$path."<br>";
				$not_writable[] = $path;
			}
		}
		if(!empty($not_writable)) {
			echo "Please set writable permissions to the above files and directories and reload the site.";
			die();
		}
		unset($check_writable);
		
		//copy exemple config files and create needed directories on fresh installation
		if(!file_exists(ROOT_DIR.'/config/config.local.inc.php')) copy(ROOT_DIR.'/config/config.local.inc.php.example', ROOT_DIR.'/config/config.local.inc.php');	
		
		//create temp directories
		$create_dirs[] = '/templates_c/';
		$create_dirs[] = '/tmp/';
		$create_dirs[] = '/rrdtool/';
		$create_dirs[] = '/rrdtool/databases/';
		foreach($create_dirs as $dir) {
			if(!file_exists(ROOT_DIR.$dir))
				@mkdir(ROOT_DIR.$dir);
		}

		//check if directories and files are writable
		$check_writable = $create_dirs;
		$check_writable[] = '/';
		$check_writable[] = '/config/config.local.inc.php';
		foreach($check_writable as $path) {
			if (!is_writable(ROOT_DIR.$path)) {
				echo ROOT_DIR.$path."<br>";
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
		header("access-control-allow-origin: *");
	}
	date_default_timezone_set('Europe/Berlin');
	
	//include important configuration files
	require_once(ROOT_DIR.'/config/config.local.inc.php');
	require_once(ROOT_DIR.'/config/release.php');
	
	//load Zend framework
	require_once(ROOT_DIR.'/lib/extern/Zend/Loader/Autoloader.php');
	Zend_Loader_Autoloader::getInstance();
	
	//include important classes
	require_once(ROOT_DIR.'/lib/core/db.class.php');
	require_once(ROOT_DIR.'/lib/core/config.class.php');
	require_once(ROOT_DIR.'/lib/core/message.class.php');
	require_once(ROOT_DIR.'/lib/core/helper.class.php');
	require_once(ROOT_DIR.'/lib/core/permission.class.php');  
	require_once(ROOT_DIR.'/lib/core/menus.class.php');
	
	if ($GLOBALS['installed']) {
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
	require_once (ROOT_DIR.'/lib/extern/smarty/Smarty.class.php');
	$smarty = new Smarty;
	$smarty->compile_check = true;
	$smarty->debugging = false;

	// base template directory
	// this is used as a fallback if nothing is found in the custom template folder
	// lookup ordered by param2
	$smarty->addTemplateDir(ROOT_DIR.'/templates/base/html', 10);
	// custom template folder - smarty will try here first ( order 0 )
	$smarty->addTemplateDir(ROOT_DIR.'/templates/'.$GLOBALS['template'].'/html', 0);

	$smarty->compile_dir = ROOT_DIR.'/templates_c';
	$smarty->assign('template', $GLOBALS['template']);
	$smarty->assign('installed', $GLOBALS['installed']);
	$smarty->assign('community_name', $GLOBALS['community_name']);
	$smarty->assign('community_slogan', $GLOBALS['community_slogan']);

	/**
	* Auto Login
	*/
	if ($GLOBALS['installed']) {
		//if the user is not logged in and the remember me cookie is set
		if (!isset($_SESSION['user_id']) AND !empty($_COOKIE["remember_me"])) {
			require_once(ROOT_DIR.'/lib/core/user_old.class.php');
			require_once(ROOT_DIR.'/lib/core/UserRememberMeList.class.php');
			require_once(ROOT_DIR.'/lib/extern/phpass/PasswordHash.php');
			
			//get user_id and password from remember_me cookie
			$remember_me_cookie = explode(",", $_COOKIE["remember_me"]);
			$user_id = $remember_me_cookie[0];
			$password = $remember_me_cookie[1];
			
			//check if the user exists
			$user_data = User_old::getUserById($user_id);
			if(!empty($user_data)) {
				//get the remember_mes of the user from the database
				$user_remember_me_list = new UserRememberMeList($user_id, "create_date", "desc");
				$user_remember_me_list = $user_remember_me_list->getUserRememberMeList();
				//check if any remember me matches the password stored in the cookie
				$phpass = new PasswordHash(8, false);
				foreach($user_remember_me_list as $user_remember_me) {
					if($phpass->CheckPassword($password, $user_remember_me->getPassword())) {
						//if a remember me matches, then login and set a new random password on the remember me
						//store the session-id to the database
						$stmt = DB::getInstance()->prepare("UPDATE users SET session_id = ? WHERE id = ?");
						$stmt->execute(array(session_id(), $user_data['id']));
						//store the 
						$_SESSION['user_id'] = $user_data['id'];
						
						//generate long random password
						$random_password = Helper::randomPassword(56);
						//hash the random password like a normal password
						$random_password_hash = $phpass->HashPassword($random_password);
						
						$user_remember_me->setPassword($random_password_hash);
						$user_remember_me->store();
						setcookie("remember_me", $user_id.",".$random_password, time() + 60*60*24*14);
						
						$messages[] = array("Herzlich willkommen zurück ".$user_data['nickname'].".", 1);
						Message::setMessage($messages);
						break;
					}
				}
			}
		}
	}

	//load menus
	if ($GLOBALS['installed']) {
		$smarty->assign('top_menu', Menus::topMenu());
		$smarty->assign('loginOutMenu', Menus::loginOutMenu());
		$smarty->assign('normal_menu', Menus::normalMenu());
		$smarty->assign('object_menu', Menus::objectMenu());
		$smarty->assign('admin_menu', Menus::adminMenu());
		$smarty->assign('root_menu', Menus::rootMenu());
		$smarty->assign('menu_community_homepage', ConfigLine::configByName('community_homepage'));
		$smarty->assign('menu_community_homepage_name', ConfigLine::configByName('community_homepage_name'));

	} else {
		$smarty->assign('installation_menu', Menus::installationMenu());
 	}

	//Give often used variables to smarty
	$smarty->assign('zeit', date("d.m.Y H:i:s", time())." Uhr");

	//This is used for redirection after login
	$current_page = "";
	if(isset($_SERVER['REQUEST_URI']) AND isset($_SERVER['SERVER_NAME']))
		$current_page = Helper::curPageURL();
	//only take actual site if its not an api site, see http://ticket.freifunk-ol.de/issues/466
	if(strpos($current_page, "api") === false) {
		$_SESSION['last_page'] = "";
		if(isset($_SESSION['current_page']))
			$_SESSION['last_page'] = $_SESSION['current_page'];

		$_SESSION['current_page'] = "";
		if(isset($_SERVER['REQUEST_URI']))
			$_SESSION['current_page'] = $current_page;
	}

	if(!isset($messages)) {
		$message = array();
		$smarty->assign('message', $message);
	} else {
		$smarty->assign('message', $messages);
	}

	//initialize important variables
	if(!isset($_GET['section'])) $_GET['section'] = null;
	if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = null;

?>
