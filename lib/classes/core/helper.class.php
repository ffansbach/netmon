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
	public function getIpInfo($id) {
		try {
			$sql = "SELECT ips.id as ip_id, ips.user_id, ips.ip, ips.zone_start, ips.zone_end, ips.dhcp_host, ips.dhcp_netmask, ips.subnet_id, ips.vpn_client_cert, ips.vpn_client_key, ips.location, ips.longitude, ips.latitude, ips.chipset, ips.create_date,
						   users.nickname,
						   subnets.title, subnets.host as subnet_host, subnets.netmask as subnet_netmask, subnets.vpn_server, subnets.vpn_server_port, subnets.vpn_server_device, subnets.vpn_server_proto, subnets.vpn_server_ca
					FROM ips
					LEFT JOIN users ON (users.id=ips.user_id)
					LEFT JOIN subnets ON (subnets.id=ips.subnet_id)
					WHERE ips.id=$id";
			$result = DB::getInstance()->query($sql);
			$ip = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		$ip['is_ip_owner'] = UserManagement::isThisUserOwner($ip['user_id']);
		return $ip;
	}

	public function getIpsByUserIDThatCanVPN($user_id) {
		try {
			$sql = "SELECT ips.id as ip_id, ips.ip, subnets.title as subnet_title
					FROM ips
					LEFT JOIN subnets ON (subnets.id=ips.subnet_id)
					WHERE ips.user_id='$user_id' AND ips.vpn_client_cert!='' AND ips.vpn_client_key!='' AND subnets.vpn_server_ca!=''";
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
  
  public function getIpDataByIpId($id) {
    try {
      $sql = "SELECT ips.id as ip_id, ips.user_id, ips.ip, ips.zone_start, ips.zone_end, ips.subnet_id, ips.radius, ips.vpn_client_cert, ips.vpn_client_key, ips.location, ips.longitude, ips.latitude, ips.chipset, DATE_FORMAT(ips.create_date, '%D %M %Y') as create_date,
				      users.nickname, users.email,
				      subnets.title, subnets.host, subnets.netmask, subnets.vpn_server, subnets.vpn_server_port, subnets.vpn_server_device, subnets.vpn_server_proto, subnets.vpn_server_ca
				   FROM ips
				   LEFT JOIN users ON (users.id=ips.user_id)
				   LEFT JOIN subnets ON (subnets.id=ips.subnet_id)
				   WHERE ips.id=$id";
      $result = DB::getInstance()->query($sql);
	  $ip = $result->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e) {
      echo $e->getMessage();
    }

    $ip['is_ip_owner'] = UserManagement::isThisUserOwner($ip['user_id']);
    return $ip;
  }

	public function getSubnetById($subnet_id) {
		try {
			$sql = "select * FROM subnets WHERE id='$subnet_id'";
			$result = DB::getInstance()->query($sql);
			$subnet = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		$subnet['last_ip'] = SubnetCalculator::getDqLastIp($GLOBALS['net_prefix'].".".$subnet['host'], $subnet['netmask']);
		$subnet['first_ip'] = SubnetCalculator::getDqFirstIp($GLOBALS['net_prefix'].".".$subnet['host'], $subnet['netmask']);
		$subnet['hosts_total'] = SubnetCalculator::getHostsTotal($subnet['netmask']);
		$subnet['broadcast'] = SubnetCalculator::getDqBcast($GLOBALS['net_prefix'].".".$subnet['host'], $subnet['netmask']);

		return $subnet;
	}

	public function getIpOfIpById($ip_id) {
		try {
			$sql = "select ip FROM ips WHERE id='$ip_id';";
			$result = DB::getInstance()->query($sql);
			$ip = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		return $ip['ip'];
	}

  public function getIpIdByServiceId($service_id) {
	try {
		$sql = "select ips.id FROM ips, services WHERE services.id='$service_id' AND ips.id=services.ip_id;";
		$result = DB::getInstance()->query($sql);
		foreach($result as $row) {
			$ip = $row;
		}
	}
	
	catch(PDOException $e) {
		echo $e->getMessage();
	}

    return $ip;
  }


  public function getIpsByUserId($user_id) {
    $ips = array();
	try {
		$sql = "select ips.id, ips.ip, ips.user_id, subnets.host as subnet_host, subnets.netmask as subnet_netmask FROM ips LEFT JOIN subnets on (subnets.id = ips.subnet_id) WHERE ips.user_id='$user_id' ORDER BY subnets.host, ips.ip;";
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

  public function getServicesByType($type) {
    //Get only services that the user is allowed to see
    if (!UserManagement::checkPermission(4))
      $visible = "AND visible = 1";
    else
      $visible = "";

    $services = array();
	try {
		$sql = "SELECT services.id as service_id, services.ip_id, services.title as services_title, services.description, services.typ, services.crawler, services.visible, notify, notification_wait, services.notified, last_notification, services.use_netmons_url, services.url, services.create_date,
			       ips.id as ip_id, ips.ip, ips.zone_start, ips.zone_end,			       
			       subnets.id as subnet_id, subnets.title, subnets.host as subnet_host, subnets.netmask as subnet_netmask, subnets.vpn_server_ca, subnets.vpn_server_cert, subnets.vpn_server_key, subnets.vpn_server_pass,
			       users.id as user_id, users.nickname, users.email
			FROM services
			LEFT JOIN ips ON (ips.id = services.ip_id)
			LEFT JOIN subnets ON (subnets.id = ips.subnet_id)
			LEFT JOIN users ON (users.id = ips.user_id)
			WHERE services.typ='$type' $visible ORDER BY services.id";
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

  public function getAllServiceIDsByServiceType($type) {
    $services = array();
	try {
		$sql = "SELECT services.id as service_id, services.typ, services.crawler,
					   ips.ip,
					   subnets.host as subnet_host, subnets.netmask as subnet_netmask
			       FROM services
			       LEFT JOIN ips ON (ips.id = services.ip_id)
			       LEFT JOIN subnets ON (subnets.id = ips.subnet_id)
			       WHERE services.typ='$type'";
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

  public function getServicesByUserId($user_id) {
    //Get only services that the user is allowed to see
    if (!UserManagement::checkPermission(4))
      $visible = "AND visible = 1";
    else
      $visible = "";

    $services = array();
	try {
		$sql = "SELECT services.id as service_id, services.title as services_title, services.typ, services.crawler,
				      ips.user_id, ips.ip, ips.id as ip_id, ips.subnet_id,
				      subnets.host as subnet_host, subnets.netmask as subnet_netmask, subnets.title,
				      users.nickname
			       FROM services
			       LEFT JOIN ips ON (ips.id = services.ip_id)
			       LEFT JOIN subnets ON (subnets.id = ips.subnet_id)
			       LEFT JOIN users ON (users.id = ips.user_id)
			       WHERE ips.user_id='$user_id' $visible ORDER BY services.id";
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

  public function getServicesByTypeAndIpId($type, $ipId) {
    //Get only services that the user is allowed to see
    if (!UserManagement::checkPermission(4))
      $visible = "AND visible = 1";
    else
      $visible = "";

    $services = array();
	try {
		$sql = "SELECT services.id as service_id, services.title as services_title, services.typ, services.crawler,
				      ips.user_id, ips.ip, ips.id as ip_id, ips.subnet_id,
				      subnets.host as subnet_host, subnets.netmask as subnet_netmask, subnets.title,
				      users.nickname
			       FROM services
			       LEFT JOIN ips ON (ips.id = services.ip_id)
			       LEFT JOIN subnets ON (subnets.id = ips.subnet_id)
			       LEFT JOIN users ON (users.id = ips.user_id)
			       WHERE services.typ='$type' AND ips.id='$ipId' $visible ORDER BY services.id";
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

  public function getServicesByIpId($ip_id) {
	$services = array();
    //Get only services that the user is allowed to see
    if (!UserManagement::checkPermission(4))
      $visible = "AND visible = 1";
    else
      $visible = "";

    try {
      $sql = "SELECT services.id as service_id, services.title as services_title, services.description, services.typ, services.crawler, services.create_date,
				      ips.user_id, ips.ip, ips.id as ip_id, ips.subnet_id,
				      subnets.host as subnet_host, subnets.netmask as subnet_netmask, subnets.title,
				      users.nickname
			       FROM services
			       LEFT JOIN ips ON (ips.id = services.ip_id)
			       LEFT JOIN subnets ON (subnets.id = ips.subnet_id)
			       LEFT JOIN users ON (users.id = ips.user_id)
			       WHERE services.ip_id='$ip_id' $visible ORDER BY services.id";
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
	try {
		$sql = "select * FROM subnets WHERE user_id='$user_id';";
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

  public function getIpsBySubnetId($subnet_id) {
    $ips = array();
	try {
		$sql = "select * FROM ips WHERE subnet_id='$subnet_id';";
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

	public function getSubnetIpOfIpById($ip_id) {
		try {
			$sql = "SELECT subnets.host as subnet_host, subnets.netmask as subnet_netmask FROM ips
					LEFT JOIN subnets on (subnets.id=ips.subnet_id)
					WHERE ips.id='$ip_id';";
			$result = DB::getInstance()->query($sql);
			$subnet = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $subnet;
	}

	function getUserByID($id) {
		try {
			$sql = "SELECT * FROM users WHERE id=$id";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$user = $row;
			}
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $user;
	}

	function getPlublicUserInfoByID($id) {
		try {
			$sql = "SELECT nickname, vorname, nachname, strasse, plz, ort, telefon, email, jabber, icq, website, about FROM users WHERE id=$id";
			$result = DB::getInstance()->query($sql);
			$user = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $user;
	}

	function getIplistByUserID($id) {
		try {
			$sql = "SELECT ips.id, ips.user_id, ips.ip, ips.subnet_id,
						   users.nickname,
					LEFT(subnets.title, 25) as title, subnets.host as subnet_host, subnets.netmask as subnet_netmask
					FROM ips
					LEFT JOIN users ON (users.id=ips.user_id)
					LEFT JOIN subnets ON (subnets.id=ips.subnet_id)
					WHERE users.id=$id
					ORDER BY subnets.host, ips.ip";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$row['is_ip_owner'] = UserManagement::isThisUserOwner($row['user_id']);
				$iplist[] = $row;
			}
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
    	return $iplist;
	}

	function getSubnetlistByUserID($id) {
		try {
			$sql = "SELECT COUNT(ips.id) as ips_in_net, subnets.id, subnets.host, subnets.netmask, subnets.title
					FROM  subnets
					LEFT JOIN ips on (ips.subnet_id=subnets.id)
					WHERE subnets.user_id=$id GROUP BY subnets.id";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$subnetlist[] = $row;
			}
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $subnetlist;	
	}

	public function getServiceseBySubnetId($subnet_id) {
		$servicelist = array();
		try {
			$sql = "SELECT ips.id as ip_id, ips.ip, services.id as service_id
					FROM ips
					LEFT JOIN services ON (services.ip_id=ips.id)
					WHERE ips.subnet_id='$subnet_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$servicelist[] = $row;
			}
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $servicelist;
	}

	public function getServiceDataByServiceId($service_id) {
		try {
			$sql = "SELECT services.id as service_id, services.ip_id, services.title, services.description, services.typ, services.crawler, services.visible, notify, notification_wait, services.notified, last_notification, services.use_netmons_url, services.url, services.create_date,
			       ips.id as ip_id, ips.ip, ips.zone_start, ips.zone_end,
			       subnets.id as subnet_id, subnets.host as subnet_host, subnets.netmask as subnet_netmask, subnets.vpn_server_ca, subnets.vpn_server_cert, subnets.vpn_server_key, subnets.vpn_server_pass,
			       users.id as user_id, users.nickname, users.email
			       FROM  services
			       LEFT JOIN ips ON (ips.id=services.ip_id)
			       LEFT JOIN subnets ON (subnets.id=ips.subnet_id)
			       LEFT JOIN users ON (users.id=ips.user_id)
			       WHERE services.id=$service_id";
			$result = DB::getInstance()->query($sql);
			$service = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $service;
	}

	public function getExistingIpsBySubnetId($subnet_id) {
		$ips = array();
		try {
			$sql = "SELECT * FROM ips WHERE subnet_id='$subnet_id' ORDER BY ip ASC";
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

	public function getExistingRangesBySubnetId($subnet_id) {
		$rangelist = array();
		$subnet_data = Helper::getSubnetById($subnet_id);
		if ($subnet_data['dhcp_kind']=='ips') {
			$ips = Helper::getIpsBySubnetId($subnet_id);
			
			foreach ($ips as $ip) {
				if((!empty($ip['zone_start']) AND $ip['zone_start']!='NULL' AND $ip['zone_start']!=0) AND (!empty($ip['zone_end']) AND $ip['zone_end']!='NULL' AND $ip['zone_end']!=0)) {
					$exploded_zone_start = explode(".", $ip['zone_start']);
					$exploded_zone_end = explode(".", $ip['zone_end']);
					
					for ($i=$exploded_zone_start[0]; $i<=$exploded_zone_end[0]; $i++) {
						for ($ii=$exploded_zone_start[1]; $ii<=$exploded_zone_end[1]; $ii++) {
							$key_ip = $GLOBALS['net_prefix'].".".$i.".".$ii;
							$rangelist[$key_ip]['range_ip'] = $i.".".$ii;
							$rangelist[$key_ip]['ip_id'] = $ip['id'];
						}
					}
				}
			}
		}
		return $rangelist;
	}

	public function getExistingIpSubnetsBySubnetId($subnet_id) {
		$rangelist = array();
		$subnet_data = Helper::getSubnetById($subnet_id);
		if ($subnet_data['dhcp_kind']=='subnet') {
			$ips = Helper::getIpsBySubnetId($subnet_id);
			
			foreach ($ips as $ip) {
				if(!empty($ip['dhcp_host']) AND !empty($ip['dhcp_netmask'])) {
					$ip_subnet['first_ip'] = SubnetCalculator::getDqFirstIp($GLOBALS['net_prefix'].".".$ip['dhcp_host'], $ip['dhcp_netmask']);
					$ip_subnet['last_ip'] = SubnetCalculator::getDqLastIp($GLOBALS['net_prefix'].".".$ip['dhcp_host'], $ip['dhcp_netmask']);
					$exploded_zone_start = explode(".", $ip_subnet['first_ip']);
					$exploded_zone_end = explode(".", $ip_subnet['last_ip']);
					for ($i=$exploded_zone_start[2]; $i<=$exploded_zone_end[2]; $i++) {
						for ($ii=$exploded_zone_start[3]; $ii<=$exploded_zone_end[3]; $ii++) {
							$key_ip = $GLOBALS['net_prefix'].".".$i.".".$ii;
							$rangelist[$key_ip]['subnet_ip'] = $i.".".$ii;
							$rangelist[$key_ip]['ip_id'] = $ip['id'];
						}
					}
				}
			}
		}
		return $rangelist;
	}

	public function getLastOnlineCrawlDataByServiceId($service_id) {
		try {
			$sql = "SELECT * FROM crawl_data
					WHERE service_id='$service_id' AND status='online' ORDER BY id DESC LIMIT 1";
			$result = DB::getInstance()->query($sql);
			$last_online_crawl = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $last_online_crawl;
	}

	public function getCurrentCrawlDataByServiceId($service_id) {
		try {
			$sql = "SELECT id, crawl_time, status, nickname as luci_nickname, hostname, email, location, prefix, ssid, longitude, latitude, luciname, luciversion, distname, distversion, chipset, cpu, network, wireless_interfaces, uptime, idletime, memory_total, memory_caching, memory_buffering, memory_free, loadavg, processes 
				FROM crawl_data
			        WHERE service_id='$service_id'
					ORDER BY id DESC LIMIT 1";
			$result = DB::getInstance()->query($sql);
			$current_crawl = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		if(empty($current_crawl['status']))
			 $current_crawl['status'] = "unbekannt";

		return $current_crawl;
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
	
	public function linkIp2IpId($ip2check) {
		$exploded_ip = explode(".", $ip2check);
		
		if($exploded_ip[0].".".$exploded_ip[1]==$GLOBALS['net_prefix']) {
			try {
				$sql = "SELECT ips.id FROM ips WHERE ips.ip='$exploded_ip[2].$exploded_ip[3]'";
				$result = DB::getInstance()->query($sql);
				$ip_id = $result->fetch(PDO::FETCH_ASSOC);
			}
			catch(PDOException $e) {
			echo $e->getMessage();
			};
			return $ip_id;
		} else {
			return false;
		}
	}
	
	public function getUserByEmail($email) {
		try {
			$sql = "SELECT * FROM  users WHERE email='$email'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$user = $row;
			}
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		};
		return $user;
	}
	
	function randomPassword($size) { 
		$result = "";
		srand((double)microtime()*1000000); 
		
		for($i=0; $i < $size; $i++) { 
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

	public function getIpIdByIp($ip) {
		$ipParts = explode(".", $ip);
		try {
			$sql = "SELECT ips.id
				FROM ips
				WHERE ips.ip='$ipParts[2].$ipParts[3]'";
			$result = DB::getInstance()->query($sql);
			
			foreach($result as $row) {
				$id = $row['id'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		return $id;
	}

	public function getServicesByIp($ip) {
		$ipId = Helper::getIpIdByIp($ip);
		return Helper::getServicesByIpId($ipId);
	}

	public function makeSmoothIplistTime($timestamp) {
		if (empty($timestamp)) {
			$time = "unbekannt";
		} elseif (date("d.m.Y", $timestamp)==date("d.m.Y", time()-86400)) {
			$time = "Gestern ".date("H:i", $timestamp." uhr");
		} elseif(date("d.m.Y", $timestamp)==date("d.m.Y", time())) {
			$time = "Heute ".date("H:i", $timestamp)." uhr";
		} else {
			$time = date("d.m.Y", $timestamp)." ".date("H:i", $timestamp)." uhr";
		}

		return $time;
	}

	public function countServiceStatusByType($type) {
		$result['online']=0;
		$result['offline']=0;
		$result['unbekannt']=0;

		$services = Helper::getServicesByType($type);
		foreach ($services as $service) {
			$crawl_data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
			if ($crawl_data['status']=='online')
				$result['online']++;
			elseif ($crawl_data['status']=='offline')
				$result['offline']++;
			elseif ($crawl_data['status']=='unbekannt')
				$result['unbekannt']++;
		}
		return $result;
	}

	public function checkIfIpIsRegisteredAndGetServices($ip) {
		try {
			$sql = "SELECT ips.id as ip_id, ips.ip,
				       services.id as service_id, services.typ 
					FROM ips
					LEFT JOIN services ON (services.ip_id=ips.id)
					WHERE ips.ip='$ip'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$checked_ip[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $checked_ip;
	}
	public function getImages() {
		try {
			$sql = "SELECT imagemaker_images.id as image_id, imagemaker_images.title,
				       users.nickname 
					FROM imagemaker_images
					LEFT JOIN users ON (users.id=imagemaker_images.user_id)";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$images[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $images;
	}

	public function getImageByImageId($image_id) {
		try {
			$sql = "SELECT imagemaker_images.id as image_id, imagemaker_images.title, imagemaker_images.description,
					users.nickname 
					FROM imagemaker_images
					LEFT JOIN users ON (users.id=imagemaker_images.user_id)
					WHERE imagemaker_images.id=$image_id";
			$result = DB::getInstance()->query($sql);
			$image = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $image;
	}

	public function getImageConfigsByImageId($image_id) {
		try {
			$sql = "SELECT imagemaker_configs.id as config_id, imagemaker_configs.title, imagemaker_configs.description,
					users.nickname 
					FROM imagemaker_configs
					LEFT JOIN users ON (users.id=imagemaker_configs.user_id)
					WHERE imagemaker_configs.image_id=$image_id";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$configs[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $configs;
	}

	public function getImageConfigByConfigId($config_id) {
		try {
			$sql = "SELECT imagemaker_configs.id as config_id, imagemaker_configs.title, imagemaker_configs.description,
					users.nickname 
					FROM imagemaker_configs
					LEFT JOIN users ON (users.id=imagemaker_configs.user_id)
					WHERE imagemaker_configs.id=$config_id";
			$result = DB::getInstance()->query($sql);
			$image = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $image;
	}
}

?>