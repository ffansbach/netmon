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
 * This file contains a class with many helpfull methods.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class Helper {
  public function getNodeInfo($id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT nodes.id as node_id, nodes.user_id, nodes.node_ip, nodes.subnet_id, nodes.vpn_client_cert, nodes.vpn_client_key, DATE_FORMAT(nodes.create_date, '%D %M %Y') as create_date,
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
  
  public function getNodeDataByNodeId($id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT nodes.id as node_id, nodes.user_id, nodes.node_ip, nodes.subnet_id, nodes.vpn_client_cert, nodes.vpn_client_key, DATE_FORMAT(nodes.create_date, '%D %M %Y') as create_date,
				      users.nickname, users.email,
				      subnets.title, subnets.subnet_ip, subnets.vpn_server_ca
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

  public function getSubnetById($subnet_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("select subnet_ip FROM subnets WHERE id='$subnet_id';");
    while($row = mysql_fetch_assoc($result)) {
      $subnet = $row['subnet_ip'];
    }
    unset($db);
    return $subnet;
  }

  public function getIpOfNodeById($node_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("select node_ip FROM nodes WHERE id='$node_id';");
    while($row = mysql_fetch_assoc($result)) {
      $node = $row['node_ip'];
    }
    unset($db);
    return $node;
  }


  public function getNodesByUserId($user_id) {
    $nodes = array();
    $db = new mysqlClass;
    $result = $db->mysqlQuery("select nodes.id, nodes.node_ip, nodes.user_id, subnets.subnet_ip FROM nodes LEFT JOIN subnets on (subnets.id = nodes.subnet_id) WHERE nodes.user_id='$user_id' ORDER BY subnets.subnet_ip, nodes.node_ip;");
    while($row = mysql_fetch_assoc($result)) {
      $nodes[] = $row;
    }
    unset($db);
    return $nodes;
  }

  public function getServicesByNodeId($node_id) {
    $serviceses = array();
    $db = new mysqlClass;
    $result = $db->mysqlQuery("select id FROM services WHERE node_id='$node_id';");
    while($row = mysql_fetch_assoc($result)) {
      $serviceses[] = $row['id'];
    }
    unset($db);
    return $serviceses;
  }

  public function getSubnetsByUserId($user_id) {
    $subnets = array();
    $db = new mysqlClass;
    $result = $db->mysqlQuery("select id, subnet_ip FROM subnets WHERE user_id='$user_id';");
    while($row = mysql_fetch_assoc($result)) {
      $subnets[] = $row;
    }
    unset($db);
    return $subnets;
  }

  public function getNodesBySubnetId($subnet_id) {
    $nodes = array();
    $db = new mysqlClass;
    $result = $db->mysqlQuery("select id FROM nodes WHERE subnet_id='$subnet_id';");
    while($row = mysql_fetch_assoc($result)) {
      $nodes[] = $row['id'];
    }
    unset($db);
    return $nodes;
  }


  public function getSubnetIpOfNodeById($node_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT subnets.subnet_ip FROM nodes
			       LEFT JOIN subnets on (subnets.id=nodes.subnet_id)
			       WHERE nodes.id='$node_id';");
    while($row = mysql_fetch_assoc($result)) {
      $subnet = $row['subnet_ip'];
    }
    unset($db);
    return $subnet;
  }

	function getUserByID($id) {
		$db = new mysqlClass;
		$result = $db->mysqlQuery("SELECT * FROM users WHERE id=$id");
    
		while($row = mysql_fetch_assoc($result)) {
		      $user = $row;
		}
		return $user;
	}

	function getNodelistByUserID($id) {
	$db = new mysqlClass;
    	$result = $db->mysqlQuery("SELECT
nodes.id, nodes.user_id, nodes.node_ip, nodes.subnet_id,
users.nickname,
LEFT(subnets.title, 25) as title, subnets.subnet_ip

FROM nodes
LEFT JOIN users ON (users.id=nodes.user_id)
LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)

WHERE users.id=$id
ORDER BY subnets.subnet_ip, nodes.node_ip
");
    
    	while($row = mysql_fetch_assoc($result)) {
		$row['is_node_owner'] = usermanagement::isThisUserOwner($row['user_id']);
      		$nodelist[] = $row;
    	}
	unset($db);
    	return $nodelist;
		
	}

  function getSubnetlistByUserID($id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT COUNT(nodes.id) as nodes_in_net, subnets.id, subnets.subnet_ip, subnets.title FROM  subnets LEFT JOIN nodes on (nodes.subnet_id=subnets.id) WHERE subnets.user_id=$id GROUP BY subnets.id");
    
    while($row = mysql_fetch_assoc($result)) {
      $subnetlist[] = $row;
    }
    unset($db);
    return $subnetlist;	
  }

  public function getServiceseByNodeId($node_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT id, node_id, title, description, typ, crawler, create_date FROM  services WHERE services.node_id=$node_id ORDER BY id");
    while($row = mysql_fetch_assoc($result)) {
      $servicelist[] = $row;
    }
    unset($db);
    return $servicelist;
  }

  public function getServiceseBySubnetId($subnet_id) {
    $servicelist = array();
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT nodes.id as node_id, nodes.node_ip, services.id as service_id, services.zone_start, services.zone_end
FROM nodes
LEFT JOIN services ON (services.node_id=nodes.id)
WHERE nodes.subnet_id='$subnet_id'");
    while($row = mysql_fetch_assoc($result)) {
      $servicelist[] = $row;
    }
    unset($db);
    return $servicelist;
  }

  public function getServiceDataByServiceId($service_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT services.id as service_id, services.node_id, services.title, services.description, services.typ, services.radius, services.crawler, services.visible, services.create_date,
			       nodes.node_ip,
			       subnets.subnet_ip, subnets.vpn_server_ca, subnets.vpn_server_cert, subnets.vpn_server_key, subnets.vpn_server_pass,
			       users.id as user_id, users.nickname, users.email
			       FROM  services
			       LEFT JOIN nodes ON (nodes.id=services.node_id)
			       LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
			       LEFT JOIN users ON (users.id=nodes.user_id)
			       WHERE services.id=$service_id");
    while($row = mysql_fetch_assoc($result)) {
      $service = $row;
    }
    unset($db);
    return $service;
  }

  public function getSubnetDataBySubnetID($subnet_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT subnets.vpn_server, subnets.vpn_server_port, subnets.vpn_server_device, subnets.vpn_server_proto, subnets.vpn_server_ca, subnets.vpn_server_cert, subnets.vpn_server_key, subnets.vpn_server_pass
			       FROM  subnets
			       WHERE subnets.id=$subnet_id");
    while($row = mysql_fetch_assoc($result)) {
      $service = $row;
    }
    unset($db);
    return $service;
  }

  public function getExistingRangesBySubnetId($subnet_id) {
    $zones = array();
    $services = Helper::getServiceseBySubnetId($subnet_id);
    foreach ($services as $service) {
      for ($i=$service['zone_start']; $i<=$service['zone_end']; $i++) {
	$zones[$i] = array('range'=>$i, 'node_id'=>$service['node_id'], 'service_id'=>$service['service_id']);
      }
    }
    unset($db);
    return $zones;
  }

function object2array($object) {
if (is_object($object) || is_array($object)) {
foreach ($object as $key => $value) {
//print "$key\r\n";
$array[$key] = Helper::object2array($value);
}
}else {
$array = $object;
}
return $array;
}

}

?>