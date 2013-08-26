<?php
	require_once('runtime.php');
	require_once('lib/core/Router.class.php');
	require_once('lib/core/Networkinterface.class.php');
	
	if($_GET['section']=='add') {
		$router = new Router((int)$_GET['router_id']);
		$router->fetch();
		$smarty->assign('router', $router);
		
		$smarty->display("header.tpl.php");
		$smarty->display("interface_add.tpl.php");
		$smarty->display("footer.tpl.php");
	} elseif($_GET['section']=='insert_add') {
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
	} elseif($_GET['section']=='delete') {
		if (!Permission::checkIfUserIsOwnerOrPermitted(64, $_GET['interface_id']))
			Permission::denyAccess();
		
		$networkinterface = new Networkinterface((int)$_GET['interface_id']);
		$networkinterface->fetch();
		$networkinterface->delete();
		
		$message[] = array("Das Netzwerkinterface ".$networkinterface->getName()." wurde entfernt.", 1);
		Message::setMessage($message);
		header('Location: ./router_config.php?router_id='.$networkinterface->getRouterId());
	}
?>