<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Ip.class.php');
	require_once(ROOT_DIR.'/lib/core/Networklist.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterface.class.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	
	if($_GET['section']=='add') {
		$networkinterface = new Networkinterface((int)$_GET['interface_id']);
		$networkinterface->fetch();
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $networkinterface->getRouter()->getUserId())) {
			$smarty->assign('message', Message::getMessage());
			
			$smarty->assign('networkinterface', $networkinterface);
			
			$networklist = new Networklist();
			$smarty->assign('networklist', $networklist->getNetworklist());
			
			$router = new Router((int)$_GET['router_id']);
			$router->fetch();
			$smarty->assign('router', $router);
			
			$smarty->display("header.tpl.html");
			$smarty->display("ip_add.tpl.html");
			$smarty->display("footer.tpl.html");
		} else {
			Permission::denyAccess(PERM_ROOT, $networkinterface->getRouter()->getUserId());
		}
	} elseif ($_GET['section']=='insert_add') {
		$networkinterface = new Networkinterface((int)$_GET['interface_id']);
		$networkinterface->fetch();
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $networkinterface->getRouter()->getUserId())) {
			$ip = new Ip(false, (int)$_GET['interface_id'], (int)$_POST['network_id'], $_POST['ip']);
			if($ip->store()) {
				$message[] = array('Die IP '.$_POST['ip'].' wurde angelegt.', 1);
				Message::setMessage($message);
			} else {
				$message[] = array('Die IP '.$_POST['ip'].' konnte nicht angelegt werden.', 2);
				Message::setMessage($message);
			}
			header('Location: ./router.php?router_id='.$_GET['router_id']);
		} else {
			Permission::denyAccess(PERM_ROOT, $networkinterface->getRouter()->getUserId());
		}
	} elseif ($_GET['section']=='delete') {
		$ip = new Ip((int)$_GET['ip_id']);
		$ip->fetch();
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $ip->getNetworkinterface()->getRouter()->getUserId())) {
			$ip->delete();
			
			$message[] = array('Die IP '.$ip->getIp().'/'.$ip->getNetwork()->getNetmask().' wurde gelöscht.', 1);
			Message::setMessage($message);
			
			header('Location: ./router.php?router_id='.$_GET['router_id']);
		} else {
			Permission::denyAccess(PERM_ROOT, $ip->getNetworkinterface()->getRouter()->getUserId());
		}
	}
?>