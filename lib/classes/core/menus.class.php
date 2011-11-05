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
	    return $GLOBALS['topMenu'];
    }
  }
  
  public function loginOutMenu() {
    if (UserManagement::checkPermission(2)) {
      $menu[] = array('name'=>'Login', 'href'=>'login.php?section=login');
      $menu[] = array('name'=>'Registrieren', 'href'=>'register.php');
    }
    
    if (UserManagement::checkPermission(4)) {
      $user_data = Helper::getUserByID($_SESSION['user_id']);

      $menu[] = array('pretext'=>'Eingeloggt als:', 'name'=>$user_data['nickname'], 'href'=>"user.php?user_id=$_SESSION[user_id]");
      $menu[] = array('name'=>'Logout', 'href'=>'login.php?section=logout');
    }
    return $menu;
  }

  function installationMenu() {
      $menu[] = array('name'=>'Übersicht', 'href'=>'install.php');
      $menu[] = array('name'=>'Datenbank', 'href'=>'install.php?section=db');
      $menu[] = array('name'=>'Nachrichten', 'href'=>'install.php?section=messages');
      $menu[] = array('name'=>'Netzwerk', 'href'=>'install.php?section=network');
      $menu[] = array('name'=>'Beenden', 'href'=>'install.php?section=finish');
    $menu = Menus::checkIfSelected($menu);
    return $menu;
  }

  function normalMenu() {
    if (UserManagement::checkPermission(1)) {
//      $menu[] = array('name'=>'News', 'href'=>'portal.php');
      $menu[] = array('name'=>'Map', 'href'=>'map.php');
      $menu[] = array('name'=>'Routerliste', 'href'=>'routerlist.php', 'selected'=>$selected);
      $menu[] = array('name'=>'Dienste', 'href'=>'servicelist.php');
      $menu[] = array('name'=>'Netzwerkstatistik', 'href'=>'networkstatistic.php');
      $menu[] = array('name'=>'Topologie', 'href'=>'http://dev.freifunk-ol.de/topo/batvpn.png');
      $menu[] = array('name'=>'Suchen', 'href'=>'search.php');
    }

    $menu = Menus::checkIfSelected($menu);
    return $menu;
  }

  function userMenu() {
    if (UserManagement::checkPermission(8)) {
//      $menu[] = array('name'=>'Mein Benutzer', 'href'=>"user.php?user_id=$_SESSION[user_id]");
      $menu[] = array('name'=>'Neuer Router', 'href'=>'routereditor.php?section=new');
      $menu[] = array('name'=>'Benutzerliste', 'href'=>'userlist.php');
    }
    $menu = Menus::checkIfSelected($menu);
    return $menu;
  }

  function adminMenu() {
    if (UserManagement::checkPermission(32)) {
//      $menu[] = array('name'=>'Neues Projekt', 'href'=>'subneteditor.php?section=new');
      $menu[] = array('name'=>'Neues Projekt', 'href'=>'projecteditor.php?section=new');
      $menu[] = array('name'=>'Imagemaker', 'href'=>'imagemaker.php');
//      $menu[] = array('name'=>'CCD regenerieren', 'href'=>'vpn.php?section=regenerate_ccd_subnet');
      $menu[] = array('name'=>'Projektliste', 'href'=>'projectlist.php');
    }
    $menu = Menus::checkIfSelected($menu);
    return $menu;
  }
  function rootMenu() {
    if (UserManagement::checkPermission(64)) {
      $menu[] = array('name'=>'Konfiguration', 'href'=>'config.php?section=edit');
    }
    $menu = Menus::checkIfSelected($menu);
    return $menu;
  }

	public function checkIfSelected($menu) {
		if(!empty($menu)) {
			foreach($menu as $key=>$m) {
				if (stristr($_SERVER["REQUEST_URI"], $menu[$key]['href'])!==FALSE) {
					$menu[$key]['selected'] = true;
				} else {
					$menu[$key]['selected'] = false;
				}
			}
		}

		return $menu;
	}

}

?>