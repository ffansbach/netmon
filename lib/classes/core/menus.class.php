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
 * This class contains the menustructure.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class Menus extends UserManagement {

  function topMenu() {
    if (UserManagement::checkPermission(1)) {
      //Please put your own links here
      $menu[] = array('name'=>'Wiki', 'href'=> 'http://wiki.freifunk-ol.de');
      $menu[] = array('name'=>'Blog', 'href'=>'http://blog.freifunk-ol.de'); 
      $menu[] = array('name'=>'Mailingliste', 'href'=>'http://lists.nord-west.net/mailman/listinfo/freifunk-ol');
      $menu[] = array('name'=>'OLSR', 'href'=>'http://olsr.freifunk-ol.de:8888');
      $menu[] = array('name'=>'Trac', 'href'=>'https://trac.freifunk-ol.de');
      $menu[] = array('name'=>'Software Repository', 'href' => 'http://dev.freifunk-ol.de/');
    }
    return $menu;
  }
  
  public function loginOutMenu() {
    if (UserManagement::checkPermission(2)) {
      $menu[] = array('name'=>'Login', 'href'=>'./login.php?section=login');
      $menu[] = array('name'=>'Registrieren', 'href'=>'./register.php');
    }
    
    if (UserManagement::checkPermission(4)) {
      $user_data = Helper::getUserByID($_SESSION['user_id']);

      $menu[] = array('pretext'=>'Eingeloggt als:', 'name'=>$user_data['nickname'], 'href'=>"./user.php?id=$_SESSION[user_id]");
      $menu[] = array('name'=>'Logout', 'href'=>'./login.php?section=logout');
    }
    return $menu;
  }

  function installationMenu() {
      $menu[] = array('name'=>'Übersicht', 'href'=>'./install.php');
      $menu[] = array('name'=>'Datenbank', 'href'=>'./install.php?section=db');
      $menu[] = array('name'=>'Nachrichten', 'href'=>'./install.php?section=messages');
      $menu[] = array('name'=>'Netzwerk', 'href'=>'./install.php?section=network');
      $menu[] = array('name'=>'Beenden', 'href'=>'./install.php?section=finish');
    return $menu;
  }

  function normalMenu() {
    if (UserManagement::checkPermission(1)) {
      $menu[] = array('name'=>'Portal', 'href'=>'./portal.php');
      $menu[] = array('name'=>'Map', 'href'=>'./map.php');
      $menu[] = array('name'=>'Topologie', 'href'=>'./topology.php');
//      $menu[] = array('name'=>'Netzwerkstatus', 'href'=>'./status.php');
      $menu[] = array('name'=>'Ipliste', 'href'=>'./iplist.php');
      $menu[] = array('name'=>'Projektliste', 'href'=>'./subnetlist.php');
//      $menu[] = array('name'=>'Impressum', 'href'=>'./impressum.php');
    }
    return $menu;
  }

  function userMenu() {
    if (UserManagement::checkPermission(8)) {
      $menu[] = array('name'=>'Desktop', 'href'=>'./desktop.php');
      $menu[] = array('name'=>'Neue Ip', 'href'=>'./ipeditor.php?section=new');
//      $menu[] = array('name'=>'Mein Benutzer', 'href'=>'./user.php?id='.$_SESSION['user_id']);
//      $menu[] = array('name'=>'Benutzer ändern', 'href'=>'./user_edit.php?section=edit&id='.$_SESSION['user_id']);
      $menu[] = array('name'=>'Benutzerliste', 'href'=>'./userlist.php');
    }
    return $menu;
  }

  function adminMenu() {
    if (UserManagement::checkPermission(32)) {
      $menu[] = array('name'=>'Neues Projekt', 'href'=>'./subneteditor.php?section=new');
      $menu[] = array('name'=>'CCD regenerieren', 'href'=>'./vpn.php?section=regenerate_ccd_subnet');
    }
    return $menu;
  }
  function rootMenu() {
    if (UserManagement::checkPermission(64)) {
      $menu[] = array('name'=>'Konfiguration', 'href'=>'./config.php?section=edit');
    }
    return $menu;
  }

}

?>