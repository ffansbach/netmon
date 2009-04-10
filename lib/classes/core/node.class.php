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
 * This file contains the class for the node site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class node {
  function __construct(&$smarty) {
    if (!isset($_GET['section'])) {
      $smarty->assign('node', $this->getNodeInfo($_GET['id']));
      $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
      $smarty->assign('is_node_owner', $this->is_node_owner);
      $smarty->assign('servicelist', $this->getServiceList($_GET['id']));
      $smarty->assign('get_content', "node");
    }
  }

  function getNodeInfo($id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT nodes.id, nodes.user_id, nodes.node_ip, nodes.subnet_id, DATE_FORMAT(nodes.create_date, '%D %M %Y') as create_date,
				      users.nickname,
					  subnets.title, subnets.subnet_ip
				  FROM nodes
				   LEFT JOIN users ON (users.id=nodes.user_id)
				   LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
				   WHERE nodes.id=$id;");
    
    while($row = mysql_fetch_assoc($result)) {
      $node = $row;
    }

    $node['is_node_owner'] = usermanagement::isThisUserOwner($node['user_id']);
    return $node;
  }

  public function getServiceList($node_id) {
	$db = new mysqlClass;
    	$result = $db->mysqlQuery("SELECT id FROM services WHERE node_id='$node_id' ORDER BY id");
      	while($row = mysql_fetch_assoc($result)) {
	  $serviceses[] = $row['id'];
    	}
	unset($db);

	foreach ($serviceses as $services) {
	  $db = new mysqlClass;
	  $result = $db->mysqlQuery("SELECT 
crawl_data.crawl_time, crawl_data.uptime, crawl_data.status,
services.id as service_id, services.title as services_title, services.typ, services.crawler

FROM crawl_data

LEFT JOIN services ON (services.id = crawl_data.service_id)

WHERE service_id='$services' ORDER BY crawl_data.id DESC LIMIT 1");
      	while($row = mysql_fetch_assoc($result)) {
	  $servicelist[] = $row;
    	}
	unset($db);
    }
    return $servicelist;
  }

}

?>