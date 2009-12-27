<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/login.class.php');
  
  $Login = new Login;
  $message = new message;

  if ($_GET['section']=="login") {
    $smarty->assign('message', Message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("login.tpl.php");
    $smarty->display("footer.tpl.php");
  } elseif ($_GET['section']=="login_send" AND $Login->user_login($_POST['nickname'], $_POST['password'], $_POST['remember'])) {
	if(isset($_SESSION['redirect_url']))
		header('Location: '.$_SESSION['redirect_url']);
	else
		header('Location: desktop.php');
  } elseif($_GET['section']=="logout") {
    $Login->user_logout($message);
    header('Location: portal.php');
  } else {
    $_GET['section'] = "login";
    require('login.php');
  }
?>