<?php

// +---------------------------------------------------------------------------+
// index.php
// Netmon, Freifunk Netzverwaltung und Monitoring Software
//
// Copyright (c) 2009 Clemens John <clemens-john@gmx.de>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 3
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+/

/**
 * This file contains the class for the nodelist site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class nodelist {
	function __construct(&$smarty) {
		if (!isset($_GET['section'])) {
		  $smarty->assign('nodelist', $this->getNodeList());
		  $smarty->assign('vpnlist', $this->getVpnList());
		  $smarty->assign('servicelist', $this->getServiceList());
		  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	      $smarty->assign('get_content', "nodelist");
		}
		
	}

	function getNodeList() {
	$db = new mysqlClass;
    	$result = $db->mysqlQuery("SELECT id FROM services WHERE typ='node' ORDER BY id");
      	while($row = mysql_fetch_assoc($result)) {
	  $serviceses[] = $row['id'];
    	}
	unset($db);

	foreach ($serviceses as $services) {
	  $db = new mysqlClass;
	  $result = $db->mysqlQuery("SELECT 
crawl_data.crawl_time, crawl_data.uptime, crawl_data.status,
services.id as service_id, services.title as services_title, services.typ, services.crawler,
nodes.user_id, nodes.node_ip, nodes.id as node_id, nodes.subnet_id,
subnets.subnet_ip, subnets.title,
users.nickname

FROM crawl_data

LEFT JOIN services ON (services.id = crawl_data.service_id)
LEFT JOIN nodes ON (nodes.id = services.node_id)
LEFT JOIN subnets ON (subnets.id = nodes.subnet_id)
LEFT JOIN users ON (users.id = nodes.user_id)

WHERE service_id='$services' ORDER BY crawl_data.id DESC LIMIT 1");
      	while($row = mysql_fetch_assoc($result)) {
	  $nodelist[] = $row;
    	}
	unset($db);
}
    	return $nodelist;
	}

	function getVpnList() {
	$db = new mysqlClass;
    	$result = $db->mysqlQuery("SELECT id FROM services WHERE typ='vpn' ORDER BY id");
      	while($row = mysql_fetch_assoc($result)) {
	  $serviceses[] = $row['id'];
    	}
	unset($db);

	foreach ($serviceses as $services) {
	  $db = new mysqlClass;
	  $result = $db->mysqlQuery("SELECT 
crawl_data.crawl_time, crawl_data.uptime, crawl_data.status,
services.id as service_id, services.title as services_title, services.typ, services.crawler,
nodes.id as node_id, nodes.user_id, nodes.node_ip, nodes.subnet_id,
subnets.subnet_ip, subnets.title,
users.nickname

FROM crawl_data

LEFT JOIN services ON (services.id = crawl_data.service_id)
LEFT JOIN nodes ON (nodes.id = services.node_id)
LEFT JOIN subnets ON (subnets.id = nodes.subnet_id)
LEFT JOIN users ON (users.id = nodes.user_id)

WHERE service_id='$services' ORDER BY crawl_data.id DESC LIMIT 1");
      	while($row = mysql_fetch_assoc($result)) {
	  $nodelist[] = $row;
    	}
	unset($db);
}
    	return $nodelist;
	}
	
	function getServiceList() {
	$db = new mysqlClass;
    	$result = $db->mysqlQuery("SELECT id FROM services WHERE typ='service' ORDER BY id");
      	while($row = mysql_fetch_assoc($result)) {
	  $serviceses[] = $row['id'];
    	}
	unset($db);

	foreach ($serviceses as $services) {
	  $db = new mysqlClass;
	  $result = $db->mysqlQuery("SELECT 
crawl_data.crawl_time, crawl_data.uptime, crawl_data.status,
services.id as service_id, services.title as services_title, services.typ, services.crawler,
nodes.id as node_id, nodes.user_id, nodes.node_ip, nodes.subnet_id,
subnets.subnet_ip, subnets.title,
users.nickname

FROM crawl_data

LEFT JOIN services ON (services.id = crawl_data.service_id)
LEFT JOIN nodes ON (nodes.id = services.node_id)
LEFT JOIN subnets ON (subnets.id = nodes.subnet_id)
LEFT JOIN users ON (users.id = nodes.user_id)

WHERE service_id='$services' ORDER BY crawl_data.id DESC LIMIT 1");

      	while($row = mysql_fetch_assoc($result)) {
	  $nodelist[] = $row;
    	}
	unset($db);
}
    	return $nodelist;
	}
  

}

?>