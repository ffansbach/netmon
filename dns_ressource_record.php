<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/DnsZone.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsZoneList.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsRessourceRecord.class.php');
	
	if(!isset($_GET['section']) AND isset($_GET['dns_ressource_record_id'])) {
		//show ressource record
	} elseif($_GET['section'] == 'add') {
		if(Permission::checkPermission(PERM_USER)) {
			//pass system messages to the template
			$smarty->assign('message', Message::getMessage());
			
			$dns_zone_list = new DnsZoneList();
			$smarty->assign('dns_zone_list', $dns_zone_list->getDnsZoneList());
			
			//compile the template and sorround the main content by footer and header template
			$smarty->display("header.tpl.html");
			$smarty->display("dns_ressource_record_add.tpl.html");
			$smarty->display("footer.tpl.html");
		} else {
			Permission::denyAccess(PERM_USER);
		}
	} elseif($_GET['section'] == 'insert_add') {
		if(Permission::checkPermission(PERM_USER)) {
			$dns_ressource_record = new DnsRessourceRecord(false, (int)$_POST['dns_zone_id'], (int)$_SESSION['user_id'],
														$_POST['host'], $_POST['type'], $_POST['pri'], (int)$_POST['destination']);
			if($dns_ressource_record->store()) {
				$message[] = array('Der Ressource Record '.$dns_ressource_record->getHost().' wurde gespeichert.', 1);
			} else {
				$message[] = array('Der Ressource Record konnte nicht gespeichert werden.', 2);
			}
			Message::setMessage($message);
			header('Location: ./dns_zone.php?dns_zone_id='.$_POST['dns_zone_id']);
		} else {
			Permission::denyAccess(PERM_USER);
		}
	} elseif($_GET['section'] == 'delete') {
		$dns_ressource_record = new DnsRessourceRecord((int)$_GET['dns_ressource_record_id']);
		$dns_ressource_record->fetch();
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $dns_ressource_record->getUserId())) {
			if($dns_ressource_record->delete()) {
				$message[] = array('Der Ressource Record '.$dns_ressource_record->getHost().' wurde gelöscht.', 1);
			} else {
				$message[] = array('Der Ressource Record '.$dns_ressource_record->getHost().' konnte nicht gelöscht werden.', 2);
			}
			Message::setMessage($message);
			header('Location: ./dns_zone.php?dns_zone_id='.$dns_ressource_record->getDnsZoneId());
		} else {
			Permission::denyAccess(PERM_ROOT, $dns_ressource_record->getUserId());
		}
	}
?>