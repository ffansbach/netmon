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
 * This file contains the class for the service site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class service {
  function __construct(&$smarty) {
    if (!isset($_GET['section'])) {
      $smarty->assign('service_data', $this->getServiceData($_GET['service_id']));
      $smarty->assign('current_crawl', $this->getCurrentCrawlData($_GET['service_id']));
      $smarty->assign('last_online_crawl', $this->getLastOnlineCrawlData($_GET['service_id']));
      $smarty->assign('crawl_history', $this->getCrawlHistory($_GET['service_id']));
      $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
      $smarty->assign('is_node_owner', $this->is_node_owner);
      $smarty->assign('get_content', "service");
    }
  }

  public function getServiceData($service_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT
services.id as service_id, services.node_id, services.title as service_title, services.description as service_description, services.typ, services.crawler, services.zone_start, services.zone_end, services.create_date as service_create_date,
nodes.id as node_id, nodes.node_ip,
subnets.id as subnet_id, subnets.subnet_ip,
users.id as user_id, users.nickname
FROM services
LEFT JOIN nodes ON (nodes.id=services.node_id)
LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
LEFT JOIN users ON (users.id=nodes.user_id)
WHERE services.id='$service_id'");
    while($row = mysql_fetch_assoc($result)) {
      $service_data = $row;
    }
    unset($db);
    return $service_data;
  }

  public function getLastOnlineCrawlData($service_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT id FROM services WHERE node_id='$id' ORDER BY id DESC LIMIT 1");
    while($row = mysql_fetch_assoc($result)) {
      $services = $row['id'];
    }
    unset($db);
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT
id, 
crawl_id,
service_id,
crawl_time,
status,
nickname,
hostname,
email,
location,
prefix,
ssid,
longitude,
latitude,
luciname,
luciversion,
distname,
distversion,
chipset,
cpu,
network,
wireless_interfaces,
uptime,
idletime,
memory_total,
memory_caching,
memory_buffering,
memory_free,
loadavg,
processes,
olsrd_hna,
olsrd_neighbors,
olsrd_links,
olsrd_mid,
olsrd_routes,
olsrd_topology
FROM crawl_data

WHERE service_id='$service_id' AND status='online' ORDER BY id DESC LIMIT 1");
    while($row = mysql_fetch_assoc($result)) {
      $row['olsrd_neighbors'] = unserialize($row['olsrd_neighbors']);
      $last_online_crawl = $row;
    }
    unset($db);
    return $last_online_crawl;
  }

  public function getCurrentCrawlData($service_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT id FROM services WHERE node_id='$id' ORDER BY id DESC LIMIT 1");
    while($row = mysql_fetch_assoc($result)) {
      $services = $row['id'];
    }
    unset($db);
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT
id, 
crawl_id,
service_id,
crawl_time,
status,
nickname,
hostname,
email,
location,
prefix,
ssid,
longitude,
latitude,
luciname,
luciversion,
distname,
distversion,
chipset,
cpu,
network,
wireless_interfaces,
uptime,
idletime,
memory_total,
memory_caching,
memory_buffering,
memory_free,
loadavg,
processes,
olsrd_hna,
olsrd_neighbors,
olsrd_links,
olsrd_mid,
olsrd_routes,
olsrd_topology
FROM crawl_data

WHERE service_id='$service_id' ORDER BY id DESC LIMIT 1");
    while($row = mysql_fetch_assoc($result)) {

      $row['olsrd_neighbors'] = unserialize($row['olsrd_neighbors']);
      $row['olsrd_routes'] = unserialize($row['olsrd_routes']);
      $row['olsrd_topology'] = unserialize($row['olsrd_topology']);

      $last_crawl = $row;
    }
    unset($db);
    return $last_crawl;
  }

  public function getCrawlHistory($service_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT id FROM services WHERE node_id='$id' ORDER BY id DESC LIMIT 10");
    while($row = mysql_fetch_assoc($result)) {
      $services = $row['id'];
    }
    unset($db);
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT
id, 
crawl_id,
service_id,
crawl_time,
status,
nickname,
hostname,
email,
location,
prefix,
ssid,
longitude,
latitude,
luciname,
luciversion,
distname,
distversion,
chipset,
cpu,
network,
wireless_interfaces,
uptime,
idletime,
memory_total,
memory_caching,
memory_buffering,
memory_free,
loadavg,
processes,
olsrd_hna,
olsrd_neighbors,
olsrd_links,
olsrd_mid,
olsrd_routes,
olsrd_topology

FROM crawl_data

WHERE service_id='$service_id' ORDER BY id DESC LIMIT 10");
    while($row = mysql_fetch_assoc($result)) {
      $last_crawl[] = $row;
    }
    unset($db);
    return $last_crawl;
  }
	
}

?>