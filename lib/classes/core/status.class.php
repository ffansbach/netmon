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
 * This file contains the class for the status site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class status {
  public function getNewestUser() {
		try {
			$sql = "SELECT id, nickname, create_date
					FROM users
					ORDER BY id DESC LIMIT 1;";
			$result = DB::getInstance()->query($sql); 
			$newest_user = $result->fetch(PDO::FETCH_ASSOC); 
		} 
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
		
		return $newest_user;    
	}

  public function getNewestIp() {
		try {
			$sql = "SELECT ips.id, ips.user_id, ips.ip, DATE_FORMAT(ips.create_date, '%D %M %Y') as create_date,
						   users.nickname,
						   subnets.title, subnets.subnet_ip
					FROM ips
					LEFT JOIN users ON (users.id=ips.user_id)
					LEFT JOIN subnets ON (subnets.id=ips.subnet_id)
					ORDER BY ips.id DESC LIMIT 1;";
			$result = DB::getInstance()->query($sql); 
			$newest_ip = $result->fetch(PDO::FETCH_ASSOC); 
		} 
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
		
		return $newest_ip;    
	}

  public function getNewestService() {
		try {
			$sql = "SELECT services.ip_id, services.title,
					  ips.ip,
					  subnets.subnet_ip
				  FROM services
				  LEFT JOIN ips ON (ips.id = services.ip_id)
				   LEFT JOIN subnets ON (subnets.id=ips.subnet_id)
				  WHERE services.typ LIKE 'service'
			       ORDER BY services.id DESC LIMIT 1;";
			$result = DB::getInstance()->query($sql); 
			$newest_service = $result->fetch(PDO::FETCH_ASSOC); 
		} 
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
		
		return $newest_service;    
	}
}

?>