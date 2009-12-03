<?php
	require_once('./config/runtime.inc.php');
	require_once './lib/classes/extern/jsonRPCServer.php';
	require_once('./lib/classes/api/crawl.class.php');
	
	$myExample = new Crawl();
	jsonRPCServer::handle($myExample)
		or print 'no request';
?>