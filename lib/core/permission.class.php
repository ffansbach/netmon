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
 * This class is used as a container for static methods that deal with permissions.
 * Each user has a permission field in the database. This field represents which roles a user has.
 * A user can have up to 7 different roles assigned which must not depent on each other. This roles are used
 * to handle site and function access.
 * Netmon knows the following roles:
 * 0 = standart permission. Each user has this role. There is no user without this role so this role can be ignored  (handled by netmon).
 * 1 = not logged in  (handled by netmon)
 * 2 = logged in  (handled by netmon)
 * 3 = user
 * 4 = moderator
 * 5 = administrator
 * 6 = root
 * To create a permission value, sum up the 2^role values you want to use in your permission value.
 * In example if you have a section which only root and administrators should have access to do: 2^root + 2^administrator = 2^6+2^5 = 96.
 * Another example: if you have a section which only logged in persons should have access to do: 2^logged in = 4.
 *
 * @package	Netmon
 */

	require_once(ROOT_DIR.'/lib/core/User.class.php');
 
class Permission {
	/**
	* Get all roles that are avaliable in netmon. For a description of the roles see class description.
	* @author  Clemens John <clemens-john@gmx.de>
	* @return array() array containing the avaliable roles
	*/
	static public function getAllRoles() {
		return array(0,1,2,3,4,5,6);
	}
	
	/**
	* Get all roles that are avaliable in netmon and can freely be assigned to a user (all permission that are not handled
	* automatically by netmon). For a description of the roles see class description.
	* @author  Clemens John <clemens-john@gmx.de>
	* @return array() array containing the avaliable and editable roles
	*/
	static public function getEditableRoles() {
		return array(3,4,5,6);
	}
	
	/**
	* Deny acces to a special section. Sets a deny message and forwards the user to the login site.
	* @author  Clemens John <clemens-john@gmx.de>
	*/
	static public function denyAccess($permission=false, $owner=false) {
		// if $permission is != false, then get the Names of the Roles that are wrapped into $permission
		if($permission) {
			$role_string = "";
			foreach(Permission::getRolesByPermission($permission) as $key=>$role) {
				if($key)
					$role_string .= ", ";
				$role_string .= permission::getRoleNameByRoleNumber($role);
			}
		}
		
		if($owner) {
			$user = new User($owner);
			$user->fetch();
		}
	
		// prepare the "permission denied"-message for the user based on the combination of $permission and $owner
		if($permission AND !$owner)
			$message[] = array("Auf diesen Bereich dürfen nur Benutzer mit den folgenden Rechten zugreifen: ".$role_string, 2);
		elseif(!$permission AND $owner)
			$message[] = array("Auf diesen Bereich darf nur der Benutzer ".$user->getNickname()." zugreifen.",2);
		elseif($permission AND $owner)
			$message[] = array("Auf diesen Bereich dürfen nur der Benutzer ".$user->getNickname()." oder Benutzer mit den folgenden Rechten zugreifen: ".$role_string, 2);
		else
			$message[] = array("Du darfst auf diesen Bereich nicht zugreifen.",2);
		
		// set the message
		Message::setMessage($message);
		
		// redirect the user to the last page he visited if it was a page inside netmon
		// if the page was not inside netmon, redirect to the default path
		if(!empty($_SESSION['last_page']) AND $_SESSION['last_page']!=$_SESSION['current_page']) {
			header('Location: '.$_SESSION['last_page']);
		} else {
			header('Location: ./');
		}
	}
	
	static public function checkPermissionByPermission($rolepermission, $permission) {
		$sitepermission = $rolepermission;
		$userpermission = $permission;
		
		//Transform permissions into binary
		$sitepermission = decbin($sitepermission);
		$userpermission = decbin($userpermission);
		$sitepermission_len = strlen($sitepermission);
		$userpermission_len = strlen($userpermission);
		
		//get all permissions
		$roles = Permission::getAllRoles();
		
		for ($i=count($roles)-1; $i>=0; $i--) {
			$exponent = $roles[$i];
			if (($sitepermission_len-($exponent+1)>=0) && $sitepermission[$sitepermission_len-($exponent+1)]==1) {
				if (($sitepermission_len-($exponent+1))>=0 AND $userpermission_len-($exponent+1)>= 0 AND $sitepermission[$sitepermission_len-($exponent+1)]==$userpermission[$userpermission_len-($exponent+1)]) {
					return true;
				}
			}
		}
		return false;
	}
	
