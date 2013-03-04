<?php

require_once('runtime.php');
require_once('./lib/classes/core/helper.class.php');
require_once('./lib/classes/core/user.class.php');

$smarty->assign('message', Message::getMessage());

if ($_GET['section'] == "edit") {
	//Only owner and Root can access this site.
	if (!Permission::checkIfUserIsOwnerOrPermitted(64, $_GET['user_id']))
		Permission::denyAccess();

	$smarty->assign('user', User::getUserByID($_GET['user_id']));	
	$smarty->assign('is_root', Permission::checkPermission(64, $_SESSION['user_id']));
	$smarty->assign('permissions', User::getRolesByUserID($_GET['user_id']));
	
	$smarty->display("header.tpl.php");
	$smarty->display("user_edit.tpl.php");
	$smarty->display("footer.tpl.php");
} elseif ($_GET['section'] == "insert_edit") {
	if (!Permission::checkIfUserIsOwnerOrPermitted(64, $_GET['user_id']))
		Permission::denyAccess();
	
	if (User::userInsertEdit($_GET['user_id'], $_POST['changepassword'], $_POST['permission'], $_POST['oldpassword'], $_POST['newpassword'],
				 $_POST['newpasswordchk'], $_POST['openid'], $_POST['vorname'], $_POST['nachname'], $_POST['strasse'],
				 $_POST['plz'], $_POST['ort'], $_POST['telefon'], $_POST['email'], $_POST['jabber'],
				 $_POST['icq'], $_POST['website'], $_POST['about'], $_POST['notification_method'])) {
		header('Location: user.php?user_id='.$_GET['user_id']);
	} else {
		header('Location: user_edit.php?section=edit&user_id='.$_GET['user_id']);
	}
} elseif ($_GET['section'] == "delete") {
	if (!Permission::checkIfUserIsOwnerOrPermitted(64, $_GET['user_id']))
		Permission::denyAccess();
	
	if ($_POST['delete'] == "true") {
		User::userDelete($_GET['user_id']);
		header('Location: routerlist.php');
	} else {
		$message[] = array("Sie müssen das Häckchen bei <i>Ja</i> setzen um den Benutzer zu löschen.", 2);
		message::setMessage($message);
		header('Location: user_edit.php?section=edit&user_id='.$_GET['user_id']);
	}
  }
?>