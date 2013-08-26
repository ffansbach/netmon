<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Networklist.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
		$network = new Network((int)$_GET['network_id']);
		$network->delete();
		header('Location: ./networks.php');
	} elseif (empty($_POST)) {
		$networklist = new Networklist();
		$smarty->assign('networklist', $networklist->getNetworklist());
		
		$smarty->display("header.tpl.php");
		$smarty->display("networks.tpl.php");
		$smarty->display("footer.tpl.php");
	} else {
		$network = new Network(false, (int)$_SESSION['user_id'], $_POST['ip'], (int)$_POST['netmask'], (int)$_POST['ipv']);
		$network->store();
		header('Location: ./networks.php');
	}
?>