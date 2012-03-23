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

	/**
	* Do not display PHP errors on a real installation
	*/

	if (ini_get('display_errors')) {
	  ini_set('display_errors', 1);
	}

	/**
	* SET INCLUDE PATH AND MONITOR_ROOT
	*/


	$include_path = __DIR__."/lib/classes/extern/";
	set_include_path(get_include_path() .PATH_SEPARATOR. $include_path);

	if(empty($GLOBALS['monitor_root'])) {
	      $GLOBALS['monitor_root'] = __DIR__."/";
	}

	/**
	* Start PHP Session
	*/

	session_start();

	/**
	* KONFIGURATION
	*/
	
	//Content type and encoding
	header("Content-Type: text/html; charset=UTF-8");
	date_default_timezone_set('Europe/Berlin');

	require_once('config/config.local.inc.php');
	require_once('config/menus.local.inc.php');
	require_once('config/release.php');

	//PDO Class
	require_once('lib/classes/core/db.class.php');

	//Config Class
	require_once('lib/classes/core/config.class.php');

	/**
	* Fetch configuration from database
	*/
	$GLOBALS['installed'] = Config::getConfigValueByName('installed');

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

	/**
	* Check if dirs a writable
	*/
	
	$GLOBALS['netmon_root_path'] = __DIR__."/";

	$dirs[] = $GLOBALS['netmon_root_path'].'templates_c/';
	$dirs[] = $GLOBALS['netmon_root_path'].'ccd/';
	$dirs[] = $GLOBALS['netmon_root_path'].'config/';
	$dirs[] = $GLOBALS['netmon_root_path'].'config/config.local.inc.php';
	$dirs[] = $GLOBALS['netmon_root_path'].'tmp/';
	$dirs[] = $GLOBALS['netmon_root_path'].'scripts/imagemaker/images/';
	$dirs[] = $GLOBALS['netmon_root_path'].'scripts/imagemaker/configurations/';
	$dirs[] = $GLOBALS['netmon_root_path'].'rrdtool/';
	$dirs[] = $GLOBALS['netmon_root_path'].'rrdtool/databases/';

	$everything_is_writable = true;
	foreach($dirs as $dir) {
	  if (!is_writable($dir))
	    $not_writable_dirs[] = $dir;
	}



	if(!empty($not_writable_dirs)) {
	  echo "The following files or directories have to be writable to run Netmon:<br><br>";
	  foreach($not_writable_dirs as $not_writable_dir) {
	    echo "$not_writable_dir<br>";
	  }
	  echo "<br>Please set writable permissions and reload the site.";
	  die();
	}

	/**
	* WICHTIGE KLASSEN
	*/

	//Class for Systemnotifications
	require_once('lib/classes/core/message.class.php');
	//Class hat conains many useull functions
	require_once('lib/classes/core/helper.class.php');
	//Usermanagement class
	require_once('lib/classes/core/usermanagement.class.php');  
	//Class to generate the menu
	require_once('lib/classes/core/menus.class.php');

	$UserManagement =  new UserManagement;

	/**
	* LOAD ZEND FRAMEWORK
	*/

	require_once 'lib/classes/extern/Zend/Loader/Autoloader.php';
	Zend_Loader_Autoloader::getInstance();

	new Zend_Mail_Transport_Smtp();
	new Zend_Mail();

	/**
	* Default Setting
	*/
	if(!isset($GLOBALS['installation_mode']))
	  $GLOBALS['installation_mode'] = false;

	/**
	* SMARTY TEMPLATEENGINE
	*/

	//Smarty main class
	require_once ('lib/classes/extern/smarty/Smarty.class.php');

	//Initialise Smarty
	$smarty = new Smarty;
	$smarty->compile_check = true;
	//Set debugging site off
	$smarty->debugging = false;
	//Templatefolder
	$smarty->template_dir = "templates/$GLOBALS[template]/html";
	//Compilefolder
	$smarty->compile_dir = 'templates_c';

	/**
	* Auto Login
	*/

	if (!$GLOBALS['installation_mode']) {
		if (isset($_SESSION['user_id']) AND !UserManagement::isLoggedIn($_SESSION['user_id'])) {
			//Login Class
			require_once('lib/classes/core/login.class.php');
			if(!empty($_COOKIE["nickname"]) AND !empty($_COOKIE["password_hash"])) {
				Login::user_login($_COOKIE["nickname"], $_COOKIE["password_hash"], false, true);
			} elseif (!empty($_COOKIE["openid"]) AND !$_SESSION['openid_login']) {
				$_SESSION['openid_login'] = true;
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
				}
			}
		}
	}

	/**
	* Menus
	*/

	if (!$GLOBALS['installation_mode']) {
		$smarty->assign('top_menu', Menus::topMenu());
		$smarty->assign('loginOutMenu', Menus::loginOutMenu());
		$smarty->assign('normal_menu', Menus::normalMenu());
		$smarty->assign('user_menu', Menus::userMenu());
		$smarty->assign('admin_menu', Menus::adminMenu());
		$smarty->assign('root_menu', Menus::rootMenu());

	} else {
		$smarty->assign('installation_menu', Menus::installationMenu());
 	}

	//Give often used variables to smarty
	$smarty->assign('zeit', date("d.m.Y H:i:s", time())." Uhr");

	require_once('lib/classes/core/crawling.class.php');

	$actual_crawl_cycle = Crawling::getActualCrawlCycle();
	$actual_crawl_cycle['crawl_date_end'] = strtotime($actual_crawl_cycle['crawl_date'])+$GLOBALS['crawl_cycle']*60;
	$actual_crawl_cycle['crawl_date_end_minutes'] = floor(($actual_crawl_cycle['crawl_date_end']-time())/60).':'.(($actual_crawl_cycle['crawl_date_end']-time()) % 60);
	$last_ended_crawl_cycle = Crawling::getLastEndedCrawlCycle();
	$smarty->assign('last_ended_crawl_cycle', $last_ended_crawl_cycle);
	$smarty->assign('actual_crawl_cycle', $actual_crawl_cycle);

	//This is used for redirection after login
	$_SESSION['last_page'] = "";
	if(isset($_SESSION['current_page']))
		$_SESSION['last_page'] = $_SESSION['current_page'];

	$_SESSION['current_page'] = "";
	if(isset($_SERVER['REQUEST_URI']))
		$_SESSION['current_page'] = $_SERVER['REQUEST_URI'];

	/**Google Maps API Key*/
	$smarty->assign('google_maps_api_key', $GLOBALS['google_maps_api_key']);

	/**Template*/
	$smarty->assign('template', $GLOBALS['template']);

	/**Globals*/
	$smarty->assign('globals', $GLOBALS);

	$smarty->assign('installation_mode', $GLOBALS['installation_mode']);

	if(!isset($messages)) {
		$message = array();
		$smarty->assign('message', $message);
	}

?>