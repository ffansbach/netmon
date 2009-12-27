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


  //Session starten
  session_start();

  /**
  * KONFIGURATION
  */
  
  //Typ und Encoding Festlegen
  header("Content-Type: text/html; charset=UTF-8");

  require_once($path.'config/config.local.inc.php');

  /**
  * WICHTIGE KLASSEN
  */

  //PDO Class
  require_once($path.'lib/classes/core/db.class.php');
  //Klasse für Nachrichten einbinden
  require_once($path.'lib/classes/core/message.class.php');
  //Allgemeine Helper-Klasse
  require_once($path.'lib/classes/core/helper.class.php');
  //Klasse für UserManagement
  require_once($path.'lib/classes/core/usermanagement.class.php');  
  //Klasse fürs Logging
  require_once($path.'lib/classes/core/logsystem.class.php');
  //Klasse fürs Menü
  require_once($path.'lib/classes/core/menus.class.php');
  //Helper-Klasse fürs Editing
  require_once($path.'lib/classes/core/editinghelper.class.php');

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

  //Smarty-Klasse einbinden
  require_once ($path.'lib/classes/extern/smarty/Smarty.class.php');

  //Smarty Objekt initialisieren
  $smarty = new Smarty;
  $smarty->compile_check = true;
  //Volle Debuggingseite für Smarty bei true (Sehr nützlich, wenn man wissen will, was alles zugewiesen wird)
  $smarty->debugging = false;
  //Templateordner festlegen
  $smarty->template_dir = "templates/html";
  //Template-Compile-Ordner festlegen
  $smarty->compile_dir = 'templates_c';

	/**
	* Auto Login
	*/

	if (!UserManagement::isLoggedIn($_SESSION['user_id']) AND !empty($_COOKIE["nickname"]) AND !empty($_COOKIE["password_hash"])) {
		//Login Class
		require_once($path.'lib/classes/core/login.class.php');
		Login::user_login($_COOKIE["nickname"], $_COOKIE["password_hash"], false, true);
	}

	/**
	* Menus
	*/

  $smarty->assign('top_menu', $Menus->topMenu());
  $smarty->assign('loginOutMenu', $Menus->loginOutMenu());
  $smarty->assign('normal_menu', $Menus->normalMenu());
  $smarty->assign('user_menu', $Menus->userMenu());
  $smarty->assign('admin_menu', $Menus->adminMenu());

  //Wichtige Config-Variablen an Smarty übergeben
  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
  $smarty->assign('zeit', date("d.m.Y H:i:s", time())." Uhr");
?>