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

require_once("lib/core/login.class.php");
require_once("lib/core/user_old.class.php");
require_once("lib/core/ConfigLine.class.php");
require_once('lib/extern/Zend/Mail.php');
require_once('lib/extern/Zend/Mail/Transport/Smtp.php');
require_once('lib/extern/phpass/PasswordHash.php');

/**
 * This class is used as a container for static methods that deal operations during
 * the registration process and possible complications
 *
 * @package	Netmon
 */
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
		} elseif(!User_old::isUniqueNickname($nickname)) {
			$message[] = array("Der ausgewählte Nickname <i>$nickname</i> existiert bereits.",2);
		} elseif(empty($password)) {
			$message[] = array("Du musst ein Passwort angeben.",2);
		} elseif($password!=$passwordchk) {
			$message[] = array("Deine beiden Passwörter stimmen nicht überein.",2);
		} elseif(strlen($password) > 72) {
			$message[] = array("Dein Passwort darf nicht länger als 72 Zeichen sein.",2);
		} elseif(empty($email)) {
			$message[] = array("Du musst eine Emailadresse angeben.",2);
		} elseif(!User_old::isUniqueEmail($email)) {
			$message[] = array("Es existiert bereits ein Benutzer mit der ausgewhälten Emailadresse <i>$email</i>.",2);
		} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$message[] = array("Die ausgewählte Emailadresse ".$email." ist keine gültige Emailadresse.",2);
		} elseif(!empty($openid) AND !User_old::isUniqueOpenID($openid)) {
			$message[] = array("Die ausgewählte OpenID <i>".$openid."</i> ist bereits mit einem Benutzer verknüpft.",2);
		} elseif(ConfigLine::configByName('enable_network_policy')=="true" AND !$agb) {
			$message[] = array("Du musst die Netzwerkpolicy akzeptieren.",2);
		} elseif(!preg_match('/^([a-zA-Z0-9_\.-])+$/i', $nickname)) {
			$message[] = array("Dein Nickname enthält nicht erlaubte Zeichen (erlaubt sind Buchstaben, Ziffern, Unterstrich, Minus und Punkt).",2);
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
			$phpass = new PasswordHash(8, false);
			$password_hash = $phpass->HashPassword($password);
			
			if (strlen($password_hash) < 20) {
				$message[] = array("Beim Hashen des Passworts trat ein Fehler auf.",2);
				Message::setMessage($message);
				return false;
			}
			
			//create acivation hash
			$activation = Helper::randomPassword(8);
			do {
				$api_key = Helper::randomPassword(32);
				$is_unique_api_key = User_old::isUniqueApiKey($api_key);
			} while(!$is_unique_api_key);
			echo $api_key;
			
			if(empty($openid))
				$openid = "";
			
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO users (nickname, password, openid, api_key, email, notification_method, permission, create_date, activated) VALUES (?, ?, ?, ?, ?, 'email', ?, NOW(), ?)");
				$stmt->execute(array($nickname, $password_hash, $openid, $api_key, $email, $permission, $activation));
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
				User_old::userDelete($user_id);
				return false;
			}
		}
	}

	/**
	* Activates a new registered user
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $activation the activation hash the user got by mail
	* @return boolean true if the user has been activated successfull
	*/
	public function userActivate($activation) {
		if (isset($activation)) {
			//get the nickname of the user that will be activated
			$stmt = DB::getInstance()->prepare("SELECT nickname from users WHERE activated=?");
			$stmt->execute(array($activation));
			$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

			//activate the user
			$stmt = DB::getInstance()->prepare("UPDATE users SET activated=0 WHERE activated=?");
			$stmt->execute(array($activation));
			if ($stmt->rowCount()) {			
				$message[] = array("Der Benutzer ".$user_data['nickname']." wurde erfolgreich aktiviert.", 1);
				$message[] = array("Sie können sich jetzt mit Ihrem Benutzernamen und Ihrem Passwort einloggen", 1);
				Message::setMessage($message);
				return true;
			} else {
				$message[] = array("Ein Benutzer mit dem Aktivierungscode ".$activation." existiert nicht!", 2);
				Message::setMessage($message);
				return false;
			}
		} else {
			$message[] = array("Es wurde kein Aktivierungscode übergeben.", 2);
			Message::setMessage($message);
			return false;
		}
	}
	
	/**
	* Activates a new registered user
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $activation the activation hash the user got by mail
	* @return boolean true if the user has been activated successfull
	*/
	public function sendRegistrationEmail($email, $nickname, $password, $activation, $datum, $openid) {
		$text = "Hallo $nickname,\n\n";
		$text .= "Du hast dich am ".date("d.m.Y", $datum)." um ".date("H:i", $datum)." Uhr bei ".ConfigLine::configByName('community_name')." registriert.\n\n";

		if($openid) {
			$text .= "Deine Open-ID zum Login lautet: $openid\n";
			$text .= "Die Open-ID wurde mit folgendem Benutzer verknuepft: $nickname\n\n";
		}
		
		$text .= "Bitte klicke auf den nachfolgenden Link um deinen Account freizuschalten.\n";
		$text .= ConfigLine::configByName('url_to_netmon')."/account_activate.php?activation_hash=".urlencode($activation)."\n\n";
		$text .= "Liebe Gruesse\n";
		$text .= ConfigLine::configByName('community_name');
		
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
			$mail->setSubject("Anmeldung ".ConfigLine::configByName('community_name'));
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
	
	/**
	* Sets a new password for a user that forgot his password and requested a new password by mail
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $new_password_hash the hash of the new password. This hash was sent to the user
	*			    by mail previously and the user sets this hash by clicking on
	*			    the link in the email
	* @param $old_password_hash the hash of the old password. This hash was sent to the user
	*			    by mail previously and is used to check if the user is permitted to
	*			    set this user a new password
	* @param $user_id id of the user that wants to set a new password
	* @return boolean true if the password was changed successfull
	*/
	public function setNewPassword($new_password_hash, $old_password_hash, $user_id) {
		$new_password_hash = urldecode($new_password_hash);
		$old_password_hash = urldecode($old_password_hash);
		$user_data = User_old::getUserByID($user_id);
		if($old_password_hash==$user_data['password']) {
			$stmt = DB::getInstance()->prepare("UPDATE users SET password = ? WHERE id = ?");
			$stmt->execute(array($new_password_hash, $user_id));
		
			if ($stmt->rowCount()) {
				$message[] = array("Dem Benutzer $user_data[nickname] wurde ein neues Passwort gesetzt", 1);
				Message::setMessage($message);
				return true;
			} else {
				$message[] = array("Dem Benutzer $user_data[nickname] konnte keine neues Passwort gesetzt werden.", 2);
				Message::setMessage($message);
				return false;
			}
		} else {
			$message[] = array("Der übergebene Passwordhash des Benutzers $user_data[nickname] stimmt nicht mit dem gespeicherten Hash überein.", 2);
			$message[] = array("Es wurde kein neues Passwort gesetzt.", 2);
			Message::setMessage($message);
			return false;
		}
	}
	
	/**
	* Sends a mail to a user who forgot his password with a new password and a link that lets the user set this new password
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $user_id id of the user that wants to set a new password
	* @param $email email of the user that wants to set a new password
	* @param $nickname nickname of the user that wants to set a new password
	* @param $newpassword new plain text password for the user
	* @param $new_password_hash the hash of the new password. This hash is used to generate a link wich makes
	*			    the user able to set this hash for his new password
	* @param $old_password_hash the hash of the old password. This hash is used to generate a link wich makes
	*			    the user able to set this hash for his new password
	* @return boolean true if the mail was sent successfull
	*/
	public function sendPassword($user_id, $email, $nickname, $newpassword, $new_password_hash, $old_password_hash) {
		$text = "Hallo $nickname,\n\n";
		$text .= "du hast ein neues Passwort fuer die Netmon-Installation von ".ConfigLine::configByName('community_name')." angefordert. Deine neuen Logindaten lauten:\n\n";
		$text .= "Nickname: $nickname\n";
		$text .= "Passwort: $newpassword\n\n";
		$text .= "Bitte bestaetige die Aenderungen mit einem Klick auf diesen Link:\n";
		$text .= ConfigLine::configByName('url_to_netmon')."/set_new_password.php?user_id=$user_id&new_passwordhash=".urlencode($new_password_hash)."&oldpassword_hash=".urlencode($old_password_hash)."\n\n";
		$text .= "Hinweis: sollte das Anklicken des Links nicht funktionieren musst du den link vollstaendig in die Adressleiste deines Webbrowsers kopieren. Insbesondere, wenn sich ein Punkt am Ende des Links befindet, da dieser mit zum Link gehoert!\n\n";
		$text .= "Liebe Gruesse\n";
		$text .= ConfigLine::configByName('community_name');
		
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
		$mail->setSubject("Neues Netmon Passwort fuer ".ConfigLine::configByName('community_name'));
		$mail->setBodyText($text);
		$mail->send($transport);

		$message[] = array("Dir wurde ein neues Passwort zugesendet.", 1);
		Message::setMessage($message);
		return true;
	}
}

?>