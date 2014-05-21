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

require_once(ROOT_DIR.'/lib/core/DnsZone.class.php');
require_once(ROOT_DIR.'/lib/core/User.class.php');

class Menus extends Permission {
	function topMenu() {
		$menu = array();
		if (Permission::checkPermission(pow(2,2))) {
			$user = new User((int)$_SESSION['user_id']);
			$user->fetch();
			$menu[] = array('name'=>$user->getNickname(), 'href'=>'user.php?user_id='.$user->getUserId());
		}
		return $menu;
	}
	
	public function loginOutMenu() {
		$menu = array();
		if (Permission::checkPermission(PERM_NOTLOGGEDIN)) {
			$menu[] = array('name'=>'Login', 'href'=>'login.php?section=login');
			$menu[] = array('name'=>'Registrieren', 'href'=>'register.php');
		}
	
		if (Permission::checkPermission(PERM_LOGGEDIN)) {
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

		$submenu = array();
		$subsubmenu = array();
		$submenu[] = array('name'=>'Karte', 'href'=>'map.php');
		$subsubmenu[] = array('name'=>'FF-Map 3D', 'href'=>'https://netmon.freifunk-ol.de/ffmap-d3/nodes.html');
		$submenu[] = $subsubmenu;
		$menu[] = $submenu;
		
		$submenu = array();
		$subsubmenu = array();
		$submenu[] = array('name'=>'Router', 'href'=>'routerlist.php');			
		$subsubmenu[] = array('name'=>'Neue Router', 'href'=>'routers_trying_to_assign.php');
		if (Permission::checkPermission(PERM_USER)) //if user is logged in and has permission "user"
			$subsubmenu[] = array('name'=>'Router anlegen', 'href'=>'routereditor.php?section=new');
		$submenu[] = $subsubmenu;
		$menu[] = $submenu;

		$submenu = array();
		$subsubmenu = array();
		$submenu[] = array('name'=>'Netzwerke', 'href'=>'networks.php');
		$submenu[] = $subsubmenu;
		$menu[] = $submenu;
		
		$submenu = array();
		$subsubmenu = array();
		$submenu[] = array('name'=>'DNS', 'href'=>'dns_zone.php');
		$submenu[] = $subsubmenu;
		$menu[] = $submenu;
		
		$submenu = array();
		$subsubmenu = array();
		$submenu[] = array('name'=>'Dienste', 'href'=>'servicelist.php');
		$submenu[] = $subsubmenu;
		$menu[] = $submenu;
		
		$submenu = array();
		$subsubmenu = array();
		$submenu[] = array('name'=>'Statistik', 'href'=>'networkstatistic.php');
		$submenu[] = array();
		$menu[] = $submenu;
		
		$submenu = array();
		$subsubmenu = array();
		$submenu[] = array('name'=>'Events', 'href'=>'eventlist.php');
		$submenu[] = array();
		$menu[] = $submenu;

		if (Permission::checkPermission(PERM_USER)) { //if user is logged in
			$submenu = array();
			$subsubmenu = array();
			$submenu[] = array('name'=>'Benutzer', 'href'=>'userlist.php');
			$submenu[] = $subsubmenu;
			$menu[] = $submenu;
		}
		
		$submenu = array();
		$subsubmenu = array();
		$submenu[] = array('name'=>'Suchen', 'href'=>'search.php');
		$submenu[] = array();
		$menu[] = $submenu;

		return $menu;
	}
	
	function objectMenu() {
		$menu = array();
		$submenu = array();
		$subsubmenu = array();
		if (Permission::checkPermission(PERM_USER)) {
			if(strpos($_SERVER['PHP_SELF'], "router.php")!==false AND !isset($_GET['section'])) {
				$submenu[] = array('name'=>'Routeroptionen', 'href'=>'#');
				$subsubmenu[] = array('name'=>'Bearbeiten', 'href'=>'routereditor.php?section=edit&router_id='.$_GET['router_id']);
				$subsubmenu[] = array('name'=>'API-Keys', 'href'=>'api_key_list.php?object_type=router&object_id='.$_GET['router_id']);
				$subsubmenu[] = array('name'=>'Interf. hinzufügen', 'href'=>'interface.php?section=add&router_id='.$_GET['router_id']);
				$submenu[] = $subsubmenu;
				$menu[] = $submenu;
			} elseif(strpos($_SERVER['PHP_SELF'], "user.php")!==false) {
				$submenu[] = array('name'=>'Benutzeroptionen', 'href'=>'#');
				if(Permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $_GET['user_id'])) {
					$subsubmenu[] = array('name'=>'Bearbeiten', 'href'=>'user_edit.php?section=edit&user_id='.$_GET['user_id']);
					$subsubmenu[] = array('name'=>'API-Keys', 'href'=>'api_key_list.php?object_type=user&object_id='.$_GET['user_id']);
					$subsubmenu[] = array('name'=>'Benachrichtigungen', 'href'=>'event_notifications.php?section=edit&user_id='.$_GET['user_id']);
					$subsubmenu[] = array('name'=>'Dienst hinzufügen', 'href'=>'service.php?section=add&user_id='.$_GET['user_id']);
				}
				$submenu[] = $subsubmenu;
				$menu[] = $submenu;
			} elseif(strpos($_SERVER['PHP_SELF'], "dns_zone.php")!==false AND isset($_GET['dns_zone_id'])) {
				$submenu[] = array('name'=>'Zonenoptionen', 'href'=>'#');
				$dns_zone = new DnsZone((int)$_GET['dns_zone_id']);
				$dns_zone->fetch();
				if(Permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $dns_zone->getUserId())) {
					$subsubmenu[] = array('name'=>'Bearbeiten', 'href'=>'dns_zone.php?section=edit&dns_zone_id='.$_GET['dns_zone_id']);
				}
				if (Permission::checkPermission(PERM_USER)) {
					$subsubmenu[] = array('name'=>'RR hinzufügen', 'href'=>'dns_ressource_record.php?section=add&dns_zone_id='.$_GET['dns_zone_id']);
				}
				$submenu[] = $subsubmenu;
				$menu[] = $submenu;
			}
		}
		return $menu;
	}
	
	function adminMenu() {
		$menu = array();
		return $menu;
	}

	function rootMenu() {
		$menu = array();
		if (Permission::checkPermission(PERM_ROOT)) {
			$submenu = array();
			$subsubmenu = array();
			$submenu[] = array('name'=>'Konfiguration', 'href'=>'config.php?section=edit_netmon');			
			$subsubmenu[] = array('name'=>'Datenbank', 'href'=>'config.php?section=edit');
			$subsubmenu[] = array('name'=>'Community', 'href'=>'config.php?section=edit_community');
			$subsubmenu[] = array('name'=>'Netzwerkverbindung', 'href'=>'config.php?section=edit_network_connection');
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