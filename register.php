<?php
require_once('runtime.php');
require_once('lib/classes/core/register.class.php');
require_once('lib/classes/core/ConfigLine.class.php');
  
$smarty->assign('enable_network_policy', ConfigLine::configByName('enable_network_policy'));
$smarty->assign('network_policy_url', ConfigLine::configByName('network_policy_url'));

if (empty($_POST)) {
	$smarty->assign('message', Message::getMessage());
	$smarty->display("header.tpl.php");
	$smarty->display("register.tpl.php");
	$smarty->display("footer.tpl.php");
} else {
	//check weather the users wants to register with his openid
	if (isset($_GET['openid'])) {
		$_POST['password'] = Helper::randomPassword(8);
		$_POST['passwordchk'] = $_POST['password'];
	}

	if (Register::insertNewUser($_POST['nickname'], $_POST['password'], $_POST['passwordchk'], $_POST['email'], $_POST['agb'], $_POST['openid'])) {
		header('Location: ./login.php');
	} else {
		if (isset($_GET['openid']))
			header("Location: ./register.php?openid=$_GET[openid]");
		else
			header('Location: ./register.php');
	}
}

?>