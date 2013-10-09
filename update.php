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
	
	/*
	Script to remove duplicated ip adresses from interfaces
	This happens when you switch from shortened ipv6 to extracted ipv6 format in db
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Iplist.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterfacelist.class.php');
	
	$networkinterfacelist = new Networkinterfacelist(false, 0, -1);
	$networkinterfacelist = $networkinterfacelist->getNetworkinterfacelist();
	
	foreach($networkinterfacelist as $networkinterface) {
		echo $networkinterface->getName()."<br>";
		$iplist = new Iplist($networkinterface->getNetworkinterfaceId(), 0, -1, "create_date", "desc");
		$iplist->deleteDuplicates();
	}*/
	
	ALTER TABLE chipsets DROP INDEX name
	ALTER TABLE `chipsets` ADD UNIQUE (`name`);
	ALTER TABLE `chipsets` CHANGE `hardware_name` `hardware_name` VARCHAR( 30 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
	ALTER TABLE `chipsets` DROP `user_id` ;
	
	ALTER TABLE config DROP INDEX name
	ALTER TABLE `config` ADD UNIQUE (`name`);
	
?>