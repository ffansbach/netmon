<?php
	require_once('./config/runtime.inc.php');
	require_once './lib/classes/extern/jsonRPCServer.php';
	require_once('./lib/classes/api/main.class.php');
	
	$myExample = new main();
	jsonRPCServer::handle($myExample)
		or print 'no request';
?>