<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/register.class.php');
  
  $register = new register;

  if (empty($_POST['nickname'])) {
    $smarty->assign('message', message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("register.tpl.php");
    $smarty->display("footer.tpl.php");
  } elseif (!$register->insertNewUser($_POST['nickname'], $_POST['password'], $_POST['passwordchk'], $_POST['email'], $_POST['agb'])) {
    $smarty->assign('message', message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("register.tpl.php");
    $smarty->display("footer.tpl.php");
  } else {
    header('Location: ./login.php');
  }
?>