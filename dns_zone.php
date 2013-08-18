<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/classes/core/DnsZone.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/DnsZoneList.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/DnsRessourceRecordList.class.php');

	if(!isset($_GET['section']) AND isset($_GET['dns_zone_id'])) {
		$dns_zone = new DnsZone((int)$_GET['dns_zone_id']);
		$dns_zone->fetch();
		$smarty->assign('dns_zone', $dns_zone);
		
		$dns_ressource_record_list = new DnsRessourceRecordList(22, 1);
		$smarty->assign('dns_ressource_record_list', $dns_ressource_record_list->getDnsRessourceRecordList());
		
		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.php");
		$smarty->display("dns_zone.tpl.php");
		$smarty->display("footer.tpl.php");
		
		//TODO Ressource record list of zone
	} elseif($_GET['section'] == 'insert_add') {
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
	} elseif($_GET['section'] == 'delete') {
		$dns_zone = new DnsZone((int)$_GET['dns_zone_id']);
		$dns_zone->fetch();
		$dns_zone_name = $dns_zone->getName();
		$dns_zone->delete();
		
		$message[] = array('Die DNS-Zone '.$dns_zone_name.' wurde gelöscht.', 1);
		Message::setMessage($message);
		
		header('Location: ./dns_zone.php');
	} else {
		$dns_zone_list = new DnsZoneList();
		$smarty->assign('dns_zone_list', $dns_zone_list->getDnsZoneList());

		$smarty->assign('message', Message::getMessage());
		$smarty->display("header.tpl.php");
		$smarty->display("dns_zones.tpl.php");
		$smarty->display("footer.tpl.php");
	}
?>