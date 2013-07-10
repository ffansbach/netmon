<?php
	require_once('runtime.php');
	
	require_once(ROOT_DIR.'/lib/classes/core/EventNotificationList.class.php');
	require_once(ROOT_DIR.'/lib/classes/core/Routerlist.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
		EventNotification::delete($_GET['event_notification_id']);
		header('Location: ./event_notifications.php');
	} elseif (empty($_POST)) {
		$routerlist = new Routerlist(false, false, false, "hostname", "asc");
		$smarty->assign('routerlist', $routerlist->getRouterlist());
		
		$event_notification_list = new EventNotificationList($_SESSION['user_id']);
		$smarty->assign('event_notification_list', $event_notification_list->getEventNotificationList());
		
		$smarty->display("header.tpl.php");
		$smarty->display("event_notifications.tpl.php");
		$smarty->display("footer.tpl.php");
	} else {
		$event_notification = new EventNotification(false, $_SESSION['user_id'], $_POST['action'], $_POST['object'], true);
		$event_notification->store();
		header('Location: ./event_notifications.php');
	}
?>