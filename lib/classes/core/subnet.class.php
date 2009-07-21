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
 * This file contains the class for the subnet site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class subnet {
	function getSubnet($subnet_id) {
		try {
			$sql = "SELECT subnets.id, subnets.subnet_ip, subnets.user_id, DATE_FORMAT(subnets.create_date, '%D %M %Y') as create_date, subnets.title, subnets.description, subnets.longitude, subnets.latitude, subnets.radius, subnets.vpn_server, subnets.vpn_server_port, subnets.vpn_server_device,	subnets.vpn_server_proto,
				      users.nickname
			       FROM subnets
			       LEFT JOIN users ON (users.id=subnets.user_id)
			       WHERE subnets.id=$subnet_id";
			$result = DB::getInstance()->query($sql); 
			$subnet = $result->fetch(PDO::FETCH_ASSOC); 
		} 
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
		return $subnet;
	}
	
	public function getIPStatus($subnet_id) {
		$iplist = array();
		
		//Nodes eintragen
		foreach (editingHelper::getExistingNodesWithID($subnet_id) as $node) {
		$iplist[$node['node_ip']] = array('ip'=>$node['node_ip'],
									      'node_id'=>$node['id'],
										  'typ'=>"node");
		}
		
		foreach (Helper::getExistingRangesBySubnetId($subnet_id) as $range) {
			$iplist[$range['range']] = array('ip'=>$range['range'],
											 'node_id'=>$range['node_id'],
											 'service_id'=>$range['service_id'],
											 'typ'=>"range");
		}
		
		for ($i=0; $i<=255; $i++) {
			if (!isset($iplist[$i])) {
				$iplist[$i] = array('ip'=>$i,
									'typ'=>"free");				
			}
			ksort($iplist);
		}
		
		return $iplist;
	}
}

?>