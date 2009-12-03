<?php
	require_once('./config/runtime.inc.php');
	require_once('./lib/classes/core/register.class.php');
	
	$Register = new Register;
	
	if($Register->userActivate($_GET['activation_hash']))
		header('Location: ./login.php');
	else
		header('Location: ./portal.php');
?>