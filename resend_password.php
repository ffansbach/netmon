<?php

  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/register.class.php');
  
  $register = new register;

  if (empty($_POST['email'])) {
    $smarty->display("header.tpl.php");
    $smarty->display("resend_password.tpl.php");
    $smarty->display("footer.tpl.php");
  } else {
    $user = Helper::getUserByEmail($_POST['email']);
    $new_password = helper::randomPassword(8);
    $register->setNewPassword($new_password, $user['id']);
    $register->sendPassword($user['email'], $user['nickname'], $new_password);
    header('Location: ./login.php');
  }

?>