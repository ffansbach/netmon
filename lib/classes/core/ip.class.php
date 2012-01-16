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
 * This file contains the class for the ip site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

require_once($GLOBALS['monitor_root'].'lib/classes/core/subnetcalculator.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/project.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/ip.class.php');
require_once($GLOBALS['monitor_root'].'lib/classes/core/interfaces.class.php');

class Ip {
/*deprecated  public function getServiceList($ip_id) {
	$services = Helper::getServicesByIpId($ip_id);
	if (is_array($services))
	  foreach ($services as $service) {
	    $crawl_data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
	    $servicelist[] = array_merge($service, $crawl_data);
	  }
    return $servicelist;
  }

  public function insertStatus($current_crawl_data, $ip_id) {
	try {
		$sql = "UPDATE ips SET ";
		if (!empty($current_crawl_data['location'])) {
			$sql .= "location = '$current_crawl_data[location]',";
		}
		if (!empty($current_crawl_data['longitude'])) {
			$sql .= "longitude = '$current_crawl_data[longitude]',";
		}
		if (!empty($current_crawl_data['latitude'])) {
			$sql .= "latitude = '$current_crawl_data[latitude]',";
		}
		$sql = substr($sql, 0, -1);
		$sql .= " WHERE id = '$ip_id';";
		DB::getInstance()->exec($sql);

	}
	catch(PDOException $e) {
		$exception = $e->getMessage();
	}
	return true; 

  }*/
//kommentar endete hier (bjo)

