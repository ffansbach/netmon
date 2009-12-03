<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/register.class.php');
  
  $Register = new Register;
    $smarty->assign('networkpolicy', $GLOBALS['networkPolicy']);
    $smarty->assign('project_name', $GLOBALS['project_name']);


  if (empty($_POST)) {
    $smarty->assign('message', Message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("register.tpl.php");
    $smarty->display("footer.tpl.php");
  } elseif (!$Register->insertNewUser($_POST['nickname'], $_POST['password'], $_POST['passwordchk'], $_POST['email'], $_POST['agb'])) {
    $smarty->assign('message', Message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("register.tpl.php");
    $smarty->display("footer.tpl.php");
  } else {
    header('Location: ./login.php');
  }
?>