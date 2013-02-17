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
	public function user_login ($nickname, $password, $remember=false, $remembered=false, $openid=false) {
		if ((empty($nickname) or empty($password)) AND $openid==false) {
			$messages[] = array("Sie müssen einen Nickname und ein Passwort angeben um sich einzuloggen", 2);
			Message::setMessage($messages);
			return false;
		} else {
			if(!$remembered)
				$password = usermanagement::encryptPassword($password);

			try {
				if ($openid!=false) {
					$sql = "select id , nickname, activated, permission from users WHERE openid='$openid'";
				} else {
					$sql = "select id , nickname, activated, permission from users WHERE nickname='$nickname' and password='$password'";
				}
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
					DB::getInstance()->query("UPDATE users SET session_id='$session_id' where id=".$user_data['id']);
					$_SESSION['user_id'] = $user_data['id'];
					
					$messages[] = array("Herzlich willkommen ".$user_data['nickname'], 1);
					Message::setMessage($messages);

					//Autologin (remember me)
					if($remember AND empty($openid)) {
						setcookie ("nickname", $nickname, time() + 60*60*24*14);
						setcookie ("password_hash", $password, time() + 60*60*24*14);
					} elseif($remember AND !empty($openid)) {
						setcookie ("openid", $openid, time() + 60*60*24*14);
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
			$_COOKIE = array();
			setcookie("nickname", "", time() - 60*60*24*14);
			setcookie("password_hash", "", time() - 60*60*24*14);
			setcookie("openid", "", time() - 60*60*24*14);
			setcookie(session_name(), '', time()-3600,'/');
			$messages[] = array("Sie wurden ausgeloggt und ihre Benutzersession wurde gelöscht!", 1);
			Message::setMessage($messages);
			return true;
		}
	}
}

?>