	public function addIPv4Address($router_id, $project_id, $interface_id, $ipv4_addr) {
		//Add new IPv4-Address
		try {
			DB::getInstance()->exec("INSERT INTO ips (router_id, project_id, ip, ipv, create_date)
						 VALUES ('$router_id', '$project_id', '$ipv4_addr', 4, NOW());");
			$ip_id = DB::getInstance()->lastInsertId();
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		try {
			DB::getInstance()->exec("INSERT INTO interface_ips (interface_id, ip_id)
						 VALUES ('$interface_id', '$ip_id');");
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		$message[] = array("Dem Interface $interface_id wurde die IPv4 Adresse $ipv4_addr hinzugefügt.", 1);
		Message::setMessage($message);
	}

	public function addIPv6Address($router_id, $project_id, $interface_id, $ipv6_addr) {
		$interface = Interfaces::getInterfaceByInterfaceId($interface_id);
		$ip_exist = Ip::getIpByIp($ipv6_addr);
		if(empty($ip_exist)) {
			try {
				DB::getInstance()->exec("INSERT INTO ips (router_id, project_id, ip, ipv, create_date)
							 VALUES ('$router_id', '$project_id', '$ipv6_addr', 6, NOW());");
				$ip_id = DB::getInstance()->lastInsertId();
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			try {
				DB::getInstance()->exec("INSERT INTO interface_ips (interface_id, ip_id)
							 VALUES ('$interface_id', '$ip_id');");
			}
			catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			$message[] = array("Dem Interface $interface[name] ($interface_id) wurde die IPv6 Adresse $ipv6_addr hinzugefügt.", 1);
			Message::setMessage($message);
			return true;
		} else {
			$router_data = Router::getRouterByInterfaceId($interface_id);
			$message[] = array("Die IPv6 Adresse $ipv6_addr konnte nicht hinzugefügt werden, da sie bereits einem Interface auf dem Router $router_data[hostname] zugewiesen ist.", 2);
			Message::setMessage($message);
			return false;
		}
	}

	public function deleteIPAddress($ip_id) {
		$ip = Ip::getIpById($ip_id);
		DB::getInstance()->exec("DELETE FROM ips WHERE id='$ip_id';");
		DB::getInstance()->exec("DELETE FROM interface_ips WHERE ip_id='$ip_id';");
		$message[] = array("Die IP $ip[ip] wurde gelöscht.",1);
		Message::setMessage($message);
	}

	public function getIpById($ip_id) {
		$ips = array();
		try {
			$sql = "SELECT * FROM ips
				WHERE id='$ip_id'";
			$result = DB::getInstance()->query($sql);
			$ip = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ip;
	}
	
	public function getIpByIp($ip) {
		$ips = array();
		try {
			$sql = "SELECT * FROM ips
				WHERE ip='$ip'";
			$result = DB::getInstance()->query($sql);
			$ip = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ip;
	}

	public function getIpAddressesByRouterId($router_id) {
		$ips = array();
		try {
			$sql = "SELECT ips.id as ip_id, ips.ip, ips.ipv FROM ips WHERE ips.router_id=$router_id";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$ips[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ips;
	}
	
	public function getIpAdressesByInterfaceId($interface_id) {
		$ips = array();
		try {
			$sql = "SELECT ips.id as ip_id, ips.ip, ips.ipv FROM ips, interface_ips WHERE interface_ips.interface_id='$interface_id' AND interface_ips.ip_id=ips.id";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$ips[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ips;
	}

	public function getExistingIPv4Ips() {
		$ips = array();
		try {
			$sql = "SELECT * FROM ips
				WHERE ipv='4'
				ORDER BY ip ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$ips[] = $row['ip'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ips;
	}

	public function getExistingIPv4IpsByProjectId($project_id) {
		$ips = array();
		try {
			$sql = "SELECT * FROM ips
				WHERE ipv='4' AND project_id='$project_id'
				ORDER BY ip ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$ips[] = $row['ip'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ips;
	}

	public function getExistingIpRangesByProjectId($project_id) {
		$ips = array();
		try {
			$sql = "SELECT * FROM ip_ranges WHERE project_id='$project_id';";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$ip_ranges[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ip_ranges;
	}

	public function getIpsOfIpRangesByProjectId($project_id) {
		$ip_ranges = Ip::getExistingIpRangesByProjectId($project_id);

		$rangelist=array();
		if(!empty($ip_ranges)) {
			foreach($ip_ranges as $ip_range) {
				$first_ip = explode(".", $ip_range['ip_start']);
				$last_ip = explode(".", $ip_range['ip_end']);
				
				for($i=$first_ip[0]; $i<=$last_ip[0]; $i++) {
					for($ii=$first_ip[1]; $ii<=$last_ip[1]; $ii++) {
						for($iii=$first_ip[2]; $iii<=$last_ip[2]; $iii++) {
							for($iiii=$first_ip[3]; $iiii<=$last_ip[3]; $iiii++) {
								$rangelist[] = "$i.$ii.$iii.$iiii";
							}
						}
					}
				}
			}
		}
		return $rangelist;
	}

	public function getAFreeIPv4IPByProjectId($project_id) {
		//Get all existing IP´s
		$existingips = Ip::getExistingIPv4Ips();

		//Get all existing IP´s of IP-Ranges
		$ips_of_ip_ranges = Ip::getIpsOfIpRangesByProjectId($project_id);

		$existingips = array_merge($existingips, $ips_of_ip_ranges);

		//Get project informations
		$project_data = Project::getProjectData($project_id);

		//Get first and last ip of project subnet
		$first_ip = SubnetCalculator::getDqFirstIp($project_data['ipv4_host'], $project_data['ipv4_netmask']);
		$last_ip = SubnetCalculator::getDqLastIp($project_data['ipv4_host'], $project_data['ipv4_netmask']);

		//Get first free IP in the project subnet
		$first_ip = explode(".", $first_ip);
		$last_ip = explode(".", $last_ip);

		for($i=$first_ip[0]; $i<=$last_ip[0]; $i++) {
			for($ii=$first_ip[1]; $ii<=$last_ip[1]; $ii++) {
				for($iii=$first_ip[2]; $iii<=$last_ip[2]; $iii++) {
					for($iiii=$first_ip[3]; $iiii<=$last_ip[3]; $iiii++) {
						if(!in_array("$i.$ii.$iii.$iiii", $existingips, TRUE)) {
							$available_ip = "$i.$ii.$iii.$iiii";
							break;
						}
					}
					if(!empty($available_ip)) {
						break;
					}
				}
				if(!empty($available_ip)) {
					break;
				}
			}
			if(!empty($available_ip)) {
				break;
			}
		}

		if (isset($available_ip)) {
			return $available_ip;
		} else {
			return false;
		}
	}

	public function getExistingIpsAndRangesByProjectId($project_id) {
		//Get all existing IP´s
		$existingips = Ip::getExistingIPv4IpsByProjectId($project_id);

		//Get all existing IP´s of IP-Ranges
		$ips_of_ip_ranges = Ip::getIpsOfIpRangesByProjectId($project_id);

		$existingips = array_merge($existingips, $ips_of_ip_ranges);
		return $existingips;
	}

	public function getFreeIpsInProject($project_id) {
		//Get all IP´s which already exist in project (ips and ranges!)
		$existingips = Ip::getExistingIpsAndRangesByProjectId($project_id);

		//Delete existing IP´s from array
		//Get project informations
		$project_data = Project::getProjectData($project_id);

		//Get first and last ip of project subnet
		$first_ip = SubnetCalculator::getDqFirstIp($project_data['ipv4_host'], $project_data['ipv4_netmask']);
		$last_ip = SubnetCalculator::getDqLastIp($project_data['ipv4_host'], $project_data['ipv4_netmask']);

		$first_ip = explode(".", $first_ip);
		$last_ip = explode(".", $last_ip);

		for($i=$first_ip[0]; $i<=$last_ip[0]; $i++) {
			for($ii=$first_ip[1]; $ii<=$last_ip[1]; $ii++) {
				for($iii=$first_ip[2]; $iii<=$last_ip[2]; $iii++) {
					for($iiii=$first_ip[3]; $iiii<=$last_ip[3]; $iiii++) {
						$exist = in_array("$i.$ii.$iii.$iiii", $existingips, TRUE);
						if(!$exist) {
							$free_ips[] = "$i.$ii.$iii.$iiii";
						}
					}
				}
			}
		}

		return $free_ips;
	}

	public function getFreeIpRangeByProjectId($project_id, $range, $ip="") {
		if ($range > 0) {
			$free_ips = Ip::getFreeIpsInProject($project_id);
			if(!empty($ip)) {
				$ip_key = array_search($ip, $free_ips, TRUE);
				unset($free_ips[$ip_key]);
			}

			$project_data = Project::getProjectData($project_id);
			
			//Get first and last ip of project subnet
			$first_ip = SubnetCalculator::getDqFirstIp($project_data['ipv4_host'], $project_data['ipv4_netmask']);
			$last_ip = SubnetCalculator::getDqLastIp($project_data['ipv4_host'], $project_data['ipv4_netmask']);
		
			$first_ip = explode(".", $first_ip);
			$last_ip = explode(".", $last_ip);
			
			$first_dhcp_ip = 0;
			$last_dhcp_ip = 0;
			$count = 0;
			
			for($i=$first_ip[0]; $i<=$last_ip[0]; $i++) {
				for($ii=$first_ip[1]; $ii<=$last_ip[1]; $ii++) {
					for($iii=$first_ip[2]; $iii<=$last_ip[2]; $iii++) {
						for($iiii=$first_ip[3]; $iiii<=$last_ip[3]; $iiii++) {
							$exist = in_array("$i.$ii.$iii.$iiii", $free_ips, TRUE);
							if(!$exist) {
								$count = 0;
								$first_dhcp_ip = 0;
								$last_dhcp_ip = 0;
							} else {
								if($first_dhcp_ip==0) {
									$first_dhcp_ip = "$i.$ii.$iii.$iiii";
								}
								if($count==$range-1) {
									$last_dhcp_ip = "$i.$ii.$iii.$iiii";
									break 2;
								}
								$count++;
							}
						}
					}
				}
			}
			
			if($first_dhcp_ip!=0 AND $last_dhcp_ip!=0) {
				return array('start'=>$first_dhcp_ip, 'end'=>$last_dhcp_ip);
			} else {
				return false;
			}
		} else {
			return array('start'=>"NULL", 'end'=>"NULL");
		}
	}
}

?>