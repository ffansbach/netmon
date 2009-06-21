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
 * This file contains the class for logging in a user.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class login {
  public function user_login ($nickname, $password, &$message) {
      if (empty($nickname) or empty($password)) {
        $messages[] = array("Sie müssen einen Nickname und ein Passwort angeben um sich einzuloggen", 2);
	message::setMessage($messages);
	return false;
      } else {
        $password = usermanagement::encryptPassword($password);
	//Prüfen ob Emailadresse existiert
	$db = new mysqlClass;
	$result = $db->mysqlQuery("select id , nickname, activated, DATE_FORMAT(last_login, '%D %M %Y um %H:%i:%s Uhr') as last_login from users WHERE nickname='$nickname' and password='$password'");
	$login = $db->mysqlAffectedRows();
	$user_data = mysql_fetch_assoc($result);
	unset($db);
	if ($login<1) {
	  $messages[] = array("Der Benutzername existiert nicht oder das Passwort ist falsch!", 2);
	  message::setMessage($messages);
	  return false;
	} elseif ($user_data['activated'] != '0') {
	  $messages[] = array("Der Benutzername wurde noch nicht aktiviert!", 2);
	  message::setMessage($messages);
	  return false;
	} else {
	  $db = new mysqlClass;
	  $db->mysqlQuery("UPDATE users SET last_login = NOW() where id=".$user_data['id']);
	  unset($db);


	  $_SESSION['user_id'] = $user_data['id'];
	  if (isset($user_data['last_login'])) {
	    $last_login = " Ihr letzter Login war am ".$user_data['last_login'];
	  }
	  $messages[] = array("Herzlich willkommen zurück ".$user_data['nickname'].$last_login, 1);
	  message::setMessage($messages);
	  return true;
	}
      }
  }

  public function user_logout(&$message) {
      if (!isset($_SESSION['user_id'])) {
        $messages[] = array("Sie können sich nicht ausloggen, wenn Sie nicht eingeloggt sind", 2);
	message::setMessage($messages);
	return false;
      } else {
	$_SESSION = array();;
	$messages[] = array("Sie wurden ausgeloggt und ihre Benutzersession wurde gelöscht!", 1);
	message::setMessage($messages);
	return true;
      }
  }


}

?>