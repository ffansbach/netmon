<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Networklist.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
		$network = new Network((int)$_GET['network_id']);
		$network->fetch();
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $network->getUserId())) {
			$network->delete();
			header('Location: ./networks.php');
		} else {
			Permission::denyAccess(PERM_ROOT, $network->getUserId());
		}
	} elseif (empty($_POST)) {
		$networklist = new Networklist();
		$smarty->assign('networklist', $networklist->getNetworklist());
		
		$smarty->display("header.tpl.html");
		$smarty->display("networks.tpl.html");
		$smarty->display("footer.tpl.html");
	} else {
		if(Permission::checkPermission(PERM_ROOT)) {
			$network = new Network(false, (int)$_SESSION['user_id'], $_POST['ip'], (int)$_POST['netmask'], (int)$_POST['ipv']);
			$network->store();
			header('Location: ./networks.php');
		} else {
			Permission::denyAccess(PERM_ROOT);
		}
	}
?>