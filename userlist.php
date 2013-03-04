<?php
	require_once('runtime.php');
	require_once('lib/classes/core/user.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	if(empty($_GET['section'])) {
		if (Permission::checkPermission(4)) {
			$smarty->assign('userlist', User::getUserList());
		
			$smarty->display("header.tpl.php");
			$smarty->display("userlist.tpl.php");
			$smarty->display("footer.tpl.php");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen die Benutzerliste einsehen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	} elseif($_GET['section']=="export_vcard30") {
		if (Permission::checkPermission(4)) {
			header("Content-Type: text/plain");
			header("Content-Disposition: attachment; filename=netmon_userlist.vcf");
			echo User::exportUserListAsvCard30();
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen die Beutzerliste exportieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
?>