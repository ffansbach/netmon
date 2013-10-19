<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/DnsZone.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsZoneList.class.php');
	require_once(ROOT_DIR.'/lib/core/DnsRessourceRecordList.class.php');

	if(!isset($_GET['section']) AND isset($_GET['dns_zone_id'])) {
		$dns_zone = new DnsZone((int)$_GET['dns_zone_id']);
		$dns_zone->fetch();
		$smarty->assign('dns_zone', $dns_zone);
		
		$dns_ressource_record_list = new DnsRessourceRecordList((int)$_GET['dns_zone_id']);
		$smarty->assign('dns_ressource_record_list', $dns_ressource_record_list->getDnsRessourceRecordList());
		
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("dns_zone.tpl.html");
		$smarty->display("footer.tpl.html");
		
		//TODO Ressource record list of zone
	} elseif($_GET['section'] == 'insert_add') {
		if(Permission::checkPermission(PERM_USER)) {
			$dns_zone = new DnsZone(false, (int)$_SESSION['user_id'], $_POST['name'], $_POST['pri_dns'], $_POST['sec_dns'],
									(int)$_POST['serial'], (int)$_POST['refresh'], (int)$_POST['retry'],
									(int)$_POST['expire'], (int)$_POST['ttl']);
			if($dns_zone->store()) {
				$message[] = array('Neue DNS-Zone '.$_POST['name'].' wurde eingetragen.', 1);
			} else {
				$message[] = array('Neue DNS-Zone '.$_POST['name'].' konnte nicht eingetragen werden.', 2);
			}
			Message::setMessage($message);
			
			header('Location: ./dns_zone.php');
		} else {
			Permission::denyAccess(PERM_USER);
		}
	} elseif($_GET['section'] == 'delete') {
		$dns_zone = new DnsZone((int)$_GET['dns_zone_id']);
		$dns_zone->fetch();
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $dns_zone->getUserId())) {
			$dns_zone_name = $dns_zone->getName();
			$dns_zone->delete();
			
			$message[] = array('Die DNS-Zone '.$dns_zone_name.' wurde gelöscht.', 1);
			Message::setMessage($message);
			
			header('Location: ./dns_zone.php');
		} else {
			Permission::denyAccess(PERM_ROOT, $dns_zone->getUserId());
		}
	} elseif($_GET['section'] == 'edit') {
		$dns_zone = new DnsZone((int)$_GET['dns_zone_id']);
		$dns_zone->fetch();
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $dns_zone->getUserId())) {
			$smarty->assign('dns_zone', $dns_zone);
					
			$smarty->assign('message', Message::getMessage());
			$smarty->display("header.tpl.html");
			$smarty->display("dns_zone_edit.tpl.html");
			$smarty->display("footer.tpl.html");
		} else {
			Permission::denyAccess(PERM_ROOT, $dns_zone->getUserId());
		}
	} elseif($_GET['section'] == 'insert_edit') {
		$dns_zone = new DnsZone((int)$_GET['dns_zone_id']);
		$dns_zone->fetch();
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $dns_zone->getUserId())) {
			$dns_zone->setName($_POST['name']);
			$dns_zone->setPriDns($_POST['pri_dns']);
			$dns_zone->setSecDns($_POST['sec_dns']);
			$dns_zone->setRefresh((int)$_POST['refresh']);
			$dns_zone->setRetry((int)$_POST['retry']);
			$dns_zone->setExpire((int)$_POST['expire']);
			$dns_zone->setTtl((int)$_POST['ttl']);
			if($dns_zone->store()) {
				$message[] = array('Die Änderungen wurden gespeichert.', 1);
			} else {
				$message[] = array('Die Änderungen konnten nicht gespeichert werden.', 2);
			}
			Message::setMessage($message);
			header('Location: ./dns_zone.php?dns_zone_id='.$_GET['dns_zone_id']);
		} else {
			Permission::denyAccess(PERM_ROOT, $dns_zone->getUserId());
		}
	} else {
		$dns_zone_list = new DnsZoneList();
		$smarty->assign('dns_zone_list', $dns_zone_list->getDnsZoneList());

		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.html");
		$smarty->display("dns_zones.tpl.html");
		$smarty->display("footer.tpl.html");
	}
?>