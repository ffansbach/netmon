<?php
	require_once('runtime.php');
	require_once('lib/core/Router.class.php');
	require_once('lib/core/Networkinterface.class.php');
	
	if($_GET['section']=='add') {
		$router = new Router((int)$_GET['router_id']);
		$router->fetch();
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $router->getUserId())) {
			$smarty->assign('router', $router);
			
			$smarty->display("header.tpl.html");
			$smarty->display("interface_add.tpl.html");
			$smarty->display("footer.tpl.html");
		} else {
			Permission::denyAccess(PERM_ROOT, $router->getUserId());
		}
	} elseif($_GET['section']=='insert_add') {
		$router = new Router((int)$_GET['router_id']);
		$router->fetch();
		
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $router->getUserId())) {
			$networkinterface = new Networkinterface(false, (int)$_GET['router_id'], $_POST['name']);
			if($networkinterface->fetch()==false) {
				$networkinterface->store();
				
				$message[] = array("Das Netzwerkinterface ".$_POST['name']." wurde hinzugefügt.", 1);
				Message::setMessage($message);
				header('Location: ./router_config.php?router_id='.$_GET['router_id']);
			} else {
				$message[] = array("Das Netzwerkinterface ".$_POST['name']." existiert bereits.", 2);
				Message::setMessage($message);
				header('Location: ./router_config.php?router_id='.$_GET['router_id']);
			}
		} else {
			Permission::denyAccess(PERM_ROOT, $router->getUserId());
		}
	} elseif($_GET['section']=='delete') {
		$networkinterface = new Networkinterface((int)$_GET['interface_id']);
		$networkinterface->fetch();
		
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $networkinterface->getRouter()->getUserId())) {
			if($networkinterface->delete())
				$message[] = array("Das Netzwerkinterface ".$networkinterface->getName()." wurde entfernt.", 1);
			else
				$message[] = array("Das Netzwerkinterface ".$networkinterface->getName()." konnte nicht entfernt werden.", 1);
			Message::setMessage($message);
			header('Location: ./router_config.php?router_id='.$networkinterface->getRouterId());
		} else {
			Permission::denyAccess(PERM_ROOT, $networkinterface->getRouter()->getUserId());
		}
	}
?>