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

class menus extends usermanagement {

  function topMenu() {
    if (usermanagement::checkPermission(1)) {
      //Links nach belieben anpassen.
      $menu[] = array('name'=>'Nodelist', 'href'=>'./index.php?get=nodelist');
      $menu[] = array('name'=>'Freifunkmap', 'href'=>'./index.php?get=map');
      $menu[] = array('name'=>'Wiki', 'href'=>'http://deinwiki.de');
      $menu[] = array('name'=>'Mailingliste', 'href'=>'http://deineliste.de');
      $menu[] = array('name'=>'Blog', 'href'=>'http://deinblog.de'); 
      $menu[] = array('name'=>'OLSR', 'href'=>'http://deinolsr.de:8888'); 
    }
    if (usermanagement::checkPermission(2)) {
      $menu[] = array('name'=>'Login', 'href'=>'./index.php?get=login');
      $menu[] = array('name'=>'Registrieren', 'href'=>'./index.php?get=register');
    }
    
    if (usermanagement::checkPermission(4)) {
      $menu[] = array('name'=>'Logout', 'href'=>'./index.php?get=logout');
    }
    return $menu;
  }

  function normalMenu() {
    if (usermanagement::checkPermission(1)) {
      $menu[] = array('name'=>'Portal', 'href'=>'./index.php?get=portal');
      $menu[] = array('name'=>'Map', 'href'=>'./index.php?get=map');
      $menu[] = array('name'=>'Netzwerkstatus', 'href'=>'./index.php?get=status');
      $menu[] = array('name'=>'Nodelist', 'href'=>'./index.php?get=nodelist');
      $menu[] = array('name'=>'Subnetliste', 'href'=>'./index.php?get=subnetlist');
      $menu[] = array('name'=>'Impressum', 'href'=>'http://wiki.freifunk-ol.de/index.php/Freifunk_OL_Wiki:Impressum');
    }
    return $menu;
  }

  function userMenu() {
    if (usermanagement::checkPermission(8)) {
      $menu[] = array('name'=>'Desktop', 'href'=>'./index.php?get=desktop');
      $menu[] = array('name'=>'Neuer Node', 'href'=>'./index.php?get=nodeeditor&section=new');
//      $menu[] = array('name'=>'Service hinzufügen', 'href'=>'./index.php?get=nodeeditor&section=add_service');
      $menu[] = array('name'=>'Mein Benutzer', 'href'=>'./index.php?get=user&id='.$_SESSION['user_id']);
      $menu[] = array('name'=>'Benutzer ändern', 'href'=>'./index.php?get=user&section=edit&id='.$_SESSION['user_id']);
      $menu[] = array('name'=>'Benutzerliste', 'href'=>'./index.php?get=userlist');
    }
    return $menu;
  }

  function adminMenu() {
    if (usermanagement::checkPermission(32)) {
      $menu[] = array('name'=>'Neues Subnetz', 'href'=>'./index.php?get=subneteditor&section=new');
      $menu[] = array('name'=>'CCD regenerieren', 'href'=>'./index.php?get=vpn&section=regenerate_ccd_subnet');

    }
    return $menu;
  }

}

?>