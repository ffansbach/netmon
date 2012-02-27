<?php

  require_once('runtime.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/register.class.php');
  require_once('./lib/classes/core/user.class.php');
  
  $Register = new Register;

  if (empty($_POST['email'])) {
    $smarty->assign('message', Message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("send_new_password.tpl.php");
    $smarty->display("footer.tpl.php");
  } else {
    $user = User::getUserByEmail($_POST['email']);
    if ($user) {
		$new_password = Helper::randomPassword(8);
		$new_password_md5 = md5($new_password);
		$Register->sendPassword($user['id'], $user['email'], $user['nickname'], $new_password, $new_password_md5, $user['password']);
		header('Location: ./login.php');
    } else {
		$message[] = array("Die Emailadresse konnte keinem Benutzer zugeordnet werden.", 2);
		Message::setMessage($message);
		header('Location: ./send_new_password.php');
    }
  }

?>