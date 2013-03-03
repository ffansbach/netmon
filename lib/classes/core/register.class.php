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
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

require_once("lib/classes/core/login.class.php");
require_once("lib/classes/core/user.class.php");
require_once('lib/classes/extern/Zend/Mail.php');
require_once('lib/classes/extern/Zend/Mail/Transport/Smtp.php');

class Register {
	/**
	* Create a new user
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $nickname nickname of the user
	* @param $password plain text password of the user
	* @param $passwordchk plain text password doublecheck
	* @param $email emailaddress of the user
	* @param $agb has the user accepted the agb (is only beeing checked if the agb is enabled)?
	* @param $openid openid if the user wants to register by openid
	* @return boolean true if the was created successfull
	*/
	public function insertNewUser($nickname, $password, $passwordchk, $email, $agb, $openid) {
		$message = array();
		//check weatcher the given data is valid
		if(empty($nickname)) {
			$message[] = array("Du musst einen Nickname angeben.",2);
		} elseif(!User::isUniqueNickname($nickname)) {
			$message[] = array("Der ausgewählte Nickname <i>$nickname</i> existiert bereits.",2);
		} elseif(empty($password)) {
			$message[] = array("Du musst ein Passwort angeben.",2);
		} elseif($password!=$passwordchk) {
			$message[] = array("Deine beiden Passwörter stimmen nicht überein.",2);
		} elseif(empty($email)) {
			$message[] = array("Du musst eine Emailadresse angeben.",2);
		} elseif(!User::isUniqueEmail($email)) {
			$message[] = array("Es existiert bereits ein Benutzer mit der ausgewhälten Emailadresse <i>$email</i>.",2);
		} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$message[] = array("Die ausgewählte Emailadresse ".$email." ist keine gültige Emailadresse.",2);
		} elseif(!empty($openid) AND !isUniqueOpenID($openid)) {
			$message[] = array("Die ausgewählte OpenID <i>".$openid."</i> ist bereits mit einem Benutzer verknüpft.",2);
		} elseif($GLOBALS['enable_network_policy']=="true" AND !$agb) {
			$message[] = array("Du musst die Netzwerkpolicy akzeptieren.",2);
		}
		
		if (count($message)>0) {
			Message::setMessage($message);
			return false;
		} else {
			//the first user that registers will get root access
			try {
				$stmt = DB::getInstance()->prepare("SELECT * FROM users");
				$stmt->execute();
				if(!$stmt->rowCount())
					$permission = 120;
				else
					$permission = 8;
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			//hash password to store in db
			$password = Usermanagement::encryptPassword($password);
			//create acivation hash
			$activation = Helper::randomPassword(8);
			
			if(empty($openid))
				$openid = "";
			
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO users (nickname, password, openid, email, notification_method, permission, create_date, activated) VALUES (?, ?, ?, ?, 'email', ?, NOW(), ?)");
				$stmt->execute(array($nickname, $password, $openid, $email, $permission, $activation));
				$user_id = DB::getInstance()->lastInsertId();
			} catch(PDOException $e) {
				$message[] = array("Beim erstellen des Datenbankeintrags ist ein Fehler aufgetreten.", 2);
				$message[] = array($e->getMessage(), 2);
				Message::setMessage($message);
				return false;
			}
		
			if(Register::sendRegistrationEmail($email, $nickname, $passwordchk, $activation, time(), $openid)) {
				$message[] = array("Dein Benutzer ".$nickname." wurde erfolgreich angelegt.", 1);
				Message::setMessage($message);
				return true;
			} else {
				$message[] = array("Die Email mit dem Link zum aktivieren des Nutzerkontos konnte nicht an ".$email." verschickt werden.", 2);
				$message[] = array("Der neu angelegte User ".$nickname." wird wieder gelöscht.", 2);
				Message::setMessage($message);
				User::userDelete($user_id);
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
	
	public function sendRegistrationEmail($email, $nickname, $password, $activation, $datum, $openid) {
		$text = "Hallo $nickname,\n";
		$text .= "Du hast dich am ".date("d.m.Y H:i:s", $datum)." Uhr bei $GLOBALS[community_name] registriert.\n\n";
		$text .= "Deine Logindaten sind:\n";
		if($openid) {
			$text .= "Open-ID: $openid\n\n";
			$text .= "Die Open-ID wurde mit folgendem Benutzer verknuepft: $nickname\n\n";
			$text .= "Aus technischen Gruenden wurde fuer diesen Benutzer auch ein Passwort generiert, dass auch den Login auf herkoemlichem Weg ermoeglicht.\n";
			$test .= "Das Passwort lautet: $password\n\n";
		} else {
			$text .= "Nickname: $nickname\n";
			$text .= "Passwort: $password\n\n";
		}
		$text .= "Bitte klicke auf den nachfolgenden Link um deinen Account freizuschalten.\n";
		$text .= "$GLOBALS[url_to_netmon]/account_activate.php?activation_hash=$activation\n\n";
		$text .= "Mit freundlichen Gruessen\n";
		$text .= "$GLOBALS[community_name]";
		
		if ($GLOBALS['mail_sending_type']=='smtp') {
			$config = array('username' => $GLOBALS['mail_smtp_username'],
					'password' => $GLOBALS['mail_smtp_password']);
			if(!empty($GLOBALS['mail_smtp_ssl']))
				$config['ssl'] = $GLOBALS['mail_smtp_ssl'];
			if(!empty($GLOBALS['mail_smtp_login_auth']))
				$config['auth'] = $GLOBALS['mail_smtp_login_auth'];
			try {
				$transport = new Zend_Mail_Transport_Smtp($GLOBALS['mail_smtp_server'], $config);
			} catch(Exception $e) {
				echo $e->getMessage();
				return false;
			}
		}
		try {
			$mail = new Zend_Mail();
			$mail->setFrom($GLOBALS['mail_sender_adress'], $GLOBALS['mail_sender_name']);
			$mail->addTo($email);
			$mail->setSubject("Anmeldung $GLOBALS[community_name]");
			$mail->setBodyText($text);
			$mail->send($transport);
		} catch(Exception $e) {
			echo $e->getMessage();
			return false;
		}
			
		$message[] = array("Eine Email mit einem Link zum aktivieren des Nutzerkontos wurde an ".$email." verschickt.", 1);
		Message::setMessage($message);
		return true;
	}
  
	public function setNewPassword($new_password_hash, $old_password_hash, $user_id) {
		$user_data = User::getUserByID($user_id);
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