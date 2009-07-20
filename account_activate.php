<?php
	require_once('./config/runtime.inc.php');
	require_once('./lib/classes/core/register.class.php');
	
	$register = new register;
	
	if($register->userActivate($_GET['activation_hash']))
		header('Location: ./login.php');
	else
		header('Location: ./portal.php');
?>