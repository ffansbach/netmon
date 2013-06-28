<?php
	require_once('runtime.php');
	
	//Move data from table interface_ips into table ips and remove table interface_ips
	//UPDATE ips SET interface_id = (SELECT interface_id FROM interface_ips WHERE interface_ips.ip_id=ips.id)
	//DROP TABLE interface_ips
?>