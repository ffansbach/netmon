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

require_once("./lib/classes/core/nodeeditor.class.php");
require_once("./lib/classes/core/subneteditor.class.php");

/**
 * This file contains the class to get the for the user site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class user {
	function __construct(&$smarty) {
		if (!isset($_GET['section'])) {
		  $smarty->assign('user', Helper::getUserByID($_GET['id']));
		  $smarty->assign('nodelist', Helper::getNodelistByUserID($_GET['id']));
		  $smarty->assign('subnetlist', Helper::getSubnetlistByUserID($_GET['id']));
		  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		  $smarty->assign('timeBetweenCrawls', $GLOBALS['timeBetweenCrawls']);
		  $smarty->assign('get_content', "user");
		}
		if ($_GET['section'] == "edit") {
		  usermanagement::isOwner(&$smarty, $_SESSION['user_id']);
		  $smarty->assign('user', Helper::getUserByID($_GET['id']));
		  $smarty->assign('get_content', "user_edit");
		} elseif ($_GET['section'] == "insert_edit") {
		  usermanagement::isOwner(&$smarty, $_SESSION['user_id']);
		  $smarty->assign('user', Helper::getUserByID($_GET['id']));
		  if ($this->userInsertEdit()) {
		    $smarty->assign('message', message::getMessage());
		    $smarty->assign('get_content', "user");
		  } else {
		    $smarty->assign('message', message::getMessage());
		    $smarty->assign('get_content', "user_edit");
		  }
		} elseif ($_GET['section'] == "delete") {
		  usermanagement::isOwner(&$smarty, $_SESSION['user_id']);
		  if ($this->userDelete($_SESSION['user_id'])) {
		    $smarty->assign('message', message::getMessage());
		    $smarty->assign('get_content', "portal");
		  } else {
		    $smarty->assign('message', message::getMessage());
		    $smarty->assign('get_content', "user_edit");
		  }
		}
	}

  function userInsertEdit() {
    if ($this->checkUserEditData($_GET['id'], $_POST['changepassword'], $_POST['oldpassword'], $_POST['newpassword'], $_POST['newpasswordchk'], $_POST['email'])) {
      $password = usermanagement::encryptPassword($_POST['newpassword']);
	  if ($_POST['changepassword']) {
	  $sqlinsert = "UPDATE users SET
password = '$password',
vorname = '$_POST[vorname]',
nachname = '$_POST[nachname]',
strasse = '$_POST[strasse]',
plz = '$_POST[plz]',
ort = '$_POST[plz]',
telefon = '$_POST[telefon]',
email = '$_POST[email]',
jabber = '$_POST[jabber]',
icq = '$_POST[icq]',
website = '$_POST[website]',
about = '$_POST[about]'
WHERE id = '$_GET[id]'
";	  	
	  } else {
	  $sqlinsert = "UPDATE users SET
vorname = '$_POST[vorname]',
nachname = '$_POST[nachname]',
strasse = '$_POST[strasse]',
plz = '$_POST[plz]',
ort = '$_POST[plz]',
telefon = '$_POST[telefon]',
email = '$_POST[email]',
jabber = '$_POST[jabber]',
icq = '$_POST[icq]',
website = '$_POST[website]',
about = '$_POST[about]'
WHERE id = '$_GET[id]'
";	  	
	  }
	  
      //Mach DB Eintrag
      $db = new mysqlClass;
      $db->mysqlQuery($sqlinsert);
      unset($db);

  $db = new mysqlClass;
  $result = $db->mysqlQuery("SELECT nickname
FROM users
WHERE id = $_GET[id]
    ");
  $user = mysql_fetch_assoc($result);
    unset($db);
    $message[] = array("Die Daten von $user[nickname] wuden geändert", 1);
    message::setMessage($message);
    return true;
   } else {
      return false;
   }
 }

  public function checkUserEditData($user_id, $changepassword, $oldpassword, $newpassword, $newpasswordchk, $email) {
	if($changepassword) {
    	$db = new mysqlClass;
    	$result = $db->mysqlQuery("SELECT password from users WHERE id='$user_id';");
    	while($row = mysql_fetch_assoc($result)) {
			$dbpassword = $row['password'];
    	}
    	unset($db);	
   		if ($dbpassword == usermanagement::encryptPassword($oldpassword)) {
   			//Prüfen ob Passwort gesetzt ist
   			if (empty($newpassword)) {
   				$message[] = array("Es wurde kein Passwort angegeben.",2);
   			} elseif (empty($newpasswordchk)) {
   				$message[] = array("Das Passwort wurde kein zweites mal eingegeben.",2);
   			} elseif ($newpassword != $newpasswordchk) {
   				$message[] = array("Die neuen Passwörter stimmen nicht überein.", 2);
   			}
   		} else {
   			$message[] = array("Das alte Passwort ist Falsch.", 2);
   		}
	}
    
    //Prüfen ob Email gesetzt ist
    if (!isset($email) OR $email == "") {
      $message[] = array("Es wurde keine Emailadresse angegeben.",2);
    } else {
      //Prüfen ob Emailadresse existiert
      $db = new mysqlClass;
      $db->mysqlQuery("select * from users WHERE email='$email' AND id!='$user_id'");
      $emailcheck = $db->mysqlAffectedRows();
      unset($db);
      if ($emailcheck>0) {
        $message[] = array("Ein Benutzer mit der Emailadresse ".$email." existiert bereits.",2);
      } else {
	//Prüfen ob die Emailadresse systaktisch korrekt ist
	$syntax = true;
	if (!$syntax) {
          $message[] = array("Die Emailadresse ".$email." ist syntaktisch falsch.",2);
	}
      }
    }



    //Rückgabe
    if (isset($message) AND count($message)>0) {
      message::setMessage($message);
      return false;
    } else {
      return true;
    }
  }
  

  public function checkUserData($user_id, $password, $passwordchk, $email) {
    //Prüfen ob Email gesetzt ist
    if (!isset($email) OR $email == "") {
      $message[] = array("Es wurde keine Emailadresse angegeben.",2);
    } else {
      //Prüfen ob Emailadresse existiert
      $db = new mysqlClass;
      $db->mysqlQuery("select * from users WHERE email='$email' AND id!='$user_id'");
      $emailcheck = $db->mysqlAffectedRows();
      unset($db);
      if ($emailcheck>0) {
        $message[] = array("Ein Benutzer mit der Emailadresse ".$email." existiert bereits.",2);
      } else {
	//Prüfen ob die Emailadresse systaktisch korrekt ist
	$syntax = true;
	if (!$syntax) {
          $message[] = array("Die Emailadresse ".$email." ist syntaktisch falsch.",2);
	}
      }
    }

    //Prüfen ob Passwort gesetzt ist
    if (!isset($password) OR $password == "") {
      $message[] = array("Es wurde kein Passwort angegeben.",2);
    } elseif (!isset($passwordchk) OR $passwordchk == "") {
      $message[] = array("Das Passwort wurde kein zweites mal eingegeben.",2);
    } elseif ($password != $passwordchk) {
      $message[] = "Die Passwörter stimmen nicht überein.";
    }

    //Rückgabe
    if (isset($message) AND count($message)>0) {
      message::setMessage($message);
      return false;
    } else {
      return true;
    }
  }

	/**
	* Löscht einen Benutzer vollständig!
	*/
	public function userDelete($id) {
		if ($_POST['delete'] == "true") {
			//Nodes mit Services löschen
			foreach(Helper::getNodesByUserId($id) as $node) {
				nodeeditor::deleteNode($node['id']);
			}
			
			//Subnets, Nodes und Services löschen
			foreach(Helper::getSubnetsByUserId($_SESSION['user_id']) as $subnet) {
				subneteditor::deleteSubnet($subnet['id']);
			}

	    	//Ausloggen vorm löschen des Benutzers
    		login::logout();

			$db = new mysqlClass;
      		$db->mysqlQuery("DELETE FROM users WHERE id='$id';");
      		unset($db);
      		$message[] = array("Der Benutzer mit der ID ".$id." wurde gelöscht.",1);
      		message::setMessage($message);
      		return true;
		} else {
			$message[] = "Sie müssen das Häckchen bei \"Ja\" setzen um den Benutzer zu löschen!";
			message::setMessage($message);
			return false;
		}
	}

}

?>