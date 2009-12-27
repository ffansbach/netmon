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

class Login {
	public function user_login ($nickname, $password, $remember=false, $remembered=false) {
		if (empty($nickname) or empty($password)) {
			$messages[] = array("Sie müssen einen Nickname und ein Passwort angeben um sich einzuloggen", 2);
			Message::setMessage($messages);
			return false;
		} else {
			if(!$remembered)
				$password = usermanagement::encryptPassword($password);

			try {
				$sql = "select id , nickname, activated, permission, DATE_FORMAT(last_login, '%D %M %Y um %H:%i:%s Uhr') as last_login from users WHERE nickname='$nickname' and password='$password'";
				$result = DB::getInstance()->query($sql);
				$user_data = $result->fetch(PDO::FETCH_ASSOC);
				$login = $result->rowCount();
				if ($login<1) {
					$messages[] = array("Der Benutzername existiert nicht oder das Passwort ist falsch!", 2);
					Message::setMessage($messages);
					return false;
				} elseif ($user_data['activated'] != '0') {
					$messages[] = array("Der Benutzername wurde noch nicht aktiviert!", 2);
					Message::setMessage($messages);
					return false;
				} else {
					$session_id = session_id();
					DB::getInstance()->query("UPDATE users SET session_id='$session_id', last_login = NOW() where id=".$user_data['id']);
					$_SESSION['user_id'] = $user_data['id'];
					if (isset($user_data['last_login'])) {
						$last_login = " Ihr letzter Login war am ".$user_data['last_login'];
					}
					
					$messages[] = array("Herzlich willkommen zurück ".$user_data['nickname'].$last_login, 1);
					Message::setMessage($messages);

					//Autologin (remember me)
					if($remember) {
						// Setzen des Verfalls-Zeitpunktes auf 1 Stunde in der Vergangenheit
						setcookie ("nickname", $nickname, time() + 3000000);
						setcookie ("password_hash", $password, time() + 3000000);
					}

					return array('result'=>true, 'user_id'=>$user_data['id'], 'permission'=>$user_data['permission'], 'session_id'=>$session_id);
				}
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
	}
	
	public function user_logout() {
		if (!isset($_SESSION['user_id'])) {
			$messages[] = array("Sie können sich nicht ausloggen, wenn Sie nicht eingeloggt sind", 2);
			Message::setMessage($messages);
			return false;
		} else {
			$_SESSION = array();
			setcookie("nickname", "", time() - 3000000);
			setcookie("password_hash", "", time() - 3000000);

			$messages[] = array("Sie wurden ausgeloggt und ihre Benutzersession wurde gelöscht!", 1);
			Message::setMessage($messages);
			return true;
		}
	}
}

?>