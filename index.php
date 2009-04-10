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
  require_once('./config/config.local.inc.php');

  /**
  * SMARTY TEMPLATEENGINE
  * (Nötige Schritte beim Wechel auf eine neue Smarty Version müssen dokumentiert werden!)
  */

  //Smarty-Klasse einbinden
  require_once ('./smarty/libs/Smarty.class.php');
  //Smarty Objekt initialisieren
  $smarty = new Smarty;
  $smarty->compile_check = true;
  //Volle Debuggingseite für Smarty bei true (Sehr nützlich, wenn man wissen will, was alles zugewiesen wird)
  $smarty->debugging = false;
  //Templateordner festlegen
  $smarty->template_dir = "./templates/html";
  //Template-Compile-Ordner festlegen
  $smarty->compile_dir = './smarty/templates_c';

  /**
  * WICHTIGE KLASSEN
  */

  //Klasse für Mysql-Verbindungen einbinden
  require_once('./lib/classes/core/mysql.class.php');
  //Klasse für Nachrichten einbinden
  require_once('./lib/classes/core/message.class.php');
  //Allgemeine Helper-Klasse
  require_once('./lib/classes/core/helper.class.php');
  //Klasse für Usermanagement
  require_once('./lib/classes/core/usermanagement.class.php');  
  //Klasse fürs Logging
  require_once('./lib/classes/core/logsystem.class.php');
  //Klasse fürs Menü
  require_once('./lib/classes/core/menus.class.php');
  //Helper-Klasse fürs Editing
  require_once('./lib/classes/core/editinghelper.class.php');  

  $usermanagement =  new usermanagement;
  $menus =  new menus;

  /**
  * INHALTE JE NACH ÜBERGEBENEM GET EINBINDEN
  */

    $gotten = false;

//    echo "<b>Derzeit werden änderungen an der DB vorgenommen!!!</b>";

  if (!isset($_GET['get'])) {
    $usermanagement->act($smarty, 1);
    $smarty->assign('get_content', "portal");
//     $smarty->display("design.tpl.php");
    $gotten = true;
  }

  if ($_GET['get'] == "portal") {
    $usermanagement->act($smarty, 1);
    $smarty->assign('get_content', "portal");

    require_once('./lib/classes/core/portal.class.php');
    $nodeeditor = new portal($smarty);
    $gotten = true;
  }
  if ($_GET['get'] == "impressum") {
    $usermanagement->act($smarty, 1);
    $smarty->assign('get_content', "impressum");
//     $smarty->display("design.tpl.php");
    $gotten = true;
  }

  if ($_GET['get'] == "register") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/register.class.php');
    $register = new register($smarty);
//     $smarty->display("design.tpl.php");
    $gotten = true;
  }
  
  if ($_GET['get'] == "login") {
    $usermanagement->act($smarty, 2);
    require_once('./lib/classes/core/login.class.php');
    $register = new login($smarty);
//    $smarty->display("design.tpl.php");
    $gotten = true;
  }

  if ($_GET['get'] == "logout") {
    $usermanagement->act($smarty, 8);
    require_once('./lib/classes/core/login.class.php');
    $register = new login($smarty);
//     $smarty->display("design.tpl.php");
    $gotten = true;
  }

  if ($_GET['get'] == "desktop") {
    //require_once('./lib/classes/core/login.class.php');
    //$register = new login($smarty);
    $usermanagement->act($smarty, 8);
    $smarty->assign('get_content', "desktop");
//     $smarty->display("design.tpl.php");
    $gotten = true;
  }

  if ($_GET['get'] == "nodeeditor") {
    $usermanagement->act($smarty, 8);
    require_once('./lib/classes/core/nodeeditor.class.php');
    $nodeeditor = new nodeeditor($smarty);
//     $smarty->display("design.tpl.php");
    $gotten = true;
  }

  if ($_GET['get'] == "subneteditor") {
    $usermanagement->act($smarty, 32);
    require_once('./lib/classes/core/subneteditor.class.php');
    $nodeeditor = new subneteditor($smarty);
//     $smarty->display("design.tpl.php");
    $gotten = true;
  }

  if ($_GET['get'] == "subnetlist") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/subnetlist.class.php');
    $nodelist = new subnetlist($smarty);
    $gotten = true;
  }
  
  if ($_GET['get'] == "subnet") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/subnet.class.php');
    $nodelist = new subnet($smarty);
    $gotten = true;
  }
  
  if ($_GET['get'] == "nodelist") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/nodelist.class.php');
    $nodelist = new nodelist($smarty);
    $gotten = true;
  }

  if ($_GET['get'] == "node") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/node.class.php');
    $nodelist = new node($smarty);
    $gotten = true;
  }

  if ($_GET['get'] == "userlist") {
    $usermanagement->act($smarty, 8);
    require_once('./lib/classes/core/userlist.class.php');
    $nodelist = new userlist($smarty);
    $gotten = true;
  }
  
  if ($_GET['get'] == "user") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/login.class.php');
    require_once('./lib/classes/core/user.class.php');
    $nodelist = new user($smarty);
    $gotten = true;
  }

  if ($_GET['get'] == "vpn") {
    $usermanagement->act($smarty, 8);
    require_once('./lib/classes/extern/archive.class.php');
    require_once('./lib/classes/core/vpn.class.php');
    $nodelist = new vpn($smarty);
    $gotten = true;
  }

  if ($_GET['get'] == "getinfo") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/getinfo.class.php');
    $getinfo = new getinfo($smarty);
    $gotten = true;
  }

  if ($_GET['get'] == "graphmonitor") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/graphmonitor.class.php');
    $getinfo = new graphmonitor($smarty);
    $gotten = true;
  }

  if ($_GET['get'] == "status") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/status.class.php');
    $getinfo = new status($smarty);
    $gotten = true;
  }

  if ($_GET['get'] == "service") {
    $usermanagement->act($smarty, 1);
    require_once('./lib/classes/core/service.class.php');
    $getinfo = new service($smarty);
    $gotten = true;
  }

  if ($_GET['get'] == "serviceeditor") {
    $usermanagement->act($smarty, 8);
    require_once('./lib/classes/core/serviceeditor.class.php');
    $getinfo = new serviceeditor($smarty);
    $gotten = true;
  }
       
  //---------------


  if (!$gotten) {
    class index {
      function __construct(&$smarty) {
	$message[] = array("Die angeforderte Seite existiert nicht", 2);
	message::setMessage($message);
	$smarty->assign('message', message::getMessage());
      }
    }
    new index($smarty);
  }

  $smarty->assign('top_menu', $menus->topMenu());
  $smarty->assign('normal_menu', $menus->normalMenu());
  $smarty->assign('user_menu', $menus->userMenu());
  $smarty->assign('admin_menu', $menus->adminMenu());


 //Muss am Ende stehen damit die Menüs mit aktuellen Daten versorgt werden
 $smarty->display("design.tpl.php");
?>