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
 * This file contains a class with many helpfull methods used for editing.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

  require_once('./lib/classes/core/subnet.class.php');

class EditingHelper {
	public function getAFreeIP($subnet_id, $zone_start=false, $zone_end=false) {
		//Get all IP´s which already exist in subnet (ips and dhcp-zones!)
		$existingips = EditingHelper::getExistingIpsAndRanges($subnet_id);

//Delete only of edit subnet is working
/*		//Für den Ip bestimmte Range den Existierenden IP's hinzufügen
		if ($zone_start AND $zone_end) {
			for ($i=$zone_start; $i<=$zone_end; $i++) {
				array_push($existingips, $i);
			}
		}*/

		//Get first free IP in subnet
		$subnet_data = Subnet::getSubnet($subnet_id);
		$first_ip = explode(".", $subnet_data['first_ip']);
		$last_ip = explode(".", $subnet_data['last_ip']);

		for($i=$first_ip[2]; $i<=$last_ip[2]; $i++) {
			for($ii=$first_ip[3]; $ii<=$last_ip[3]; $ii++) {
				if(!in_array("$i.$ii", $existingips, TRUE)) {
					$available_ip = "$i.$ii";
					break;
				}
			}
		}

		if (isset($available_ip)) {
			return $available_ip;
		} else {
			return false;
		}
	}

	public function getExistingSubnets() {
		try {
			$sql = "select * FROM subnets ORDER BY host ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$subnets[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $subnets;
	}
	
	public function getExistingIps($subnet_id) {
		$ips = array();
		try {
			$sql = "SELECT ip FROM ips WHERE subnet_id='$subnet_id' ORDER BY ip ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$ips[$row['ip']] = $row['ip'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ips;
	}

	public function getExistingRanges($subnet_id) {
		$services = Helper::getServiceseBySubnetId($subnet_id);
		$zones = array();
		foreach ($services as $service) {
			for ($i=$service['zone_start']; $i<=$service['zone_end']; $i++) {
				$zones[$i] = $i;
			}
		}
		return $zones;
	}

	public function getExistingIpsAndRanges($subnet_id) {
		$ips = array();
		foreach (Helper::getExistingIpsBySubnetId($subnet_id) as $key=>$ip) {
			$ips[] = $ip['ip'];
		}
		
		foreach (Helper::getExistingRangesBySubnetId($subnet_id) as $range) {
			$ips[] = $range['range_ip'];
		}

		//Sort IP´s ascending
		$first = array();
		$second = array();
		foreach($ips as $key=>$ip) {
			$exploded = explode(".", $ip);
			$first[$key] = $exploded[0];
			$second[$key] = $exploded[1];
		}
		array_multisort($first, SORT_ASC, $second, SORT_ASC, $ips);

		return $ips;
	}

	public function getFreeIpsInSubnet($subnet_id) {
		//Get all IP´s which already exist in subnet (ips and dhcp-zones!)
		$existingips = EditingHelper::getExistingIpsAndRanges($subnet_id);

		//Delete existing IP´s from array
		$subnet_data = Subnet::getSubnet($subnet_id);
		$first_ip = explode(".", $subnet_data['first_ip']);
		$last_ip = explode(".", $subnet_data['last_ip']);

		for($i=$first_ip[2]; $i<=$last_ip[2]; $i++) {
			for($ii=$first_ip[3]; $ii<=$last_ip[3]; $ii++) {
				$exist = in_array("$i.$ii", $existingips, TRUE);
				if(!$exist) {
					$free_ips[] = "$i.$ii";
				}
			}
		}

	return $free_ips;
	}

	
	public function getFreeIpZone($subnet_id, $range, $ip) {
		if ($range > 0) {
			$free_ips = EditingHelper::getFreeIpsInSubnet($subnet_id);
			$ip_key = array_search($ip, $free_ips, TRUE);
			unset($free_ips[$ip_key]);
			
			$subnet_data = Subnet::getSubnet($subnet_id);
			$first_ip = explode(".", $subnet_data['first_ip']);
			$last_ip = explode(".", $subnet_data['last_ip']);
			
			$first_dhcp_ip = 0;
			$last_dhcp_ip = 0;
			$count = 0;
			for($i=$first_ip[2]; $i<=$last_ip[2]; $i++) {
				for($ii=$first_ip[3]; $ii<=$last_ip[3]; $ii++) {
					$exist = in_array("$i.$ii", $free_ips, TRUE);
					if(!$exist) {
						$count = 0;
						$first_dhcp_ip = 0;
						$last_dhcp_ip = 0;
					} else {
						if($first_dhcp_ip==0) {
							$first_dhcp_ip = "$i.$ii";
						}
						if($count==$range-1) {
							$last_dhcp_ip = "$i.$ii";
							break 2;
						}
						$count++;
					}
				}
			}

			if($first_dhcp_ip!=0 AND $last_dhcp_ip!=0) {
				return array('start'=>$first_dhcp_ip, 'end'=>$last_dhcp_ip);
			} else {
				return false;
			}
		} else {
			//NULL Gibt Probleme beim ändern wenn Range vorher auch NULL ist! (Clemens)
			return array('start'=>"NULL", 'end'=>"NULL");
		}
	}
	
	public function addIpTyp($ip_id, $title, $description, $typ, $crawler, $port, $visible, $notify, $notification_wait) {
		if ($typ="false" OR $crawler="false") {
			$message[] = array("Bitte wählen sie Servicetyp und Crawler aus.", 2);
			message::setMessage($message);
			return array("result"=>false, "service_id"=>$service_id);
		}

		if (!empty($port)) {
			$crawler = $port;
		}
		
		DB::getInstance()->exec("INSERT INTO services (ip_id, title, description, typ, crawler, visible, notify, notification_wait, create_date) VALUES ('$ip_id', '$title', '$description', '$typ', '$crawler', '$visible', '$notify', '$notification_wait', NOW());");
		$service_id = DB::getInstance()->lastInsertId();
		
		try {
			$sql = "select ips.ip FROM ips WHERE ips.id='$ip_id'";
			$result = DB::getInstance()->query($sql);
			$ip_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		$message[] = array("Ein Service vom Typ ".$typ." wurde der Ip $GLOBALS[net_prefix].$ip_data[ip] hinzugefügt.",1);
		message::setMessage($message);
		
		return array("result"=>true, "service_id"=>$service_id);
	}
}

?>