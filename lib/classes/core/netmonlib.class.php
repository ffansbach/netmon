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
 * This file is the basefile of the project
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

  //Lokale Konfiguration einbinden
  require_once('config/config.local.inc.php');

  /**
  * WICHTIGE KLASSEN
  */

  //Klasse für Mysql-Verbindungen einbinden
  require_once('lib/classes/core/mysql.class.php');
  //Klasse für Nachrichten einbinden
  require_once('lib/classes/core/message.class.php');
  //Allgemeine Helper-Klasse
  require_once('lib/classes/core/helper.class.php');
  //Klasse für Usermanagement
  require_once('lib/classes/core/usermanagement.class.php');  
  //Klasse fürs Logging
  require_once('lib/classes/core/logsystem.class.php');
  //Klasse fürs Menü
  require_once('lib/classes/core/menus.class.php');
  //Helper-Klasse fürs Editing
  require_once('lib/classes/core/editinghelper.class.php');

  $usermanagement =  new usermanagement;
  $menus =  new menus;

  /**
  * SMARTY TEMPLATEENGINE
  */

  //Smarty-Klasse einbinden
  require_once ('smarty/libs/Smarty.class.php');

  //Smarty Objekt initialisieren
  $smarty = new Smarty;
  $smarty->compile_check = true;
  //Volle Debuggingseite für Smarty bei true (Sehr nützlich, wenn man wissen will, was alles zugewiesen wird)
  $smarty->debugging = false;
  //Templateordner festlegen
  $smarty->template_dir = "templates/html";
  //Template-Compile-Ordner festlegen
  $smarty->compile_dir = 'smarty/templates_c';


  $smarty->assign('top_menu', $menus->topMenu());
  $smarty->assign('normal_menu', $menus->normalMenu());
  $smarty->assign('user_menu', $menus->userMenu());
  $smarty->assign('admin_menu', $menus->adminMenu());


  class netmonLib {
    function __construct() {

    }
  }

?> 
