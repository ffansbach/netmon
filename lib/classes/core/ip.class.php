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
require_once(ROOT_DIR.'/lib/classes/core/interfaces.class.php');

class Ip_old {
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
			$stmt = DB::getInstance()->prepare("SELECT ips.id as ip_id, ips.ip, ips.ipv FROM ips WHERE ips.interface_id=?");
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
}

?>