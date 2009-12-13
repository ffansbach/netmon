<?php
	require_once('./config/runtime.inc.php');
	require_once('./lib/classes/api/crawl.class.php');
	require_once 'lib/classes/extern/Zend/Json/Server.php';
	
	$server = new Zend_Json_Server();
	$server->setClass('Crawl');
	
	if ('GET' == $_SERVER['REQUEST_METHOD']) {
		// Indicate the URL endpoint, and the JSON-RPC version used:
		$server->setTarget('api_main.php')
			   ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
		
		// Grab the SMD
		$smd = $server->getServiceMap();
		
		// Return the SMD to the client
		header('Content-Type: application/json');
		echo $smd;
		return;
	}
	
	$server->handle();
?>