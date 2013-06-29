<?php
	require_once('runtime.php');
	
	//Move data from table interface_ips into table ips and remove table interface_ips
	//ALTER TABLE `ips` ADD `interface_id` INT( 11 ) NOT NULL AFTER `id` 
	//UPDATE ips SET interface_id = (SELECT interface_id FROM interface_ips WHERE interface_ips.ip_id=ips.id)
	//DROP TABLE interface_ips
	
	//ALTER TABLE `ips` ADD `protected` BOOLEAN NOT NULL AFTER `ipv` 
	//UPDATE ips SET protected=1
	
	//ALTER TABLE `interfaces` ADD `protected` BOOLEAN NOT NULL AFTER `vpn_client_key` 
	//UPDATE interfaces SET protected=1
	
	//ALTER TABLE `crawl_routers` ADD `client_count` INT NOT NULL 
	//DROP TABLE crawl_clients_count
	
?>