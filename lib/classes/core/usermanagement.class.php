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
 * This file contains the class for permissionadministration
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class usermanagement {
  
  //Verschlüssele Passwort (nicht mit md5! Ändern!)
  //Funktion muss auch für Login und überall wo das Passwort benötigt wird angewendet werden!
  public function encryptPassword($password) {
    return md5($password);
  }
  
  public function act(&$smarty, $sitepermission) {
    if (!usermanagement::checkPermission($sitepermission)) {
      $message[] = array("Sie haben nicht das Recht auf diesen Bereich zuzugreifen!",2);
      message::setMessage($message);
      $smarty->assign('message', message::getMessage());
//      $smarty->assign('get_content', "portal");
      $smarty->display("design.tpl.php");
      die();
    }
  }

  public function isOwner(&$smarty, $owning_user_id) {
    if($owning_user_id!=$_SESSION['user_id'] OR !isset($owning_user_id)) {
      $message[] = array("Sie haben nicht das Recht auf diesen Bereich zuzugreifen, da Sie nicht der Eigentümer des Objekts sind!",2);
      message::setMessage($message);
      $smarty->assign('message', message::getMessage());
//      $smarty->assign('get_content', "portal");
      $smarty->display("design.tpl.php");
      die();
    } else {
      return true;
    }
  }

  public function isThisUserOwner($owning_user_id) {
    if($owning_user_id!=$_SESSION['user_id'] OR !isset($owning_user_id)) {
      return false;
    } else {
      return true;
    }
  }

  public function checkPermission($sitepermission) {
    $userpermission = usermanagement::getUserPermission();
    //Rechte ins Binärsystem wandeln
    $sitepermission = decbin($sitepermission);
    $userpermission = decbin($userpermission);
    $sitepermission_len = strlen($sitepermission);
    $userpermission_len = strlen($userpermission);
 
    //Alle verfügbaren Rechte-Optionen holen
    $permission = usermanagement::getAllPermissions();

    $return = false;
    for ($i=count($permission)-1; $i>=0; $i--) {
      $exponent = $permission[$i];
      if ($sitepermission[$sitepermission_len-($exponent+1)]==1) {
	if ($sitepermission[$sitepermission_len-($exponent+1)]==$userpermission[$userpermission_len-($exponent+1)]) {
	  $return = true;
	}
      }
    }
    return $return;
  }

  public function getUserPermission() {
  	if (!isset($_SESSION['user_id'])) {
      //Jeder Benutzer bekommt das recht 0
      $userpermission = pow(2,0);
      //Jder nicht eingeloggte Benutzer bekommt das recht 1
      $userpermission = $userpermission + pow(2,1);
    } else {
      //Jeder Benutzer bekommt das recht 0
      $userpermission = pow(2,0);
      //Jeder eingeloggte Benutzer bekommt das recht "eingeloggt"
      $userpermission = $userpermission + pow(2,2);

	  $db = new mysqlClass;
      $result = $db->mysqlQuery("select permission from users WHERE id=$_SESSION[user_id]");
      $user_data = mysql_fetch_assoc($result);
      unset($db);
      
      //Jeder Benutzer bekommt das recht aus der Datenbank
      $userpermission = $userpermission + $user_data['permission'];
    }
    return $userpermission;
  }

  public function getAllPermissions() {
    /*
0 = Alle, Recht wird vom Script erstellt
1 = nicht eingeloggte, Recht wird vom Script erstellt
2 = eingeloggte, Recht wird vom Script erstellt
3 = user, Recht in DB
4 = mod, Recht in DB
5 = admin, Recht in DB
6 = root, Recht in DB
    */
    return array(0,1,2,3,4,5,6);
  }


}

?>