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
 * This file contains the class to register a new user.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class register {
  public function insertNewUser($nickname, $password, $passwordchk, $email, $agb) {
    if ($this->checkUserData($nickname, $password, $passwordchk, $email, $agb)) {
      $password = usermanagement::encryptPassword($password);
      $activation = md5($nickname);

      //Gib dem 1. user volle Rechte (User+Mod+Admin+Root; 2^3+2^4+2^5+2^6=120=Alle Rechte) 
      //Ansonten vergib nur User-Rechte (User; 2^3=8=User) 
      $db = new mysqlClass;
      $db->mysqlQuery("select * from users");
      if ($db->mysqlAffectedRows()<=0) {
	$permission = 120;
      } else {
	$permission = 8;
      }
      unset($db);


      //Mach DB Eintrag
      $db = new mysqlClass;
      $db->mysqlQuery("INSERT INTO users (nickname, password, email, permission, create_date, activated) VALUES ('$nickname', '$password', '$email', '$permission', NOW(), '$activation');");
      $ergebniss = $db->mysqlAffectedRows();
      $id = $db->getInsertID();
      unset($db);
      if ($ergebniss>0) {
        if($this->sendRegistrationEmail($email, $nickname, $passwordchk, $activation, time())) {
          $message[] = array("Der Benutzer ".$nickname." wurde erfolgreich angelegt.", 1);
	  message::setMessage($message);
	  return true;
        } else {
	  $message[] = array("Die Email mit dem Link zum aktivieren des Nutzerkontos konnte nicht an ".$email." verschickt werden.", 2);
	  $message[] = array("Der neu angelegte User mit der ID ".$id." wird wieder gelöscht.", 2);
	  message::setMessage($message);
	  $this->deleteUser($id);
	  return false;
	}
      } else {
	$message[] = array("Der Benutzer ".$nickname." konnte nicht in die Datenbank eingetragen werden werden.", 2);
	message::setMessage($message);
	return false;
      }
    }
  }

  public function userActivate($activation) {
    if (isset($activation)) {
      $db = new mysqlClass;
      $result = $db->mysqlQuery("SELECT nickname from users WHERE activated='$activation';");
      while($row = mysql_fetch_assoc($result)) {
	$nickname = $row['nickname'];
      }
      unset($db);

      $db = new mysqlClass;
      $db->mysqlQuery("UPDATE users SET activated=0 WHERE activated='$activation';");
      $ergebniss = $db->mysqlAffectedRows();
      unset($db);

      if ($ergebniss>0) {
        $message[] = array("Der Benutzer ".$nickname." wurde erfolgreich aktiviert.", 1);
        $message[] = array("Sie können sich jetzt mit Ihrem Benutzernamen und Ihrem Passwort einloggen", 1);
	message::setMessage($message);
	return true;
      } else {
	$message[] = array("Der Benutzer mit dem Aktivationcode ".$activation." konnte nicht freigeschaltet werden!", 2);
	message::setMessage($message);
	return false;
      }
    }
  }

  public function checkUserData($nickname, $password, $passwordchk, $email, $agb) {
    //Prüfen ob Nickname gesetzt ist
    if (!isset($nickname) OR $nickname == "") {
      $message[] = array("Es wurde kein Nickname angegeben.",2);
    } else {
      //Prüfen ob Nickname existiert
      $db = new mysqlClass;
      $db->mysqlQuery("select * from users WHERE nickname='$nickname'");
      $nicknamecheck = $db->mysqlAffectedRows();
      unset($db);
      if ($nicknamecheck>0) {
	$message[] = array("Ein Benutzer mit dem Namen ".$nickname." existiert bereits.",2);
      }
    }

    //Prüfen ob Email gesetzt ist
    if (!isset($email) OR $email == "") {
      $message[] = array("Es wurde keine Emailadresse angegeben.",2);
    } else {
      //Prüfen ob Emailadresse existiert
      $db = new mysqlClass;
      $db->mysqlQuery("select * from users WHERE email='$email'");
      $emailcheck = $db->mysqlAffectedRows();
      unset($db);
      if ($emailcheck>0) {
        $message[] = array("Ein Benutzer mit der Emailadresse ".$email." existiert bereits.",2);
      } else {
	//Prüfen ob die Emailadresse systaktisch korrekt ist
	$syntax = true;
	if (!$syntax) {
          $message[] = array("Die Emailadresse ".$email." ist syntaktisch falsch.",2);
	}
      }
    }

    //Prüfen ob Passwort gesetzt ist
    if (!isset($password) OR $password == "") {
      $message[] = array("Es wurde kein Passwort angegeben.",2);
    } elseif (!isset($passwordchk) OR $passwordchk == "") {
      $message[] = array("Das Passwort wurde kein zweites mal eingegeben.",2);
    } elseif ($password != $passwordchk) {
      $message[] = array("Die Passwörter stimmen nicht überein.",2);
    }

    if (!$agb) {
      $message[] = array("Bitte lesen und akzeptieren Sie die Netzwerkpolicy!",2);
    }

    //Rückgabe
    if (isset($message) AND count($message)>0) {
      message::setMessage($message);
      return false;
    } else {
      return true;
    }
  }

  public function deleteUser($id) {
    //Mach DB Eintrag
    $db = new mysqlClass;
    $db->mysqlQuery("DELETE FROM users WHERE id='$id';");
    $ergebniss = $db->mysqlAffectedRows();
    unset($db);
    $message[] = array("Der Benutzer mit der ID ".$id." wurde gelöscht.",1);
    message::setMessage($message);
    return true;
  }

  public function sendRegistrationEmail($email, $nickname, $password, $activation, $datum) {
        
    $text = "Hallo $nickname,

Du hast dich am ".date("d.m.Y H:i:s", $datum)." beim Oldenburger Freifunkprojekt angemeldet.

Deine Logindaten Sind:
Nickname: $nickname
Passwort: $password

Bitte klicke auf den nachfolgenden link um deinen Account freizuschalten.
http://$GLOBALS[domain]/$GLOBALS[subfolder]/index.php?get=register&section=activate&activation=$activation

Das Oldenburger Freifunkteam";
    $ergebniss = mail($email, "Anmeldung Freifunk Oldenburg", $text, "From: Freifunk Oldenburg Portal <portal@freifunk-ol.de>");
    $message[] = array("Eine Email mit einem Link zum aktivieren des Nutzerkontos wurde an ".$email." verschickt.", 1);
    message::setMessage($message);
    return true;
  }
  
	public function setNewPassword($password, $user_id) {
		$password_hash = md5($password);
	    $db = new mysqlClass;
    	$db->mysqlQuery("UPDATE users SET password = '$password_hash' WHERE id = '$user_id'");
	    $ergebniss = $db->mysqlAffectedRows();
    	unset($db);
    	if ($ergebniss>0) {
      		$message[] = array("Dem Benutzer mit der ID $user_id wurde ein neues Passwort gesetzt", 1);
      		message::setMessage($message);
      		return true;
    	} else {
      		$message[] = array("Dem Benutzer mit der ID ".$user_id." konnte keine neues Passwort gesetzt werden.", 2);
      		message::setMessage($message);
      	return false;
    	}
	}
	
  public function sendPassword($email, $nickname, $password) {
        
    $text = "Hallo $nickname,

Deine Logindaten Sind:
Nickname: $nickname
Passwort: $password

Das Oldenburger Freifunkteam";
    $ergebniss = mail($email, "Neues Password (Freifunk Oldenburg)", $text, "From: Freifunk Oldenburg Portal <portal@freifunk-ol.de>");
	$message[] = array("Dir wurde ein neues Passwort zugesendet.", 1);
	message::setMessage($message);
    return true;
  }

}

?>