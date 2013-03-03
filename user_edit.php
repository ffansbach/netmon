<?php

require_once('runtime.php');
require_once('./lib/classes/core/helper.class.php');
require_once('./lib/classes/core/usermanagement.class.php');
require_once('./lib/classes/core/user.class.php');

$smarty->assign('message', Message::getMessage());

if ($_GET['section'] == "edit") {	
	//Only owner and Root can access this site.
	if (!UserManagement::checkIfUserIsOwnerOrPermitted(64, $_GET['user_id']))
		UserManagement::denyAccess();

	$smarty->assign('user', User::getUserByID($_GET['user_id']));	
	$smarty->assign('is_root', UserManagement::checkPermission(64, $_SESSION['user_id']));
	$permissions = UserManagement::getEditablePermissionsWithNames();
	foreach ($permissions as $key=>$permission) {
		$permission['dual'] = pow(2,$permission['permission']);
		$permissions[$key]['check'] = UserManagement::checkPermission($permission['dual'], $_GET['user_id']);
		$permissions[$key]['dual'] = $permission['dual'];
	}
	$smarty->assign('permissions', $permissions);
	
	$smarty->display("header.tpl.php");
	$smarty->display("user_edit.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif ($_GET['section'] == "insert_edit") {
	if (!UserManagement::checkIfUserIsOwnerOrPermitted(64, $_GET['user_id']))
		UserManagement::denyAccess();
	
	if (User::userInsertEdit($_GET['user_id'], $_POST['changepassword'], $_POST['permission'], $_POST['oldpassword'], $_POST['newpassword'],
				 $_POST['newpasswordchk'], $_POST['openid'], $_POST['vorname'], $_POST['nachname'], $_POST['strasse'],
				 $_POST['plz'], $_POST['ort'], $_POST['telefon'], $_POST['email'], $_POST['jabber'],
				 $_POST['icq'], $_POST['website'], $_POST['about'], $_POST['notification_method'])) {
		header('Location: user.php?user_id='.$_SESSION['user_id']);
	} else {
		header('Location: user_edit.php?section=edit&user_id='.$_SESSION['user_id']);
	}
} elseif ($_GET['section'] == "delete") {
	if (!UserManagement::checkIfUserIsOwnerOrPermitted(64, $_GET['user_id']))
		UserManagement::denyAccess();
	
	if ($_POST['delete'] == "true") {
		User::userDelete($_POST['user_id']);
		header('Location: routerlist.php');
	} else {
		$message[] = array("Sie müssen das Häckchen bei <i>Ja</i> setzen um den Benutzer zu löschen.", 2);
		message::setMessage($message);
		header('Location: user_edit.php?section=edit&user_id='.$_SESSION['user_id']);
	}
  }
?>