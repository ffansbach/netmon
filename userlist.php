<?php
	require_once('runtime.php');
	require_once('lib/core/user_old.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	if(empty($_GET['section'])) {
		if (Permission::checkPermission(PERM_USER)) {
			$smarty->assign('userlist', User_old::getUserList());
		
			$smarty->display("header.tpl.html");
			$smarty->display("userlist.tpl.html");
			$smarty->display("footer.tpl.html");
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen die Benutzerliste einsehen!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	} elseif($_GET['section']=="export_vcard30") {
		if (Permission::checkPermission(PERM_USER)) {
			header("Content-Type: text/plain");
			header("Content-Disposition: attachment; filename=netmon_userlist.vcf");
			echo User_old::exportUserListAsvCard30();
		} else {
			$message[] = array("Nur eingeloggte Benutzer dürfen die Beutzerliste exportieren!", 2);
			Message::setMessage($message);
			header('Location: ./login.php');
		}
	}
?>