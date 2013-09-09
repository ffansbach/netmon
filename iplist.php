<?php
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Network.class.php');
	require_once(ROOT_DIR.'/lib/core/Iplist.class.php');

	$network = new Network((int)$_GET['network_id']);
	$network->fetch();
	$smarty->assign('network', $network);
	
	$iplist = new Iplist(false, (int)$_GET['network_id']);
	$smarty->assign('iplist', $iplist->getIplist());
	
	$smarty->display("header.tpl.html");
	$smarty->display("iplist.tpl.html");
	$smarty->display("footer.tpl.html");
?>