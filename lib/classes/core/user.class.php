<?php

// +---------------------------------------------------------------------------+
// user.class.php
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

require_once("lib/classes/core/ipeditor.class.php");
require_once("lib/classes/core/subneteditor.class.php");
require_once("lib/classes/core/login.class.php");
require_once("lib/classes/core/router.class.php");

/**
 * This class is used as a container for static methods that deal operations
 * on user objects.
 *
 * @package	Netmon
 */
class User {
	/**
	* Insert the changes on a user into the database
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $user_id
	* @param $changepassword
	* @param $permission
	* @param $oldpassword
	* @param $newpassword
	* @param $newpasswordchk
	* @param $openid
	* @param $vorname
	* @param $nachname
	* @param $strasse
	* @param $plz
	* @param $ort
	* @param $telefon
	* @param $email
	* @param $jabber
	* @param $icq
	* @param $website
	* @param $about
	* @param $notification_method
	* @return boolean if the user was edited successfull
	*/
	public function userInsertEdit($user_id, $changepassword, $permission, $oldpassword, $newpassword, $newpasswordchk,
				       $openid, $vorname, $nachname, $strasse, $plz, $ort, $telefon, $email, $jabber,
				       $icq, $website, $about, $notification_method) {
		$user_data = User::getUserByID($user_id);
	
		$message = array();
		//check weatcher the given data is valid
		if($changepassword AND (User::getPasswordByUserID($user_id) != Usermanagement::encryptPassword($oldpassword))) {
			$message[] = array("Dein altes Passwort ist nicht richtig.",2);
		} elseif($changepassword AND empty($newpassword)) {
			$message[] = array("Du musst ein neues Passwort angeben.",2);
		} elseif($changepassword AND ($newpassword!=$newpasswordchk)) {
			$message[] = array("Deine beiden neuen Passwörter stimmen nicht überein.",2);
		} elseif(empty($email)) {
			$message[] = array("Du musst eine Emailadresse angeben.",2);
		} elseif(!User::isUniqueEmail($email, $user_id)) {
			$message[] = array("Es existiert bereits ein Benutzer mit der ausgewhälten Emailadresse <i>$email</i>.",2);
		} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$message[] = array("Die ausgewählte Emailadresse ".$email." ist keine gültige Emailadresse.",2);
		} elseif(!empty($openid) AND !User::isUniqueOpenID($openid, $user_id)) {
			$message[] = array("Die ausgewählte OpenID <i>".$openid."</i> ist bereits mit einem Benutzer verknüpft.",2);
		}
		
		//if the user data is not valid, return false
		if (count($message)>0) {
			Message::setMessage($message);
			return false;
		}
		
		//if user wants to set a new password, encrypt new password
		if ($changepassword)
			$newpassword = Usermanagement::encryptPassword($newpassword);
		else
			$newpassword = $user_data['password'];
	
		if (!$permission) {
				$newpermission = $user_data['permission'];
		} else {
			$newpermission=0;
			foreach($permission as $dual) {
				$newpermission += $dual;
			}
		}
		
		//if all checks are okay, update the data into the database
		$stmt = DB::getInstance()->prepare("UPDATE users SET 
							   permission = ?, password = ?, openid = ?, vorname = ?, nachname = ?,
							   strasse = ?, plz = ?, ort = ?, telefon = ?, email = ?, jabber = ?,
							   icq = ?, website = ?, about = ?, notification_method = ?
						    WHERE id = ?");
		$stmt->execute(array($newpermission, $newpassword, $openid, $vorname, $nachname, $strasse, $plz, $ort, $telefon, $email, $jabber, $icq, $website, $about, $notification_method, $user_id));

		$message[] = array("Die Daten von $user_data[nickname] wurden geändert", 1);
		message::setMessage($message);
		return true;
	}
	
