<?php

require_once('runtime.php');
require_once(ROOT_DIR.'/lib/classes/core/helper.class.php');
require_once(ROOT_DIR.'/lib/classes/core/register.class.php');
require_once(ROOT_DIR.'/lib/classes/core/user.class.php');
require_once(ROOT_DIR.'/lib/classes/extern/phpass/PasswordHash.php');
  
$Register = new Register;

if (empty($_POST['email'])) {
	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("send_new_password.tpl.php");
	$smarty->display("footer.tpl.php");
} else {
	$user_data = User::getUserByEmail($_POST['email']);
	if ($user_data) {
		$new_password = Helper::randomPassword(10);
		
		//hash password to store in db
		$phpass = new PasswordHash(8, false);
		$new_password_hash = $phpass->HashPassword($new_password);
		
		if (strlen($new_password_hash) < 20) {
			$message[] = array("Beim Hashen des Passworts trat ein Fehler auf.",2);
			Message::setMessage($message);
			header('Location: ./login.php');
			die();
		}

		$Register->sendPassword($user_data['id'], $user_data['email'], $user_data['nickname'], $new_password, $new_password_hash, $user_data['password']);
		header('Location: ./login.php');
	} else {
		$message[] = array("Die Emailadresse konnte keinem Benutzer zugeordnet werden.", 2);
		Message::setMessage($message);
		header('Location: ./send_new_password.php');
	}
}

?>