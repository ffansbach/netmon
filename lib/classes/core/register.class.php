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
			
			//Give the first user that registers full Root access (User+Mod+Admin+Root; 2^3+2^4+2^5+2^6=120) 
			//Give further users only user permission (User; 2^3=8=User) 
			try {
				$result = DB::getInstance()->query("select * from users");
				if ($result->rowCount()<=0)
					$permission = 120;
				else
					$permission = 8;
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			DB::getInstance()->exec("INSERT INTO users (nickname, password, email, permission, create_date, activated) VALUES ('$nickname', '$password', '$email', '$permission', NOW(), '$activation');");
			$id = DB::getInstance()->lastInsertId();
			
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
		}
	}

	public function userActivate($activation) {
		if (isset($activation)) {
			$result = DB::getInstance()->query("SELECT nickname from users WHERE activated='$activation';");
			$user_data = $result->fetch(PDO::FETCH_ASSOC);

			$result = DB::getInstance()->exec("UPDATE users SET activated=0 WHERE activated='$activation';");
			if ($result>0) {
				$message[] = array("Der Benutzer ".$user_data['nickname']." wurde erfolgreich aktiviert.", 1);
				$message[] = array("Sie können sich jetzt mit Ihrem Benutzernamen und Ihrem Passwort einloggen", 1);
				message::setMessage($message);
				return true;
			} else {
				$message[] = array("Ein Benutzer mit dem Aktivationcode ".$activation." existiert nicht!", 2);
				message::setMessage($message);
				return false;
			}
		} else {
			$message[] = array("Es wurde kein Aktivierungscode übergeben!", 2);
			message::setMessage($message);
			return false;
		}
	}

	public function checkUserData($nickname, $password, $passwordchk, $email, $agb) {
		//Prüfen ob Nickname gesetzt ist
		if (empty($nickname)) {
			$message[] = array("Es wurde kein Nickname angegeben.",2);
		} else {
			//Check if nickname already exist
			try {
				$result = DB::getInstance()->query("select * from users WHERE nickname='$nickname'");
				if ($result->rowCount()>0)
					$message[] = array("Ein Benutzer mit dem Namen ".$nickname." existiert bereits.",2);
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
		
		if (empty($email)) {
			$message[] = array("Es wurde keine Emailadresse angegeben.",2);
		} else {
			//Check if mailadress already exist
			try {
				$result = DB::getInstance()->query("select * from users WHERE email='$email'");
				if ($result->rowCount()>0) {
					$message[] = array("Ein Benutzer mit der Emailadresse ".$email." existiert bereits.",2);
				} else {
					//Check if systax of adress is correct
					$syntax = true;
					if (!$syntax)
						$message[] = array("Die Emailadresse ".$email." ist syntaktisch falsch.",2);
				}
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
		
		if (empty($password)) {
			$message[] = array("Es wurde kein Passwort angegeben.",2);
		} elseif (empty($passwordchk)) {
			$message[] = array("Das Passwort wurde kein zweites mal eingegeben.",2);
		} elseif ($password != $passwordchk) {
			$message[] = array("Die Passwörter stimmen nicht überein.",2);
		}
		
		if (!$agb)
			$message[] = array("Bitte lesen und akzeptieren Sie die Netzwerkpolicy!",2);
		
		//Rückgabe
		if (isset($message) AND count($message)>0) {
			message::setMessage($message);
			return false;
		} else {
			return true;
		}
	}
	
	public function deleteUser($id) {
		DB::getInstance()->exec("DELETE FROM users WHERE id='$id';");
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
http://$GLOBALS[domain]/$GLOBALS[subfolder]/account_activate.php?activation_hash=$activation

Dein Freifunkteam $GLOBALS[city_name]";
		$ergebniss = mail($email, "Anmeldung Freifunk Oldenburg", $text, "From: $GLOBALS[mail_sender]");
		$message[] = array("Eine Email mit einem Link zum aktivieren des Nutzerkontos wurde an ".$email." verschickt.", 1);
		message::setMessage($message);
		return true;
	}
  
	public function setNewPassword($password, $user_id) {
		$password_hash = md5($password);
		$result = DB::getInstance()->exec("UPDATE users SET password = '$password_hash' WHERE id = '$user_id'");
		if ($result>0) {
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

Dein Freifunkteam $GLOBALS[city_name]";
		$ergebniss = mail($email, "Neues Password (Freifunk Oldenburg)", $text, "From: Freifunk Oldenburg Portal <portal@freifunk-ol.de>");
		$message[] = array("Dir wurde ein neues Passwort zugesendet.", 1);
		message::setMessage($message);
		return true;
	}
}

?>