	/**
	* Checks weather a given emailaddress is already used by another user
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $email emailaddress to check
	* @param $user_id optional, user to exclude from check (used to check the data in the user edit method)
	* @return boolean true if the email is unique
	*/
	public function isUniqueEmail($email, $user_id=false) {
		try {
			if(!$user_id) {
				$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE email=?");
				$stmt->execute(array($email));
			} else {
				$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE email=? AND id!=?");
				$stmt->execute(array($email, $user_id));
			}
			return !$stmt->rowCount();
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	* Checks weather a given nickname is already used by another user
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $nickname nickname to check
	* @param $user_id optional, user to exclude from check (used to check the data in the user edit method)
	* @return boolean true if the nickname is unique
	*/
	public function isUniqueNickname($nickname, $user_id=false) {
		try {
			if(!$user_id) {
				$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE nickname=?");
				$stmt->execute(array($nickname));
			} else {
				$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE nickname=? AND id!=?");
				$stmt->execute(array($nickname, $user_id));
			}
			return !$stmt->rowCount();
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	* Checks weather a given openid is already used by another user
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $openid openid to check
	* @param $user_id optional, user to exclude from check (used to check the data in the user edit method)
	* @return boolean true if the openid is unique
	*/
	public function isUniqueOpenID($openid, $user_id=false) {
		try {
			if(!$user_id) {
				$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE openid=?");
				$stmt->execute(array($openid));
			} else {
				$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE openid=? AND id!=?");
				$stmt->execute(array($openid, $user_id));
			}
			return !$stmt->rowCount();
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	* Deletes a user and all of the objects he owns
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $user_id the id of the user to delete
	*/
	public function userDelete($user_id) {
		$user_data = User::getUserByID($user_id);
	
		//Delete routers (and with the routers you delete interfaces, ips and services of the user)
		foreach(Router::getRouterListByUserId($user_id) as $router) {
			RouterEditor::insertDeleteRouter($router['router_id']);
		}
		
		//Delete ip´s, serices and subnets
		foreach(Project::getProjectsByUserId($user_id) as $project) {
			//TODO: implemet ProjectEditor::deleteProject($project_id)
			//ProjectEditor::deleteProject($project['id']);
		}
		
		//Logout the user before deleting him to get rid of session information an coockies
		Login::user_logout();
		
		//delete the user from the database
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM users WHERE id=?");
			$stmt->execute(array($user_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			return false;
		}
		
		$message[] = array("Der Benutzer ".$user_data['nickname']." wurde gelöscht.",1);
		message::setMessage($message);
		return true;
	}

	/**
	* Fetches a user by a given id from the database.
	* @author  Clemens John <clemens-john@gmx.de>
	* @param int $id Id of a user
	* @return array() Array containing the user data
	*/
	function getUserByID($id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE id=?");
			$stmt->execute(array($id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $rows;
	}

	/**
	* Fetches a user by a given email from the database.
	* @author  Clemens John <clemens-john@gmx.de>
	* @param string $email Email of a user
	* @return array() Array containing the user data
	*/
	public function getUserByEmail($email) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM  users WHERE email=?");
			$stmt->execute(array($email));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
		};
		return $rows;
	}

	/**
	* Fetches a user by a given email from the database.
	* @author  Clemens John <clemens-john@gmx.de>
	* @param string $openid OpenID of a user
	* @return array() Array containing the user data
	*/
	public function getUserByOpenID($openid) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM  users WHERE openid=?");
			$stmt->execute(array($openid));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
		  echo $e->getMessage();
		};
		return $rows;
	}
	
	/**
	* Fetches a user by a given nickname and the appropriate password.
	* @author  Clemens John <clemens-john@gmx.de>
	* @param string $nickname nickname of a user
	* @param string $password plain text password of the user
	* @param boolean $hashed true if the given password is already hashed with UserManagement::encryptPassword();
	*		 false if it is plain text
	* @return array() Array containing the user data
	*/
	public function getUserByNicknameAndPassword($nickname, $password, $hashed=false) {
		//hash the password if it is not already hashed
		if(!$hashed)
			$password = UserManagement::encryptPassword($password);
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM  users WHERE nickname=? AND password=?");
			$stmt->execute(array($nickname, $password));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
		  echo $e->getMessage();
		};
		return $rows;
	}
	
	/**
	* Get the password of a user
	* @author  Clemens John <clemens-john@gmx.de>
	* @param string $user_id id of the user you want to get the password from
	* @return string the password of the user
	*/
	public function getPasswordByUserID($user_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT password FROM  users WHERE id=?");
			$stmt->execute(array($user_id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
		  echo $e->getMessage();
		};
		return $rows['password'];
	}
}

?>