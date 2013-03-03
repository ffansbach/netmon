<?php
	require_once('runtime.php');
	require_once('lib/classes/core/register.class.php');
	
	if(Register::userActivate($_GET['activation_hash']))
		header('Location: ./login.php');
	else
		header('Location: ./index.php');
?>