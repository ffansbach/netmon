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
	
	ALTER TABLE crawl_batman_advanced_interfaces DROP INDEX crawl_batman_advanced_interfaces_id
	ALTER TABLE crawl_batman_advanced_interfaces DROP INDEX router_id_2
	ALTER TABLE crawl_batman_advanced_interfaces DROP INDEX crawl_cycle_id_2
	
	ALTER TABLE `crawl_batman_advanced_originators` CHANGE `originator` `originator` VARCHAR( 17 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
	ALTER TABLE `crawl_batman_advanced_originators` CHANGE `nexthop` `nexthop` VARCHAR( 17 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
	
	ALTER TABLE `crawl_interfaces` CHANGE `name` `name` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
	ALTER TABLE `crawl_interfaces` CHANGE `mac_addr` `mac_addr` VARCHAR( 17 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
	ALTER TABLE `crawl_interfaces` DROP `ipv4_addr` ,
								   DROP `ipv6_addr` ,
								   DROP `ipv6_link_local_addr` ;
	ALTER TABLE `crawl_interfaces` CHANGE `wlan_bssid` `wlan_bssid` VARCHAR( 17 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
	ALTER TABLE crawl_interfaces DROP INDEX crawl_interfaces_id
	ALTER TABLE crawl_interfaces DROP INDEX router_id_2
	ALTER TABLE crawl_interfaces DROP INDEX crawl_cycle_id_2
	
	DROP TABLE crawl_ips
	
	ALTER TABLE crawl_routers DROP INDEX id
	ALTER TABLE crawl_routers DROP INDEX crawl_routers_id
	ALTER TABLE crawl_routers DROP INDEX status
	ALTER TABLE crawl_routers DROP INDEX router_id_2
	ALTER TABLE crawl_routers DROP INDEX crawl_cycle_id_2
	ALTER TABLE `crawl_routers` DROP `ping` ;
	ALTER TABLE `crawl_routers` CHANGE `memory_total` `memory_total` INT( 11 ) NOT NULL ;
	ALTER TABLE `crawl_routers` CHANGE `memory_caching` `memory_caching` INT( 11 ) NOT NULL ;
	ALTER TABLE `crawl_routers` CHANGE `memory_buffering` `memory_buffering` INT( 11 ) NOT NULL ;
	ALTER TABLE `crawl_routers` CHANGE `memory_free` `memory_free` INT( 11 ) NOT NULL ;
	ALTER TABLE `crawl_routers` CHANGE `hostname` `hostname` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
	
	ALTER TABLE `crawl_services` DROP `crawled_ipv4_addr` ;
	
	ALTER TABLE `events` ADD INDEX ( `object` ) ;
	ALTER TABLE `events` ADD INDEX ( `object_id` ) ;
	
ALTER TABLE `event_notifications` ADD INDEX ( `user_id` ) ;
ALTER TABLE `event_notifications` ADD INDEX ( `action` ) ;
ALTER TABLE `event_notifications` ADD INDEX ( `object` ) ;
	
	
ALTER TABLE `router_adds` ADD INDEX ( `router_id` ) ;

ALTER TABLE `service_dns_ressource_records` ADD INDEX ( `service_id` ) ;
ALTER TABLE `service_dns_ressource_records` ADD INDEX ( `dns_ressource_record_id` ) ;

ALTER TABLE `service_ips` ADD INDEX ( `service_id` ) ;
ALTER TABLE `service_ips` ADD INDEX ( `ip_id` ) ;

ALTER TABLE `variable_splash_clients` ADD INDEX ( `router_id` ) ;

ALTER TABLE `interfaces` DROP `project_id` ;
ALTER TABLE `interfaces` CHANGE `name` `name` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE `interfaces` DROP `protected` ;
ALTER TABLE interfaces DROP INDEX router_id_2

ALTER TABLE `ips` DROP `project_id` ;
ALTER TABLE `ips` DROP `netmask` ;
ALTER TABLE `ips` DROP `ipv` ;
ALTER TABLE `ips` DROP `protected` ;
ALTER TABLE ips DROP INDEX router_id_2

DROP TABLE projects



?>