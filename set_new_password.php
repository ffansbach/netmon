<?php

  require_once('./config/runtime.inc.php');
  require_once('./lib/classes/core/helper.class.php');
  require_once('./lib/classes/core/register.class.php');
  
  $register = new register;
  
  $register->setNewPassword($_GET['new_passwordhash'], $_GET['oldpassword_hash'], $_GET['user_id']);
  header('Location: login.php');
  
?>