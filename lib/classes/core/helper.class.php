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
    try {
      /*** query the database ***/
      $sql = "SELECT nodes.id as node_id, nodes.user_id, nodes.node_ip, nodes.subnet_id, nodes.vpn_client_cert, nodes.vpn_client_key, DATE_FORMAT(nodes.create_date, '%D %M %Y') as create_date,
				      users.nickname,
				      subnets.title, subnets.subnet_ip
				  FROM nodes
				   LEFT JOIN users ON (users.id=nodes.user_id)
				   LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
				   WHERE nodes.id=$id";

      $result = DB::getInstance()->query($sql);

      foreach($result as $row) {
        $node[] = $row;
      }
    }
    catch(PDOException $e) {
      echo $e->getMessage();
    }

    $node['is_node_owner'] = usermanagement::isThisUserOwner($node['user_id']);
    return $node;
  }
  
  public function getNodeDataByNodeId($id) {
    try {
      /*** query the database ***/
      $sql = "SELECT nodes.id as node_id, nodes.user_id, nodes.node_ip, nodes.subnet_id, nodes.vpn_client_cert, nodes.vpn_client_key, DATE_FORMAT(nodes.create_date, '%D %M %Y') as create_date,
				      users.nickname, users.email,
				      subnets.title, subnets.subnet_ip, subnets.vpn_server, subnets.vpn_server_port, subnets.vpn_server_device, subnets.vpn_server_proto, subnets.vpn_server_ca
				   FROM nodes
				   LEFT JOIN users ON (users.id=nodes.user_id)
				   LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
				   WHERE nodes.id=$id";

      $result = DB::getInstance()->query($sql);

      foreach($result as $row) {
        $node[] = $row;
      }
    }
    catch(PDOException $e) {
      echo $e->getMessage();
    }

    $node['is_node_owner'] = usermanagement::isThisUserOwner($node['user_id']);
    return $node;
  }

  public function getSubnetById($subnet_id) {
    try {
      /*** query the database ***/
      $sql = "select subnet_ip FROM subnets WHERE id='$subnet_id'";

      $result = DB::getInstance()->query($sql);

      foreach($result as $row) {
	$subnet = $row['subnet_ip'];
      }
    }
    catch(PDOException $e) {
      echo $e->getMessage();
    }

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

  public function getNodeIdByServiceId($service_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("select nodes.id FROM nodes, services WHERE services.id='$service_id' AND nodes.id=services.node_id;");
    while($row = mysql_fetch_assoc($result)) {
      $node = $row;
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

  public function getServicesByType($type) {
    //Nur Services zurückgeben die der Benutzer sehen darf
    if (!usermanagement::checkPermission(4))
      $visible = "AND visible = 1";
    else
      $visible = "";

    $services = array();
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT services.id as service_id, services.title as services_title, services.typ, services.crawler,
				      nodes.user_id, nodes.node_ip, nodes.id as node_id, nodes.subnet_id,
				      subnets.subnet_ip, subnets.title,
				      users.nickname
			       FROM services
			       LEFT JOIN nodes ON (nodes.id = services.node_id)
			       LEFT JOIN subnets ON (subnets.id = nodes.subnet_id)
			       LEFT JOIN users ON (users.id = nodes.user_id)
			       WHERE services.typ='$type' $visible ORDER BY services.id");
    while($row = mysql_fetch_assoc($result)) {
      $services[] = $row;
    }
    unset($db);
    return $services;
  }

  public function getServicesByNodeId($node_id) {
    //Nur Services zurückgeben die der Benutzer sehen darf
    if (!usermanagement::checkPermission(4))
      $visible = "AND visible = 1";
    else
      $visible = "";

    try {
      /*** query the database ***/
      $sql = "SELECT services.id as service_id, services.title as services_title, services.typ, services.crawler,
				      nodes.user_id, nodes.node_ip, nodes.id as node_id, nodes.subnet_id,
				      subnets.subnet_ip, subnets.title,
				      users.nickname
			       FROM services
			       LEFT JOIN nodes ON (nodes.id = services.node_id)
			       LEFT JOIN subnets ON (subnets.id = nodes.subnet_id)
			       LEFT JOIN users ON (users.id = nodes.user_id)
			       WHERE services.node_id='$node_id' $visible ORDER BY services.id";

      $result = DB::getInstance()->query($sql);

      foreach($result as $row) {
        $services[] = $row;
      }
    }
    catch(PDOException $e) {
      echo $e->getMessage();
    }

    return $services;
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
    $result = $db->mysqlQuery("SELECT services.id as service_id, services.node_id, services.title, services.description, services.typ, services.radius, services.crawler, services.zone_start, services.zone_end, services.visible, services.create_date,
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

  public function getLastOnlineCrawlDataByServiceId($service_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT id FROM services WHERE node_id='$id' ORDER BY id DESC LIMIT 1");
    while($row = mysql_fetch_assoc($result)) {
      $services = $row['id'];
    }
    unset($db);
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT * FROM crawl_data
WHERE service_id='$service_id' AND status='online' ORDER BY id DESC LIMIT 1");
    while($row = mysql_fetch_assoc($result)) {
      $row['olsrd_neighbors'] = unserialize($row['olsrd_neighbors']);
      $last_online_crawl = $row;
    }
    unset($db);
    return $last_online_crawl;
  }

  public function getCurrentCrawlDataByServiceId($service_id) {
    //Belege vor, falls noch nich gecrawlt wurde
    $last_crawl['status'] = "unbekannt";

    //Hole letzten Crawl
    try {
      $sql = "SELECT id, crawl_id, crawl_time, status, nickname as luci_nickname, hostname, email, location, prefix, ssid, longitude, latitude, luciname, luciversion, distname, distversion, chipset, cpu, network, wireless_interfaces, uptime, idletime, memory_total, memory_caching, memory_buffering, memory_free, loadavg, processes, olsrd_hna, olsrd_neighbors, olsrd_links, olsrd_mid, olsrd_routes, olsrd_topology FROM crawl_data
			       WHERE service_id='$service_id' ORDER BY id DESC LIMIT 1";
      $result = DB::getInstance()->query($sql);

      foreach($result as $row) {
	//In der Datenbank als String gespeicherte Objekte unserialisieren und in Arrays verwandeln.
	$row['olsrd_neighbors'] = Helper::object2array(unserialize($row['olsrd_neighbors']));
	$row['olsrd_routes'] = Helper::object2array(unserialize($row['olsrd_routes']));
	$row['olsrd_topology'] = Helper::object2array(unserialize($row['olsrd_topology']));

	//Leerzeichen und Punkte aus Arrayindizies entfernen!
	$row['olsrd_neighbors'] = Helper::removeDotsAndWhitspacesFromArrayInizies($row['olsrd_neighbors']);
	$row['olsrd_routes'] = Helper::removeDotsAndWhitspacesFromArrayInizies($row['olsrd_routes']);
	$row['olsrd_topology'] = Helper::removeDotsAndWhitspacesFromArrayInizies($row['olsrd_topology']);

	$last_crawl = $row;
      }
    }
    catch(PDOException $e) {
      echo $e->getMessage();
    }

    return $last_crawl;
  }

      function object2array($object) {
		if (is_object($object) || is_array($object)) {
			foreach ($object as $key => $value) {
				$array[$key] = Helper::object2array($value);
			}
		}else {
			$array = $object;
		}
		return $array;
	}
	
	public function linkIp2NodeIdAndGetTyp($ip2check, $parentNodeID=false) {
		
		
	}
	
	public function getUserByEmail($email) {
		$db = new mysqlClass;
		$result = $db->mysqlQuery("SELECT * FROM  users WHERE email='$email'");
		while($row = mysql_fetch_assoc($result)) {
			$user = $row;
		}
		unset($db);
		return $user;
	}
	
  function randomPassword($size) 
  { 
    $result = ""; 

    srand((double)microtime()*1000000); 

    for($i=0; $i < $size; $i++) 
    { 
      $num = rand(48,120); 
      while (($num >= 58 && $num <= 64) || ($num >= 91 && $num <= 96)) 
         $num = rand(48,120); 
           
      $result .= chr($num); 
    } 
    return $result; 
  }

function rename_keys(&$value, $key) {
    $value = str_replace(' ', '', $value);
    $value = str_replace('.', '', $value);
}

  public function removeDotsAndWhitspacesFromArrayInizies($array) {
    if (is_array($array)) {
      foreach ($array as $neighbours) {
	$keys = array_keys($neighbours);
	$values = array_values($neighbours);
	
	array_walk($keys, array('Helper', 'rename_keys'));
	$return[] = array_combine($keys, $values);
      }
      return $return;
    } else {
      return $array;
    }
  }

}

?>