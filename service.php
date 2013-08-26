<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Service.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsRessourceRecordList.class.php');
	require_once(ROOT_DIR.'/lib/core/Routerlist.class.php');
	
	if(!isset($_GET['section']) AND isset($_GET['service_id'])) {
		$smarty->assign('message', Message::getMessage());
		
		$service = new Service((int)$_GET['service_id']);
		$service->fetch();
		$smarty->assign('service', $service);
		
		$smarty->display("header.tpl.php");
		$smarty->display("service.tpl.php");
		$smarty->display("footer.tpl.php");
	} elseif($_GET['section'] == 'add') {
		//pass system messages to the template
		$smarty->assign('message', Message::getMessage());
		
		$dns_ressource_record_list = new DnsRessourceRecordList(false, (int)$_SESSION['user_id']);
		$smarty->assign('dns_ressource_record_list', $dns_ressource_record_list->getDnsRessourceRecordList());
		
		$routerlist = new Routerlist((int)$_SESSION['user_id']);
		$smarty->assign('routerlist', $routerlist->getRouterlist());
		
		//compile the template and sorround the main content by footer and header template
		$smarty->display("header.tpl.php");
		$smarty->display("service_add.tpl.php");
		$smarty->display("footer.tpl.php");
	} elseif($_GET['section'] == 'insert_add') {
		$service = new Service(false, (int)$_GET['user_id'], $_POST['title'], $_POST['description'], (int)$_POST['port'], 1,
							   $_POST['iplist'], $_POST['dns_ressource_record_list']);
		
		if($service->store()) {
			$message[] = array('Der Service '.$service->getTitle().' wurde gespeichert.', 1);
		} else {
			$message[] = array('Der Service konnte nicht gespeichert werden.', 2);
		}
		Message::setMessage($message);
		
		header('Location: ./user.php?user_id='.$_GET['user_id']);
	} elseif($_GET['section'] == 'delete') {
		$service = new Service((int)$_GET['service_id']);
		$service->fetch();
		if($service->delete()) {
			$message[] = array('Der Dienst '.$service->getTitle().' wurde gelöscht.', 1);
		} else {
			$message[] = array('Der Dienst '.$service->getTitle().' konnte nicht gelöscht werden.', 2);
		}
		Message::setMessage($message);
		header('Location: ./user.php?user_id='.$service->getUserId());
	}
?>