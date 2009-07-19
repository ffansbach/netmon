<?php
  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/register.class.php');
  
  $register = new register;

  if (isset($_GET['activation_hash'])) {
    if(!$register->userActivate($_GET['activation_hash']))
	header('Location: ./portal.php');
    else
	header('Location: ./login.php');
  } else {
	$message[] = array("Es wurde kein aktivierungshash angegeben!", 2);
	message::setMessage($message);
  	header('Location: ./portal.php');
  }
?>