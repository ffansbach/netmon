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

require_once("./lib/classes/core/ipeditor.class.php");
require_once("./lib/classes/core/subneteditor.class.php");
require_once("./lib/classes/core/login.class.php");

/**
 * This file contains the class to get the for the user site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class User {
	function userInsertEdit() {
		if ($this->checkUserEditData($_GET['id'], $_POST['changepassword'], $_POST['oldpassword'], $_POST['newpassword'], $_POST['newpasswordchk'], $_POST['email'])) {
			$user = Helper::getUserByID($_GET['id']);
			if (!$_POST['changepassword']) {
				$password = $user['password'];
			} else {
				$password = usermanagement::encryptPassword($_POST['newpassword']);
			}

			if (!$_POST['permission']) {
				$permission = $user['permission'];
			} else {
				$permission=0;
				foreach($_POST['permission'] as $dual) {
					$permission = $permission+$dual;
				}
			}

			$sqlinsert = "UPDATE users
						  SET 
							  permission = '$permission',
							  password = '$password',
							  vorname = '$_POST[vorname]',
							  nachname = '$_POST[nachname]',
							  strasse = '$_POST[strasse]',
							  plz = '$_POST[plz]',
							  ort = '$_POST[ort]',
							  telefon = '$_POST[telefon]',
							  email = '$_POST[email]',
							  jabber = '$_POST[jabber]',
							  icq = '$_POST[icq]',
							  website = '$_POST[website]',
							  about = '$_POST[about]',
							  notification_method = '$_POST[notification_method]'
							  WHERE id = '$_GET[id]'";
			DB::getInstance()->exec($sqlinsert);
			
			$message[] = array("Die Daten von $user[nickname] wurden geändert", 1);
			message::setMessage($message);
			return true;
		} else {
			return false;
		}
	}

	public function checkUserEditData($user_id, $changepassword, $oldpassword, $newpassword, $newpasswordchk, $email) {
		if($changepassword) {
			try {
				$sql = "SELECT password from users WHERE id='$user_id';";
				$result = DB::getInstance()->query($sql);
				$dbpassword = $result->fetch(PDO::FETCH_ASSOC);
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			if ($dbpassword['password'] == usermanagement::encryptPassword($oldpassword)) {
				//Check if password is set.
				if (empty($newpassword)) {
					$message[] = array("Es wurde kein Passwort angegeben.",2);
				} elseif (empty($newpasswordchk)) {
					$message[] = array("Das Passwort wurde kein zweites mal eingegeben.",2);
				} elseif ($newpassword !== $newpasswordchk) {
					$message[] = array("Die neuen Passwörter stimmen nicht überein.", 2);
				}
			} else {
				$message[] = array("Das alte Passwort ist Falsch.", 2);
			}
		}
		
		//Check if email is set.
		if (!isset($email) OR $email == "") {
			$message[] = array("Es wurde keine Emailadresse angegeben.",2);
		} else {
			//Check if Emailadress already exist.
			try {
				$sql = "select * from users WHERE email='$email' AND id!='$user_id'";
				$result = DB::getInstance()->query($sql);
				$emailcheck = $result->rowCount();
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}

			if ($emailcheck>0) {
				$message[] = array("Ein Benutzer mit der Emailadresse ".$email." existiert bereits.",2);
			} else {
				//Check if the syntax of the adress is correct
				$syntax = true;
				if (!$syntax) {
					$message[] = array("Die Emailadresse ".$email." ist syntaktisch falsch.",2);
				}
			}
		}
		
		if (isset($message) AND count($message)>0) {
			message::setMessage($message);
			return false;
		} else {
			return true;
		}
	}
	
	public function checkUserData($user_id, $password, $passwordchk, $email) {
		//Check if email is set.
		if (!isset($email) OR $email == "") {
			$message[] = array("Es wurde keine Emailadresse angegeben.",2);
		} else {
			//Check if email exist.
			try {
				$sql = "select * from users WHERE email='$email' AND id!='$user_id'";
				$result = DB::getInstance()->query($sql);
				$emailcheck = $result->rowCount();
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			if ($emailcheck>0) {
				$message[] = array("Ein Benutzer mit der Emailadresse ".$email." existiert bereits.",2);
			} else {
				//Check if the syntax of the adress is correct
				$syntax = true;
				if (!$syntax) {
					$message[] = array("Die Emailadresse ".$email." ist syntaktisch falsch.",2);
				}
			}
		}
		
		if (isset($message) AND count($message)>0) {
			message::setMessage($message);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	* Deletes a user and all of his objects.
	*/
	public function userDelete($id) {
		if ($_POST['delete'] == "true") {
			//Ips mit Services löschen
			foreach(Helper::getIpsByUserId($id) as $ip) {
				IpEditor::deleteIp($ip['id']);
			}
			
			//Subnets, Ips und Services löschen
			foreach(Helper::getSubnetsByUserId($_SESSION['user_id']) as $subnet) {
				subneteditor::deleteSubnet($subnet['id']);
			}
			
			//Logout the user bevore deleting.
			login::user_logout();
			
			DB::getInstance()->exec("DELETE FROM users WHERE id='$id';");
			$message[] = array("Der Benutzer mit der ID ".$id." wurde gelöscht.",1);
			message::setMessage($message);
			return true;
		} else {
			$message[] = array("Sie müssen das Häckchen bei <i>Ja</i> setzen um den Benutzer zu löschen.", 2);
			message::setMessage($message);
			return false;
		}
	}
}

?>