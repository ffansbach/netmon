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

require_once 'lib/classes/extern/Zend/Mail.php';
require_once 'lib/classes/extern/Zend/Mail/Transport/Smtp.php';

class Register {
	public function insertNewUser($nickname, $password, $passwordchk, $email, $agb, $openid) {
		if (!empty($openid))
			$password = Helper::randomPassword(8);
			$passwordchk = $password;

		if ($this->checkUserData($nickname, $password, $passwordchk, $email, $agb, $openid)) {
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
			
			DB::getInstance()->exec("INSERT INTO users (nickname, password, openid, email, notification_method, permission, create_date, activated) VALUES ('$nickname', '$password', '$openid', '$email', 'email', '$permission', NOW(), '$activation');");
			$id = DB::getInstance()->lastInsertId();
			
			if($this->sendRegistrationEmail($email, $nickname, $passwordchk, $activation, time(), $openid)) {
				$message[] = array("Der Benutzer ".$nickname." wurde erfolgreich angelegt.", 1);
				Message::setMessage($message);
				return true;
			} else {
				$message[] = array("Die Email mit dem Link zum aktivieren des Nutzerkontos konnte nicht an ".$email." verschickt werden.", 2);
				$message[] = array("Der neu angelegte User mit der ID ".$id." wird wieder gelöscht.", 2);
				Message::setMessage($message);
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
				Message::setMessage($message);
				return true;
			} else {
				$message[] = array("Ein Benutzer mit dem Aktivationcode ".$activation." existiert nicht!", 2);
				Message::setMessage($message);
				return false;
			}
		} else {
			$message[] = array("Es wurde kein Aktivierungscode übergeben!", 2);
			Message::setMessage($message);
			return false;
		}
	}

