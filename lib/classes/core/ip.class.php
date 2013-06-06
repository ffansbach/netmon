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

require_once(ROOT_DIR.'/lib/classes/core/subnetcalculator.class.php');
require_once(ROOT_DIR.'/lib/classes/core/project.class.php');
require_once(ROOT_DIR.'/lib/classes/core/ip.class.php');
require_once(ROOT_DIR.'/lib/classes/core/interfaces.class.php');

class Ip {
	public function addIPv4Address($router_id, $project_id, $interface_id, $ipv4_addr) {
		//Add new IPv4-Address
		try {
			$stmt = DB::getInstance()->prepare("INSERT INTO ips (router_id, project_id, ip, ipv, create_date)
												VALUES (?, ?, ?, 4, NOW())");
			$stmt->execute(array($router_id, $project_id, $ipv4_addr));
			$ip_id = DB::getInstance()->lastInsertId();
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		try {
			$stmt = DB::getInstance()->prepare("INSERT INTO interface_ips (interface_id, ip_id)
												VALUES (?, ?)");
			$stmt->execute(array($interface_id, $ip_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		$message[] = array("Dem Interface $interface_id wurde die IPv4 Adresse $ipv4_addr hinzugefügt.", 1);
		Message::setMessage($message);
	}

	public function addIPv6Address($router_id, $project_id, $interface_id, $ipv6_addr) {
		$interface = Interfaces::getInterfaceByInterfaceId($interface_id);
		$ip_exist = Ip::getIpByIp($ipv6_addr);
		if(empty($ip_exist)) {
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO ips (router_id, project_id, ip, ipv, create_date)
													VALUES (?, ?, ?, 6, NOW())");
				$stmt->execute(array($router_id, $project_id, $ipv6_addr));
				$ip_id = DB::getInstance()->lastInsertId();
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO interface_ips (interface_id, ip_id)
													VALUES (?, ?)");
				$stmt->execute(array($interface_id, $ip_id));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
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
		
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM ips WHERE id=?");
			$stmt->execute(array($ip_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM interface_ips WHERE ip_id=?");
			$stmt->execute(array($ip_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		$message[] = array("Die IP $ip[ip] wurde gelöscht.",1);
		Message::setMessage($message);
	}

	public function getIpById($ip_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM ips
												WHERE id=?");
			$stmt->execute(array($ip_id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function getIpByIp($ip) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM ips
												WHERE ip=?");
			$stmt->execute(array($ip));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	public function getIpAddressesByRouterId($router_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT ips.id as ip_id, ips.ip, ips.ipv FROM ips WHERE ips.router_id=?");
			$stmt->execute(array($router_id));
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function getIpAdressesByInterfaceId($interface_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT ips.id as ip_id, ips.ip, ips.ipv FROM ips, interface_ips WHERE interface_ips.interface_id=? AND interface_ips.ip_id=ips.id");
			$stmt->execute(array($interface_id));
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	public function getExistingIPv4Ips() {
		$plain_ips = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT ips.ip FROM ips WHERE ipv='4' ORDER BY ip ASC");
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function getExistingIPv4IpsPlain() {
		$plain_ips = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT ips.ip FROM ips WHERE ipv='4' ORDER BY ip ASC");
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		foreach($result as $ip) {
			$plain_ips[] = $ip['ip'];
		}
		return $plain_ips;
	}
	
	public function getExistingIPs($ipv="all") {
		if($ipv=="all")
			$sql_append = "";
		elseif ($ipv == 4 OR $ipv = 6)
			$sql_append = "WHERE ipv='$ipv'";

		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM ips $sql_append ORDER BY ip ASC");
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	public function getExistingIPv4IpsByProjectId($project_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM ips WHERE ipv='4' AND project_id=? ORDER BY ip ASC");
			$stmt->execute(array($project_id));
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function getExistingIPv4IpsByProjectIdPlain($project_id) {
		$result = array();
		$ips = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM ips WHERE ipv='4' AND project_id=? ORDER BY ip ASC");
			$stmt->execute(array($project_id));
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		foreach($result as $ip) {
			$ips[] = $ip['ip'];
		}
		return $ips;
	}

	public function getExistingIpRangesByProjectId($project_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM ip_ranges WHERE project_id=?");
			$stmt->execute(array($project_id));
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
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
		$existingips = Ip::getExistingIPv4IpsPlain();

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
		$existingips = Ip::getExistingIPv4IpsByProjectIdPlain($project_id);

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

		die("Too much load in this function!");
		
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
/*			if(!empty($ip)) {
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
			}*/
		} else {
			return array('start'=>"NULL", 'end'=>"NULL");
		}
	}
}

?>