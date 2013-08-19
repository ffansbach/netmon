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

require_once(ROOT_DIR.'/lib/classes/core/Ip.class.php');
require_once(ROOT_DIR.'/lib/classes/core/Iplist.class.php');
require_once(ROOT_DIR.'/lib/classes/core/subnetcalculator.class.php');

class Interfaces {
	
	public function deleteInterface($interface_id) {
		$interface_data = Interfaces::getInterfaceByInterfaceId($interface_id);

		//Delete IP Adresses
		$iplist = new Iplist((int)$interface_id);
		$iplist->delete();

		//Delete Interface
		try {
			$stmt = DB::getInstance()->prepare("DELETE FROM interfaces WHERE id=?");
			$stmt->execute(array($interface_id));
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}

		$message[] = array("Das Interface $interface_data[name] wurde entfernt.",1);
		Message::setMessage($message);
		return true;
	}
	
	public function getInterfaceByInterfaceId($interface_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM interfaces WHERE id=?");
			$stmt->execute(array($interface_id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	public function getInterfacesByRouterId($router_id) {
		$interfaces = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT  interfaces.id as interface_id, interfaces.router_id, interfaces.project_id, interfaces.create_date, interfaces.name, interfaces.mac_addr,
								    projects.title, projects.is_wlan, projects.wlan_essid, projects.wlan_bssid, projects.wlan_channel, projects.is_vpn, projects.vpn_server, projects.vpn_server_port, projects.vpn_server_device, projects.vpn_server_proto, projects.vpn_server_ca_crt, projects.vpn_server_ca_key, projects.vpn_server_pass, projects.is_ccd_ftp_sync, projects.ccd_ftp_folder, projects.ccd_ftp_username, projects.ccd_ftp_password, projects.is_olsr, projects.is_batman_adv, projects.is_ipv4, projects.ipv4_host, projects.ipv4_netmask, projects.ipv4_dhcp_kind
							    FROM interfaces
							    LEFT JOIN projects on (projects.id=interfaces.project_id)
							    WHERE router_id=?");
			$stmt->execute(array($router_id));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		foreach($rows as $key=>$row) {
			$row['ip_addresses'] = new Iplist((int)$row['interface_id']);
			$interfaces[] = $row;
		}
		return $interfaces;
	}
	
	public function getInterfacesCrawlByCrawlCycle($crawl_cycle_id, $router_id) {
		$interfaces = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT  * FROM crawl_interfaces WHERE crawl_cycle_id=? AND router_id=?");
			$stmt->execute(array($crawl_cycle_id, $router_id));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		foreach($rows as $row) {
			switch($row['wlan_frequency']) {
				case "2.412": $row['wlan_channel'] = 1; break; 
				case "2.417": $row['wlan_channel'] = 2; break;
				case "2.422": $row['wlan_channel'] = 3; break;
				case "2.427": $row['wlan_channel'] = 4; break;
				case "2.432": $row['wlan_channel'] = 5; break;
				case "2.437": $row['wlan_channel'] = 6; break;
				case "2.442": $row['wlan_channel'] = 7; break;
				case "2.447": $row['wlan_channel'] = 8; break;
				case "2.452": $row['wlan_channel'] = 9; break;
				case "2.457": $row['wlan_channel'] = 10; break;
				case "2.462": $row['wlan_channel'] = 11; break;
				case "2.467": $row['wlan_channel'] = 12; break;
				case "2.472": $row['wlan_channel'] = 13; break;
				case "2.484": $row['wlan_channel'] = 14; break;
			}
			$interfaces[] = $row;
		}
		return $interfaces;
	}

	public function getInterfaceCrawlByCrawlCycleAndRouterIdAndInterfaceName($crawl_cycle_id, $router_id, $interface_name) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  * FROM crawl_interfaces WHERE crawl_cycle_id=? AND router_id=? AND name=?");
			$stmt->execute(array($crawl_cycle_id, $router_id, $interface_name));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
}

?>