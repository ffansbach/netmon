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

  //Session starten
  session_start();

  /**
  * KONFIGURATION
  */
  
  //Content type and encoding
  header("Content-Type: text/html; charset=UTF-8");

  require_once('config/config.local.inc.php');
  require_once('config/release.php');

  /**
  * Check if dirs a writable
  */
  $dirs[] = 'templates_c/';
  $dirs[] = 'ccd/';
  $dirs[] = 'config/';
  $dirs[] = 'log/';
  $dirs[] = 'tmp/';

  $everything_is_writable = true;
  foreach($dirs as $dir) {
    if (!is_writable($dir))
      $not_writable_dirs[] = $dir;
  }

  if(!empty($not_writable_dirs)) {
    echo "The following directories has to be writable to run Netmon:<br><br>";
    foreach($not_writable_dirs as $not_writable_dir) {
      echo "$not_writable_dir<br>";
    }
    echo "<br>Please set writable permissions and reload the site.";
    die();
  }

  /**
  * WICHTIGE KLASSEN
  */

  //PDO Class
  require_once('lib/classes/core/db.class.php');
  //Class for Systemnotifications
  require_once('lib/classes/core/message.class.php');
  //Class hat conains many useull functions
  require_once('lib/classes/core/helper.class.php');
  //Usermanagement class
  require_once('lib/classes/core/usermanagement.class.php');  
  //Loging class
  require_once('lib/classes/core/logsystem.class.php');
  //Class to generate the menu
  require_once('lib/classes/core/menus.class.php');
  //Class to edit things
  require_once('lib/classes/core/editinghelper.class.php');

  $UserManagement =  new UserManagement;
  $Menus =  new Menus;

  /**
  * PEAR EZC LIBRARY FOR GRAPHS
  */
  
  require_once "ezc/Base/base.php"; // dependent on installation method, see below
  function __autoload( $className ) {
	ezcBase::autoload( $className );
  }

  /**
  * ZEND FRAMEWORK LIBRARYS
  */

  // load the zend Framework
  //http://blog.bananas-playground.net/archives/66-ZendFramework-Laden.html
  define("LIB_DIR","lib/classes/extern/");
  $include_path = get_include_path();
  
  if(!ini_set('include_path',$include_path.PATH_SEPARATOR.LIB_DIR)) {
    die('Failed to set the include path. Check you php configuration.');
  }

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
  $smarty->template_dir = "templates/html";
  //Compilefolder
  $smarty->compile_dir = 'templates_c';

	/**
	* Auto Login
	*/

	if (!UserManagement::isLoggedIn($_SESSION['user_id']) AND !$GLOBALS['installation_mode']) {
		//Login Class
		require_once('lib/classes/core/login.class.php');
		if(!empty($_COOKIE["nickname"]) AND !empty($_COOKIE["password_hash"])) {
			Login::user_login($_COOKIE["nickname"], $_COOKIE["password_hash"], false, true);
		} elseif (!empty($_COOKIE["openid"])) {
			require_once('lib/classes/extern/class.openid.php');
			if (empty($_SESSION['redirect_url'])) {
				$_SESSION['redirect_url'] = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI'];

// = substr($_SERVER['REQUEST_URI'],1,strlen($_SERVER['REQUEST_URI']));
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

	/**
	* Menus
	*/

  $smarty->assign('top_menu', $Menus->topMenu());
  $smarty->assign('loginOutMenu', $Menus->loginOutMenu());
  $smarty->assign('installation_menu', $Menus->installationMenu());
  $smarty->assign('normal_menu', $Menus->normalMenu());
  $smarty->assign('user_menu', $Menus->userMenu());
  $smarty->assign('admin_menu', $Menus->adminMenu());

  //Give often used variables to smarty
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('zeit', date("d.m.Y H:i:s", time())." Uhr");
?>