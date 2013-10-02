<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/EventNotificationList.class.php');
	require_once(ROOT_DIR.'/lib/core/Routerlist.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
		$event_notification = new EventNotification((int)$_GET['event_notification_id']);
		$event_notification->fetch();
		
		if(permission::checkIfUserIsOwnerOrPermitted(PERM_ROOT, $event_notification->getUserId())) {
			if($event_notification->delete())
				$message[] = array('Die Benachrichtigung wurde gelöscht.', 1);
			else
				$message[] = array('Die Benachrichtigung konnte nicht gelöscht werden.', 2);
			Message::setMessage($message);
			header('Location: ./event_notifications.php');
		} else {
			Permission::denyAccess(PERM_ROOT, $event_notification->getUserId());
		}
	} elseif (empty($_POST)) {
		if(Permission::checkPermission(PERM_USER)) {
			$routerlist = new Routerlist(false, false, false, false, false, false, false, false, 0, -1);
			$routerlist->sort("hostname", "asc");
			$smarty->assign('routerlist', $routerlist->getRouterlist());
			
			$event_notification_list = new EventNotificationList($_SESSION['user_id']);
			$smarty->assign('event_notification_list', $event_notification_list->getEventNotificationList());
			
			$smarty->display("header.tpl.html");
			$smarty->display("event_notifications.tpl.html");
			$smarty->display("footer.tpl.html");
		} else {
			Permission::denyAccess(PERM_USER);
		}
	} else {
		if(Permission::checkPermission(PERM_USER)) {
			$event_notification = new EventNotification(false, (int)$_SESSION['user_id'], $_POST['action'], (int)$_POST['object'], true);
			if($event_notification->store())
				$message[] = array('Die Benachrichtigung wurde eingetragen.', 1);
			else
				$message[] = array('Die Benachrichtigung konnte nicht eingetragen werden.', 2);
			Message::setMessage($message);
			header('Location: ./event_notifications.php');
		} else {
			Permission::denyAccess(PERM_USER);
		}
	}
?>