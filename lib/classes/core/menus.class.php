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

require_once('lib/classes/core/user.class.php');

class Menus extends UserManagement {
	function topMenu() {
		$menu = array();
		if (UserManagement::checkPermission(1)) {
			return $GLOBALS['topMenu'];
		}
	}
  
	public function loginOutMenu() {
		$menu = array();
		if (UserManagement::checkPermission(2)) {
			$menu[] = array('name'=>'Login', 'href'=>'login.php?section=login');
			$menu[] = array('name'=>'Registrieren', 'href'=>'register.php');
		}
	
		if (UserManagement::checkPermission(4)) {
			$menu[] = array('name'=>'Logout', 'href'=>'login.php?section=logout');
		}
		return $menu;
	}
	
	function installationMenu() {
		$menu = array();
		$menu[] = array('name'=>'Übersicht', 'href'=>'install.php');
		$menu[] = array('name'=>'Datenbank', 'href'=>'install.php?section=db');
		$menu[] = array('name'=>'Nachrichten', 'href'=>'install.php?section=messages');
		$menu[] = array('name'=>'Netzwerk', 'href'=>'install.php?section=network');
		$menu[] = array('name'=>'Beenden', 'href'=>'install.php?section=finish');
		$menu = Menus::checkIfSelected($menu);
		return $menu;
	}
	
	function normalMenu() {
		$menu = array();
		if (UserManagement::checkPermission(1)) {
			$submenu = array();
			$subsubmenu = array();
			$submenu[] = array('name'=>'Karte', 'href'=>'map.php');
			$submenu[] = array();
			$menu[] = $submenu;
			
			$submenu = array();
			$subsubmenu = array();
			$submenu[] = array('name'=>'Router', 'href'=>'routerlist.php');			
			$subsubmenu[] = array('name'=>'Neue Router', 'href'=>'routers_trying_to_assign.php');
			if (UserManagement::checkPermission(12)) //if user is logged in and has permission "user"
				$subsubmenu[] = array('name'=>'Router anlegen', 'href'=>'routereditor.php?section=new');
			$submenu[] = $subsubmenu;
			$menu[] = $submenu;

			$submenu = array();
			$subsubmenu = array();
			$submenu[] = array('name'=>'Dienste', 'href'=>'servicelist.php');
			if (UserManagement::checkPermission(12)) { //if user is logged in and has permission "user"
				$subsubmenu[] = array('name'=>'Dienst anlegen', 'href'=>'serviceeditor.php?section=add');
				$subsubmenu[] = array('name'=>'Domain anlegen', 'href'=>'dnseditor.php?section=add_host');
			}
			$submenu[] = $subsubmenu;
			$menu[] = $submenu;

			$submenu = array();
			$subsubmenu = array();
			$submenu[] = array('name'=>'Statistik', 'href'=>'networkstatistic.php');
			$subsubmenu[] = array('name'=>'Historie', 'href'=>'networkhistory.php');
			$submenu[] = $subsubmenu;
			$menu[] = $submenu;

			$submenu = array();
			$subsubmenu = array();
			$submenu[] = array('name'=>'Topologie', 'href'=>'http://dev.freifunk-ol.de/topo/batvpn.png');
			$submenu[] = array();
			$menu[] = $submenu;
			
			if (UserManagement::checkPermission(4)) { //if user is logged in
				$submenu = array();
				$subsubmenu = array();
				$submenu[] = array('name'=>'Benutzer', 'href'=>'userlist.php');
				$subsubmenu[] = array('name'=>'Mein Benutzer', 'href'=>'user.php?user_id='.$_SESSION['user_id']);
				$subsubmenu[] = array('name'=>'Einstellungen', 'href'=>'user_edit.php?section=edit&user_id='.$_SESSION['user_id']);
				$submenu[] = $subsubmenu;
				$menu[] = $submenu;
			}

			$submenu = array();
			$subsubmenu = array();
			$submenu[] = array('name'=>'Suchen', 'href'=>'search.php');
			$submenu[] = array();
			$menu[] = $submenu;
		}
//		$menu = Menus::checkIfSelected($menu);
		return $menu;
	}

	function adminMenu() {
		$menu = array();
		if (UserManagement::checkPermission(32)) {
//			$menu[] = array('name'=>'Neues Projekt', 'href'=>'subneteditor.php?section=new');
			$menu[] = array('name'=>'Neues Projekt', 'href'=>'projecteditor.php?section=new');
//			$menu[] = array('name'=>'CCD regenerieren', 'href'=>'vpn.php?section=regenerate_ccd_subnet');
			$menu[] = array('name'=>'Projektliste', 'href'=>'projectlist.php');
		}
		$menu = Menus::checkIfSelected($menu);
		return $menu;
	}

	function rootMenu() {
		$menu = array();
		if (UserManagement::checkPermission(64)) {
			$submenu = array();
			$subsubmenu = array();
			$submenu[] = array('name'=>'Konfiguration', 'href'=>'config.php?section=edit_netmon');			
			$subsubmenu[] = array('name'=>'Datenbank', 'href'=>'config.php?section=edit');
			$subsubmenu[] = array('name'=>'Community', 'href'=>'config.php?section=edit_community');
			$subsubmenu[] = array('name'=>'Mail', 'href'=>'config.php?section=edit_email');
			$subsubmenu[] = array('name'=>'Jabber', 'href'=>'config.php?section=edit_jabber');
			$subsubmenu[] = array('name'=>'Twitter', 'href'=>'config.php?section=edit_twitter');
			$subsubmenu[] = array('name'=>'Hardware', 'href'=>'config.php?section=edit_hardware');
			$submenu[] = $subsubmenu;
			$menu[] = $submenu;
		}
//		$menu = Menus::checkIfSelected($menu);
		return $menu;
	}
	
	public function checkIfSelected($menu) {
		if(!empty($menu)) {
			foreach($menu as $key=>$m) {
				if (isset($_SERVER["REQUEST_URI"]) AND stristr($_SERVER["REQUEST_URI"], $menu[$key]['href'])!==FALSE) {
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