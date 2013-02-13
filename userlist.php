<?php
	require_once('runtime.php');
	require_once('./lib/classes/core/userlist.class.php');
	
	if(empty($_GET['section'])) {
		$smarty->assign('userlist', UserList::getList());
		
		$smarty->display("header.tpl.php");
		$smarty->display("userlist.tpl.php");
		$smarty->display("footer.tpl.php");
	} elseif($_GET['section']=="export_vcard30") {
		header("Content-Type: text/plain");
		header("Content-Disposition: attachment; filename=netmon_userlist.vcf");
		echo UserList::exportUserListAsvCard30();
	}
?>