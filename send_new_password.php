<?php

  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/register.class.php');
  
  $register = new register;

  if (empty($_POST['email'])) {
    $smarty->display("header.tpl.php");
    $smarty->display("send_new_password.tpl.php");
    $smarty->display("footer.tpl.php");
  } else {
    $user = Helper::getUserByEmail($_POST['email']);
    $new_password = helper::randomPassword(8);
	$new_password_md5 = md5($new_password);
    $register->sendPassword($user['id'], $user['email'], $user['nickname'], $new_password, $new_password_md5, $user['password']);
    header('Location: ./login.php');
  }

?>