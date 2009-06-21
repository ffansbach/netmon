<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/login.class.php');
  
  $login = new login;
  $message = new message;

  $smarty->assign('message', message::getMessage());

  if ($_GET['section']=="login") {
    $smarty->display("header.tpl.php");
    $smarty->display("login.tpl.php");
    $smarty->display("footer.tpl.php");
  } elseif ($_GET['section']=="login_send" AND $login->user_login($_POST['nickname'], $_POST['password'], $message)) {
    header('Location: desktop.php');
  } elseif($_GET['section']=="logout") {
    $login->user_logout($message);
    header('Location: portal.php');
  } else {
    $_GET['section'] = "login";
    require('login.php');
  }
?>