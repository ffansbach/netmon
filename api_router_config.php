<?php
	require_once('runtime.php');
	require_once('./lib/classes/api/router_config.class.php');
	require_once 'lib/classes/extern/Zend/Json/Server.php';
	
	$server = new Zend_Json_Server();
	$server->setClass('RouterConfig');
	
	if ('GET' == $_SERVER['REQUEST_METHOD']) {
		// Indicate the URL endpoint, and the JSON-RPC version used:
		$server->setTarget('api_router_config.php')
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