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
	public function getIpIdByServiceId($service_id) {
		try {
			$sql = "select ips.id FROM ips, services WHERE services.id='$service_id' AND ips.id=services.ip_id;";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$ip = $row;
			}
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		return $ip;
	}

	public function getExistingIPv4IpsByProjectId($project_id) {
		$ips = array();
		try {
			$sql = "SELECT * FROM interfaces
				WHERE project_id='$project_id' AND ipv4_addr!=''
				ORDER BY ipv4_addr ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$interfaces[] = $row['ipv4_addr'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $interfaces;
	}

	public function getExistingIPv4Ips() {
		$ips = array();
		try {
			$sql = "SELECT * FROM interfaces
				WHERE ipv4_addr!=''
				ORDER BY ipv4_addr ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$ips[] = $row['ipv4_addr'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $ips;
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

	function recursive_remove_directory($directory, $empty=FALSE) {
		// if the path has a slash at the end we remove it here
		if(substr($directory,-1) == '/')
		{
			$directory = substr($directory,0,-1);
		}

		// if the path is not valid or is not a directory ...
		if(!file_exists($directory) || !is_dir($directory))
		{
			// ... we return false and exit the function
			return FALSE;

		// ... if the path is not readable
		}elseif(!is_readable($directory))
		{
			// ... we return false and exit the function
			return FALSE;

		// ... else if the path is readable
		}else{

			// we open the directory
			$handle = opendir($directory);

			// and scan through the items inside
			while (FALSE !== ($item = readdir($handle)))
			{
				// if the filepointer is not the current directory
				// or the parent directory
				if($item != '.' && $item != '..')
				{
					// we build the new path to delete
					$path = $directory.'/'.$item;

					// if the new path is a directory
					if(is_dir($path)) 
					{
						// we call this function with the new path
						Helper::recursive_remove_directory($path);

					// if the new path is a file
					}else{
						// we remove the file
						unlink($path);
					}
				}
			}
			// close the directory
			closedir($handle);

			// if the option to empty is not set to true
			if($empty == FALSE)
			{
				// try to delete the now empty directory
				if(!rmdir($directory))
				{
					// return false if not possible
					return FALSE;
				}
			}
			// return success
			return TRUE;
		}
	}
}

?>