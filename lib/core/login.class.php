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

require_once(ROOT_DIR.'/lib/core/user_old.class.php');
require_once(ROOT_DIR.'/lib/core/UserRememberMeList.class.php');
require_once(ROOT_DIR.'/lib/extern/phpass/PasswordHash.php');

/**
 * This class is used as a container for static methods that can login and logout a user
 *
 * @package	Netmon
 */
class Login {
	/**
	* Logs out a user and resets the complete session
	* @author  Clemens John <clemens-john@gmx.de>
	* @return boolean true if the logout was successful
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

			//delete all Remember-Mes from the database (TODO: this could be improved by storing
			//the current session id along with the remember me and then delete only the remember me
			//coresponding to the current session.
			$user_remember_me_list = new UserRememberMeList($_SESSION['user_id']);
			$user_remember_me_list->delete();

			unset($_SESSION);
			unset($_COOKIE);
			setcookie("remember_me", "", time() - 60*60*24*14);
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
