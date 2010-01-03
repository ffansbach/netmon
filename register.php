<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/register.class.php');
  
  $Register = new Register;
    $smarty->assign('networkpolicy', $GLOBALS['networkPolicy']);
    $smarty->assign('project_name', $GLOBALS['project_name']);


  if (empty($_POST)) {
/*    if(isset($_GET['openid'])) {
      $openid_exploded = explode(".", $_GET['openid']);
      $smarty->assign('nickname', $openid_exploded[0]);
    }*/
    $smarty->assign('message', Message::getMessage());
    $smarty->display("header.tpl.php");
    $smarty->display("register.tpl.php");
    $smarty->display("footer.tpl.php");
  } else {
    if (isset($_GET['openid'])) {
	$password = false;
	$openid = $_POST['openid'];
    } else {
	$password = $_POST['password'];
	$openid = false;
    }

    if ($Register->insertNewUser($_POST['nickname'], $password, $_POST['passwordchk'], $_POST['email'], $_POST['agb'], $openid)) {
	header('Location: ./login.php');
    } else {

   
	if (isset($_GET['openid'])) {
	    header("Location: ./register.php?openid=$_GET[openid]");
	} else {
	    header('Location: ./register.php');
	}
    }
  }
?>