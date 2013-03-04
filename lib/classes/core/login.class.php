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
 * This class is used as a container for static methods that can login and logout a user
 *
 * @package	Netmon
 */
class Login {
	/**
	* Logs in a user by nickname and password, by openid or by remember me coockie
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $nickname the nickname of the user that wants to login (false on openid login)
	* @param $password the password of the user that wants to login (false on openid login). If the password is
	*		   plain text, set $remembered to false. If the password is already hashed with Usermanagement::encryptPassword()
	*		   set $remembered true.
	* @param $remember boolean true if the user wants to be logged in automatically by coockie the next time	
	* @param $remembered boolean true if the user if beeing logged in automatically by coockie.
	*		     Sets the mode of the password (plain text or already hashed)			    
	* @param $openid string openid of the user if it logs in by openid
	* @return array() some variables that are used by some methods in the api
	*/
	public function user_login($nickname, $password, $remember=false, $remembered=false, $openid=false) {
		if ((empty($nickname) or empty($password)) AND $openid==false) {
			$messages[] = array("Sie müssen einen Nickname und ein Passwort angeben um sich einzuloggen", 2);
			Message::setMessage($messages);
			return false;
		} elseif ($openid!=false) {
			$user_data = User::getUserByOpenID($openid);
		} else {
			$user_data = User::getUserByNicknameAndPassword($nickname, $password, $remembered);
		}
		
		if (empty($user_data)) {
			$messages[] = array("Der Benutzername existiert nicht oder das Passwort ist falsch!", 2);
			Message::setMessage($messages);
			return false;
		} elseif ($user_data['activated'] != '0') {
			$messages[] = array("Der Benutzername wurde noch nicht aktiviert!", 2);
			Message::setMessage($messages);
			return false;
		} else {
			$stmt = DB::getInstance()->prepare("UPDATE users SET session_id = ? WHERE id = ?");
			$stmt->execute(array(session_id(), $user_data['id']));
			
			$_SESSION['user_id'] = $user_data['id'];
			
			//Autologin (remember me)
			if($remember AND empty($openid)) {
				setcookie ("nickname", $nickname, time() + 60*60*24*14);
				setcookie ("password_hash", $password, time() + 60*60*24*14);
			} elseif($remember AND !empty($openid)) {
				setcookie ("openid", $openid, time() + 60*60*24*14);
			}

			$messages[] = array("Herzlich willkommen ".$user_data['nickname'], 1);
			Message::setMessage($messages);
			return array('result'=>true, 'user_id'=>$user_data['id'], 'permission'=>$user_data['permission'], 'session_id'=>session_id());
		}
	}
	
	/**
	* Logs out a user and resets the complete session
	* @author  Clemens John <clemens-john@gmx.de>
	* @return boolean true if the logout was successfull
	*/
	public function user_logout() {
		if (!isset($_SESSION['user_id'])) {
			$messages[] = array("Sie können sich nicht ausloggen, wenn Sie nicht eingeloggt sind", 2);
			Message::setMessage($messages);
			return false;
		} else {
			//destroy current session
			//to correctly destroy a session look at http://php.net/manual/de/function.session-destroy.php
			$stmt = DB::getInstance()->prepare("UPDATE users SET session_id = ? WHERE id = ?");
			$stmt->execute(array('', $_SESSION['user_id']));
			
			unset($_SESSION);
			unset($_COOKIE);
			setcookie("nickname", "", time() - 60*60*24*14);
			setcookie("password_hash", "", time() - 60*60*24*14);
			setcookie("openid", "", time() - 60*60*24*14);
			setcookie(session_name(), '', time()-3600,'/');
			
			if (ini_get("session.use_cookies")) {
				$params = session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000, $params["path"],
					  $params["domain"], $params["secure"], $params["httponly"]);
			}
			session_destroy();
			
			session_start();
			$messages[] = array("Sie wurden ausgeloggt und ihre Benutzersession wurde gelöscht!", 1);
			Message::setMessage($messages);
			return true;
		}
	}
}

?>