	public static function getRolesByPermission($permission) {
		$roles = permission::getAllRoles();
		$roles_edit = array();
		foreach ($roles as $key=>$role) {
			if(Permission::checkPermissionByPermission(pow(2,$role), $permission))
				$roles_edit[] = $role;
		}
		return $roles_edit;
	}
	
	public static function getRoleNameByRoleNumber($role) {
		switch($role) {
			case 0: return "Default";
			case 1: return "nicht eingeloggt";
			case 2: return "eingeloggt";
			case 3: return "Benutzer";
			case 4: return "Moderator";
			case 5: return "Administrator";
			case 6: return "Root";
		}
	}
	
	/**
	* Checks if a user is the owner of an object
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $$owning_user_id user id of the user that own the object for wich the permission should be checked.
	* @param $user_id the id of the user that is requesting access. If not given, the id of the current logged in user is used.
	* @return boolean true if the user is the owner of the object.
	*/
	static public function isThisUserOwner($owning_user_id, $user_id=false) {
		if(!$user_id)
			$user_id = $_SESSION['user_id'];
		
		if($owning_user_id!=$user_id OR !isset($owning_user_id))
			return false;
		else
			return true;
	}
	
	/**
	* Checks if a user is permitted to acces a special section or object or if he is the owner
	* of this section or object
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $site_permission permission value of the site. See class description on how to calculate the permission value you need.
	* @param $$owning_user_id user id of the user that own the object for wich the permission should be checked.
	* @return boolean true if the user has the asced permission or is owner.
	*/
	static public function checkIfUserIsOwnerOrPermitted($site_permission, $owning_user_id) {
		if(isset($owning_user_id) AND isset($_SESSION['user_id']) AND $owning_user_id==$_SESSION['user_id'])
			return true;
		elseif(isset($_SESSION['user_id']) AND isset($site_permission) AND Permission::checkPermission($site_permission, $_SESSION['user_id']))
			return true;
		else
			return false;
	}
	
	/**
	* Checks if a user has the requested permission. In example if you want to check if a is root, then
	* set $sitepermission to 2^root = 2^6 = 64.
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $sitepermission permission value of the site. See class description on how to calculate the permission value you need.
	* @param $user_id user id of the user for wich the permission should be checked.
	* @return boolean true if the user has the asked permission.
	*/
	static public function checkPermission($sitepermission, $user_id=false) {
		$userpermission = Permission::getUserPermission($user_id);
		
		//Transform permissions into binary
		$sitepermission = decbin($sitepermission);
		$userpermission = decbin($userpermission);
		$sitepermission_len = strlen($sitepermission);
		$userpermission_len = strlen($userpermission);
		
		//get all permissions
		$roles = Permission::getAllRoles();
		
		for ($i=count($roles)-1; $i>=0; $i--) {
			$exponent = $roles[$i];
			if (($sitepermission_len-($exponent+1)>=0) && $sitepermission[$sitepermission_len-($exponent+1)]==1) {
				if (($sitepermission_len-($exponent+1))>=0 AND $userpermission_len-($exponent+1)>= 0 AND $sitepermission[$sitepermission_len-($exponent+1)]==$userpermission[$userpermission_len-($exponent+1)]) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	* Get the permission value of the current user. Only this method gives you a correct value that includes
	* the automatically generated permissions of netmon. Dont fetch the value directly from the database.
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $user_id user id of the user for wich you want to get the permission. If the user id is not set,
	*       	  the id of the currently logged in user is used.  Can only be the current user.
	* @return int permission value of the user
	*/
	static public function getUserPermission($user_id=false) {
		if(!$user_id && isset($_SESSION['user_id']))
			$user_id = $_SESSION['user_id'];
		
		//Each user gets the permission 0
		$userpermission = pow(2,0);
		if(!isset($_SESSION['user_id'])) {
			//Each user that is not logged in gets the permission 1
			$userpermission += pow(2,1);
		} else {
			//Each logged in user gets the permission "logged in"
			$userpermission += pow(2,2);
		}

		if(is_numeric($user_id)) {
			//Each user get´s the permission from the database		
			try {
				$stmt = DB::getInstance()->prepare("SELECT permission FROM users WHERE id=?");
				$stmt->execute(array($user_id));
				$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
			$userpermission += $user_data['permission'];
		}
		return $userpermission;
	}

	/**
	* Wrapper method for checking if the current user is logged in
	* @author  Clemens John <clemens-john@gmx.de>
	* @param $user_id user id of the user for wich you want to check the login. Can only be the current user.
	* @return boolean if the current user is logged in.
	*/
	static public function isLoggedIn($user_id) {
		return Permission::checkPermission(4, $user_id);
	}
}

?>