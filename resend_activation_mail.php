<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/register.class.php');
  
  $register = new register;

  if (empty($_POST['email'])) {
    $smarty->assign('message', message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("resend_activation_mail.tpl.php");
    $smarty->display("footer.tpl.php");
  } else {
    $user = Helper::getUserByEmail($_POST['email']);
    if ($user['activated']!="0") {
      $new_password = Helper::randomPassword(8);
      $register->setNewPassword($new_password, $user['id']);
      $register->sendRegistrationEmail($user['email'], $user['nickname'], $new_password, $user['activated'], strtotime($user['create_date']));
      header('Location: ./login.php');
    } else {
      $message[] = array("Der Benutzer mit der Emailadresse $_POST[email] wurde bereits freigeschaltet!", 2);
      message::setMessage($message);
      header('Location: ./login.php');
    }
  }
?>