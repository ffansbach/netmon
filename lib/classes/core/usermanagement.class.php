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
 * This file contains the class for permissionadministration
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class UserManagement {
  
  public function encryptPassword($password) {
    return md5($password);
  }
  
	public function denyAccess() {
      $message[] = array("Sie haben nicht das Recht auf diesen Bereich zuzugreifen!",2);
      Message::setMessage($message);
	  $_SESSION['redirect_url'] = ".".$_SERVER['REQUEST_URI'];
	  header('Location: ./login.php?section=login');
      die();
	}

  public function isOwner(&$smarty, $owning_user_id) {
    if($owning_user_id!=$_SESSION['user_id'] OR !isset($owning_user_id)) {
      $message[] = array("Sie haben nicht das Recht auf diesen Bereich zuzugreifen, da Sie nicht der Eigentümer des Objekts sind!",2);
      Message::setMessage($message);
	  $_SESSION['redirect_url'] = ".".$_SERVER['REQUEST_URI'];
	  header('Location: ./login.php?section=login');
      die();
    } else {
      return true;
    }
  }

	//Checks if a user is owner or has the permission to access the site
	public function checkIfUserIsOwnerOrPermitted($site_permission, $owning_user_id) {
		if($owning_user_id==$_SESSION['user_id']) {
			return true;
		} elseif(UserManagement::checkPermission($site_permission, $_SESSION['user_id'])) {
			return true;
		} else {
			return false;
		}
	}

  public function isThisUserOwner($owning_user_id, $user_id=false) {
	if(!$user_id) {
		$user_id = $_SESSION['user_id'];
	}

    if($owning_user_id!=$user_id OR !isset($owning_user_id)) {
      return false;
    } else {
      return true;
    }
  }

	public function checkPermission($sitepermission, $user_id=false) {
		$userpermission = UserManagement::getUserPermission($user_id);
		
		//Transform permissions into binary
		$sitepermission = decbin($sitepermission);
		$userpermission = decbin($userpermission);
		$sitepermission_len = strlen($sitepermission);
		$userpermission_len = strlen($userpermission);
		
		//get all permissions
		$permission = UserManagement::getAllPermissions();
		
		$return = false;
		for ($i=count($permission)-1; $i>=0; $i--) {
			$exponent = $permission[$i];
			if ($sitepermission[$sitepermission_len-($exponent+1)]==1) {
				if ($sitepermission[$sitepermission_len-($exponent+1)]==$userpermission[$userpermission_len-($exponent+1)]) {
					$return = true;
				}
			}
		}
		return $return;
	}

	public function getUserPermission($user_id=false) {
		if($user_id) {
			//Each user gets the permission 0
			$userpermission = pow(2,0);
			//Each logged in user gets the permission "logged in"
			$userpermission = $userpermission + pow(2,2);
			try {
				$sql = "select permission from users WHERE id=$user_id";
				$result = DB::getInstance()->query($sql);
				$user_data = $result->fetch(PDO::FETCH_ASSOC);
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			//Each user get´s the permission from the database
			$userpermission = $userpermission + $user_data['permission'];
		}elseif (!isset($_SESSION['user_id'])) {
			//Jeder Benutzer bekommt das recht 0
			$userpermission = pow(2,0);
			//Each user that is not logged in gets the permission 1
			$userpermission = $userpermission + pow(2,1);
		} else {
			//Each user gets the permission 0
			$userpermission = pow(2,0);
			//Each logged in user gets the permission "logged in"
			$userpermission = $userpermission + pow(2,2);
			try {
				$sql = "select permission from users WHERE id=$_SESSION[user_id]";
				$result = DB::getInstance()->query($sql);
				$user_data = $result->fetch(PDO::FETCH_ASSOC);
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			//Each user get´s the permission from the database
			$userpermission = $userpermission + $user_data['permission'];
		}
		return $userpermission;
	}

	public function isLoggedIn($user_id) {
		if (UserManagement::checkPermission(4, $user_id))
			return true;
		else
			return false;
	}

	public function getAllPermissions() {
		/*
		0 = Alle, permission is generated by Netmon
		1 = nicht eingeloggte, permission is generated by Netmon
		2 = eingeloggte, permission is generated by Netmon
		3 = user, permission in DB
		4 = mod, permission in DB
		5 = admin, permission in DB
		6 = root, permission in DB
		*/
		return array(0,1,2,3,4,5,6);
	}
	public function getEditablePermissionsWithNames() {
		//Uncomment all permissions which are automatically set by Netmon
		/*$permissions[0]['permission'] = 0;
		$permissions[0]['name'] = 'Alle';
		$permissions[1]['permission'] = 1;
		$permissions[1]['name'] = 'Nicht eingeloggt';
		$permissions[2]['permission'] = 2;
		$permissions[2]['name'] = 'Eingeloggt';*/
		$permissions[3]['permission'] = 3;
		$permissions[3]['name'] = 'Benutzer';
		$permissions[4]['permission'] = 4;
		$permissions[4]['name'] = 'Moderator';
		$permissions[5]['permission'] = 5;
		$permissions[5]['name'] = 'Administrator';
		$permissions[6]['permission'] = 6;
		$permissions[6]['name'] = 'Root';

		return $permissions;
	}
}

?>