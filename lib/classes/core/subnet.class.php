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

  require_once('./lib/classes/core/subnetcalculator.class.php');

class Subnet {
	function getSubnet($subnet_id) {
		try {
			$sql = "SELECT subnets.id, subnets.host, subnets.netmask, subnets.real_host, subnets.real_netmask, subnets.dhcp_kind, subnets.user_id, subnets.create_date, subnets.title, subnets.description, subnets.polygons, subnets.vpn_server, subnets.vpn_server_port, subnets.vpn_server_device,	subnets.vpn_server_proto,
				      users.nickname
			       FROM subnets
			       LEFT JOIN users ON (users.id=subnets.user_id)
			       WHERE subnets.id=$subnet_id";
			$result = DB::getInstance()->query($sql); 
			$subnet = $result->fetch(PDO::FETCH_ASSOC); 

			$subnet['last_ip'] = SubnetCalculator::getDqLastIp($GLOBALS['net_prefix'].".".$subnet['host'], $subnet['netmask']);
			$subnet['first_ip'] = SubnetCalculator::getDqFirstIp($GLOBALS['net_prefix'].".".$subnet['host'], $subnet['netmask']);
			$subnet['hosts_total'] = SubnetCalculator::getHostsTotal($subnet['netmask']);

		}
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
		return $subnet;
	}

	public function getSubnetsIds() {
		try {
			$sql = "select id FROM subnets ORDER by host DESC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$subnets[] = $row['id'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getPosibleIpsBySubnetId($subnet_id) {
		try {
			$sql = "SELECT host, netmask
			       FROM subnets
			       WHERE id=$subnet_id";
			$result = DB::getInstance()->query($sql); 
			$subnet = $result->fetch(PDO::FETCH_ASSOC);
			
			$subnet['last_ip'] = SubnetCalculator::getDqLastIp($GLOBALS['net_prefix'].".".$subnet['host'], $subnet['netmask']);
			$subnet['first_ip'] = SubnetCalculator::getDqFirstIp($GLOBALS['net_prefix'].".".$subnet['host'], $subnet['netmask']);
			$subnet['hosts_total'] = SubnetCalculator::getHostsTotal($subnet['netmask']);

			$exploded_last_ip = explode(".", $subnet['last_ip']);
			$exploded_first_ip = explode(".", $subnet['first_ip']);
			
			$iplist = array();			
			for($i=$exploded_first_ip[2]; $i<=$exploded_last_ip[2]; $i++) {
				for($ii=$exploded_first_ip[3]; $ii<=$exploded_last_ip[3]; $ii++) {
					$iplist[$GLOBALS['net_prefix'].".".$i.".".$ii]['type'] = 'free';
					$iplist[$GLOBALS['net_prefix'].".".$i.".".$ii]['ip'] = $GLOBALS['net_prefix'].".".$i.".".$ii;
				}
			}
			return $iplist;
		}
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}

	}
	
	public function getIPStatus($subnet_id) {
		$iplist = subnet::getPosibleIpsBySubnetId($subnet_id);
		
		//Get dhcp ips of an ip
		foreach (Helper::getExistingRangesBySubnetId($subnet_id) as $range) {
			$key_ip = $GLOBALS['net_prefix'].".".$range['range_ip'];
			$iplist[$key_ip] = array('range_ip'=>$key_ip,
											 'ip_id'=>$range['ip_id'],
											 'type'=>"range");
		}

		//Get dhcp subnet ips of an ip
		foreach (Helper::getExistingIpSubnetsBySubnetId($subnet_id) as $subnet_ip) {
			$key_ip = $GLOBALS['net_prefix'].".".$subnet_ip['subnet_ip'];
			$iplist[$key_ip] = array('subnet_ip'=>$key_ip,
											 'ip_id'=>$subnet_ip['ip_id'],
											 'type'=>"subnet_ip");
		}

		//Get ips
		foreach (Helper::getExistingIpsBySubnetId($subnet_id) as $key=>$ip) {
			$key_ip = $GLOBALS['net_prefix'].".".$ip['ip'];
			$iplist[$key_ip] = array('ip'=>$key_ip,
											 'ip_id'=>$ip['id'],
											 'type'=>"ip");
		}

		return $iplist;
	}
}

?>