	public function checkUserData($nickname, $password, $passwordchk, $email, $agb, $openid) {
		//Check if nickname is set
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
		
		if (!$openid) {
		    if (empty($password)) {
			$message[] = array("Es wurde kein Passwort angegeben.",2);
		    } elseif (empty($passwordchk)) {
			$message[] = array("Das Passwort wurde kein zweites mal eingegeben.",2);
		    } elseif ($password != $passwordchk) {
			$message[] = array("Die Passwörter stimmen nicht überein.",2);
		    }
		} else {
		    if (empty($openid)) {
			$message[] = array("Es wurde keine Open-ID angegeben.",2);
		    } else {
			//Check if nickname already exist
			try {
				$result = DB::getInstance()->query("select * from users WHERE openid='$openid'");
				if ($result->rowCount()>0)
					$message[] = array("Die Open-ID ".$openid." ist bereits mit einem Benutzer verknüpft.",2);
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		    }
		}
		
		if ($GLOBALS['enable_network_policy'] AND !$agb)
			$message[] = array("Bitte lesen und akzeptieren Sie die Netzwerkpolicy!",2);
		
		//Return
		if (isset($message) AND count($message)>0) {
			Message::setMessage($message);
			return false;
		} else {
			return true;
		}
	}
	
	public function deleteUser($id) {
		DB::getInstance()->exec("DELETE FROM users WHERE id='$id';");
		$message[] = array("Der Benutzer mit der ID ".$id." wurde gelöscht.",1);
		Message::setMessage($message);
		return true;
	}
	
	public function sendRegistrationEmail($email, $nickname, $password, $activation, $datum, $openid) {
		$text = "Hallo $nickname,

Du hast dich am ".date("d.m.Y H:i:s", $datum)." Uhr bei $GLOBALS[community_name] registriert.

Deine Logindaten sind:\n";


if($openid) {
  $text .= "Open-ID: $openid

Die Open-ID wurde mit folgendem Benutzer verknuepft: $nickname

Aus technischen Gruenden wurde fuer diesen Benutzer auch ein Passwort generiert, dass auch den Login auf herkoemlichem Weg ermoeglicht.
Das Passwort lautet: $password\n\n";
} else {
  $text .= "Nickname: $nickname
Passwort: $password\n\n";
}

$text .= "Bitte klicke auf den nachfolgenden Link um deinen Account freizuschalten.
$GLOBALS[url_to_netmon]/account_activate.php?activation_hash=$activation

Mit freundlichen Gruessen
$GLOBALS[community_name]";

if ($GLOBALS['mail_sending_type']=='smtp') {
	$config = array('username' => $GLOBALS['mail_smtp_username'],
			'password' => $GLOBALS['mail_smtp_password']);

	if(!empty($GLOBALS['mail_smtp_ssl']))
		$config['ssl'] = $GLOBALS['mail_smtp_ssl'];
	if(!empty($GLOBALS['mail_smtp_login_auth']))
		$config['auth'] = $GLOBALS['mail_smtp_login_auth'];

	$transport = new Zend_Mail_Transport_Smtp($GLOBALS['mail_smtp_server'], $config);
}

$mail = new Zend_Mail();

$mail->setFrom($GLOBALS['mail_sender_adress'], $GLOBALS['mail_sender_name']);
$mail->addTo($email);
$mail->setSubject("Anmeldung $GLOBALS[community_name]");
$mail->setBodyText($text);

$mail->send($transport);


		$message[] = array("Eine Email mit einem Link zum aktivieren des Nutzerkontos wurde an ".$email." verschickt.", 1);
		Message::setMessage($message);
		return true;
	}
  
	public function setNewPassword($new_password_hash, $old_password_hash, $user_id) {
		$user_data = Helper::getUserByID($user_id);
		if($old_password_hash==$user_data['password']) {
			$result = DB::getInstance()->exec("UPDATE users SET password = '$new_password_hash' WHERE id = '$user_id'");
			if ($result>0) {
				$message[] = array("Dem Benutzer $user_data[nickname] wurde ein neues Passwort gesetzt", 1);
				Message::setMessage($message);
				return true;
			} else {
				$message[] = array("Dem Benutzer mit der ID ".$user_id." konnte keine neues Passwort gesetzt werden.", 2);
				Message::setMessage($message);
				return false;
			}
		} else {
			$message[] = array("Der übergebene Passwordhash der User-ID ".$user_id." stimmt nicht mit dem gespeicherten Hash überein.", 2);
			$message[] = array("Es wurde kein neues Passwort gesetzt!.", 2);
			Message::setMessage($message);
			return false;
		}
	}
	
	public function sendPassword($user_id, $email, $nickname, $password, $password_md5, $oldpassword_hash) {
		$text = "Hallo $nickname,

Deine neuen Logindaten Sind:
Nickname: $nickname
Passwort: $password

Bitte bestaetige die Aenderungen mit einem Klick auf diesen Link:
$GLOBALS[url_to_netmon]/set_new_password.php?user_id=$user_id&new_passwordhash=$password_md5&oldpassword_hash=$oldpassword_hash

Mit freundlichen Gruessen
$GLOBALS[community_name]";


if ($GLOBALS['mail_sending_type']=='smtp') {
	$config = array('username' => $GLOBALS['mail_smtp_username'],
			'password' => $GLOBALS['mail_smtp_password']);

	if(!empty($GLOBALS['mail_smtp_ssl']))
		$config['ssl'] = $GLOBALS['mail_smtp_ssl'];
	if(!empty($GLOBALS['mail_smtp_login_auth']))
		$config['auth'] = $GLOBALS['mail_smtp_login_auth'];

	$transport = new Zend_Mail_Transport_Smtp($GLOBALS['mail_smtp_server'], $config);
}

$mail = new Zend_Mail();

$mail->setFrom($GLOBALS['mail_sender_adress'], $GLOBALS['mail_sender_name']);
$mail->addTo($email);
$mail->setSubject("Neues Passwort $GLOBALS[community_name]");
$mail->setBodyText($text);

$mail->send($transport);

		$message[] = array("Dir wurde ein neues Passwort zugesendet.", 1);
		Message::setMessage($message);
		return true;
	}
}

?>