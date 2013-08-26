<?php

require_once('runtime.php');
require_once('lib/core/register.class.php');
  
Register::setNewPassword($_GET['new_passwordhash'], $_GET['oldpassword_hash'], $_GET['user_id']);
header('Location: login.php');
  
?>