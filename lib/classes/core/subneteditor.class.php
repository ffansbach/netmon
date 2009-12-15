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

require_once("./lib/classes/core/ipeditor.class.php");
require_once("./lib/classes/core/subnet.class.php");

/**
 * This file contains the class for editing a subnet.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class SubnetEditor {
	public function createNewSubnet($data) {
		$result = DB::getInstance()->exec("INSERT INTO subnets (host, netmask, real_host, real_netmask, allows_dhcp, user_id, title, description, polygons, vpn_server, vpn_server_port, vpn_server_device, vpn_server_proto, vpn_server_ca, vpn_server_cert, vpn_server_key, vpn_server_pass, create_date)
										   VALUES ('$data[host]', '$data[netmask]', '$data[real_host]', '$data[real_netmask]', '$data[allows_dhcp]', '$_SESSION[user_id]', '$data[title]', '$data[description]', '$data[polygons]', '$data[vpn_server]','$data[vpn_server_port]', '$data[vpn_server_device]', '$data[vpn_server_proto]', '$data[vpn_server_ca]', '$data[vpn_server_cert]', '$data[vpn_server_key]', '$data[vpn_server_pass]', NOW());");
		$subnet_id = DB::getInstance()->lastInsertId();
		if ($result>0) {
			$message[] = array("Das Subnetz ".$GLOBALS['net_prefix'].".".$data['host']."/".$data['netmask']." wurde in die Datenbank eingetragen.", 1);
			Message::setMessage($message);
			return array("result"=>true, "subnet_id"=>$subnet_id);
		} else {
			$message[] = array("Das Subnetz ".$GLOBALS['net_prefix'].".".$data['host']."/".$data['netmask']." konnte nicht in die Datenbank eingetragen werden.", 2);
			Message::setMessage($message);
			return false;
		}
	}

	public function updateSubnet($data) {
		$result = DB::getInstance()->exec("UPDATE subnets
										   SET host = '$data[host]',
											   real_netmask = '$data[real_netmask]',
											   real_host = '$data[real_host]',
											   netmask = '$data[netmask]',
											   allows_dhcp = '$data[allows_dhcp]',
											   title = '$data[title]',
											   description = '$data[description]',
											   polygons = '$data[polygons]',
											   vpn_server ='$data[vpn_server]',
											   vpn_server_port = '$data[vpn_server_port]',
											   vpn_server_device = '$data[vpn_server_device]',
											   vpn_server_proto = '$data[vpn_server_proto]',
											   vpn_server_ca = '$data[vpn_server_ca]',
											   vpn_server_cert = '$data[vpn_server_cert]',
											   vpn_server_key = '$data[vpn_server_key]',
											   vpn_server_pass = '$data[vpn_server_pass]'
										   WHERE id = '$_GET[id]'");
		if ($result>0) {
			$message[] = array("Das Subnetz ".$subnet." wurde geändert.", 1);
			Message::setMessage($message);
			return true;
		} else {
			$message[] = array("Die Änderungen im Subnetz ".$subnet." konnten nicht in die Datenbank eingetragen werden.", 2);
			Message::setMessage($message);
			return false;
		}
	}

	public function getSubnetsWithDefinedVpnserver() {
		try {
			$sql = "select id, host, netmask FROM subnets WHERE vpn_server IS NOT NULL ORDER BY host ASC";
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

	public function checktIfSubnetExists($subnet) {
		//Check if the subnet already exist
		try {
			$sql = "select * from subnets WHERE subnet_ip='$subnet'";
			$result = DB::getInstance()->query($sql);
			if ($result->rowCount()<1)
				return false;
			else
				return true;
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function checkSubnetData() {
		if($_POST['subnet_kind'] == "simple") {
//			$_POST['ip_count']

			foreach(EditingHelper::getExistingSubnets() as $subnet) {
				subnet::getPosibleIpsBySubnetId();
			}

			die('Not implemented yet!');
		} elseif ($_POST['subnet_kind'] == "extend") {
			if (empty($_POST['host']) OR empty($_POST['netmask']))
				$message[] = array("Bitte geben Sie Subnethost und Netzmaske an",2);

			//Check if the chosen subnet IP is still free
			//-->!!!checktIfSubnetExists
			/*$existing_subnets = EditingHelper::getExistingSubnets();
			foreach($existing_subnets as $subnet) {
				$first_ip = 
				$last_ip = 
			}
		
			if (!in_array($_POST['subnet_ip'], EditingHelper::getExistingSubnets())) {
				$message[] = array("Das gewählte Subnetz ist nicht mehr frei, bitte wählen Sie ein anderes.",2);
			} else {
				$subnet_ip = $_POST['subnet_ip'];
			}*/
		}

		//Workaround!
		$host = $_POST['host'];		
		$netmask = $_POST['netmask'];
		//-------

		if ($_POST['use_real_network']=='true') {
			$real_host = $_POST['real_host'];
			$real_netmask = $_POST['real_netmask'];
		} else {
			$real_host = 'NULL';
			$real_netmask = 'NULL';
		}

		if (empty($_POST['allows_dhcp']))
			$allows_dhcp = 0;
		else
			$allows_dhcp = 1;
		
		//Check if the data for the vpn server is complete
		if ($_POST['vpn_kind']=='no') {
			$vpn_server = "";
			$vpn_server_port = "";
			$vpn_server_device = "";
			$vpn_server_proto = "";
			$vpn_server_ca = "";
			$vpn_server_key = "";
			$vpn_server_cert = "";
			$vpn_server_pass = "";
		} elseif ($_POST['vpn_kind']=='other') {
			$vpn_data = Helper::getSubnetDataBySubnetID($_POST['vpnserver_from_project']);
			$vpn_server = $vpn_data['vpn_server'];
			$vpn_server_port = $vpn_data['vpn_server_port'];
			$vpn_server_device = $vpn_data['vpn_server_device'];
			$vpn_server_proto = $vpn_data['vpn_server_proto'];
			$vpn_server_ca = $vpn_data['vpn_server_ca'];
			$vpn_server_key = $vpn_data['vpn_server_key'];
			$vpn_server_cert = $vpn_data['vpn_server_cert'];
			$vpn_server_pass = $vpn_data['vpn_server_pass'];
		} elseif ($_POST['vpn_kind']=='own') {
			if (empty($_POST['vpn_server']) OR empty($_POST['vpn_server_port']) OR empty($_POST['vpn_server_proto']) OR empty($_POST['vpn_server_device']) OR empty($_POST['vpn_server_ca']) OR empty($_POST['vpn_server_cert']) OR empty($_POST['vpn_server_key'])) {
				$message[] = array("Die Daten zum VPN-Server sind unvollständig!",2);
			} else {
				$vpn_server = $_POST['vpn_server'];
				$vpn_server_port = $_POST['vpn_server_port'];
				$vpn_server_device = $_POST['vpn_server_device'];
				$vpn_server_proto = $_POST['vpn_server_proto'];
				$vpn_server_ca = $_POST['vpn_server_ca'];
				$vpn_server_key = $_POST['vpn_server_key'];
				$vpn_server_cert = $_POST['vpn_server_cert'];
				$vpn_server_pass = $_POST['vpn_server_pass'];
			}
		}
		
		//preset data for optional, not set values
		if (empty($_POST['title']))
			$title = "Subnetz $GLOBALS[net_prefix].$host/$netmask";
		else
			$title = $_POST['title'];
		
		if (empty($_POST['description']))
			$description = "Ein Unterprojekt des Freifunknetzes";
		else
			$description = $_POST['description'];
		
		if (empty($_POST['polygons']))
			$message[] = array("Es wurde kein geografischer Subnetbereich ausgewählt",2);
		else
			$polygons = $_POST['polygons'];

		if (isset($message) AND count($message)>0) {
			Message::setMessage($message);
			return false;
		} else {
			return array('host'=>$host, 'netmask'=>$netmask, 'real_host'=>$real_host, 'real_netmask'=>$real_netmask, 'allows_dhcp'=>$allows_dhcp, 'title'=>$title, 'description'=>$description, 'polygons'=>$polygons, 'vpn_server'=>$vpn_server, 'vpn_server_port'=> $vpn_server_port, 'vpn_server_device'=> $vpn_server_device, 'vpn_server_proto'=> $vpn_server_proto, 'vpn_server_ca'=> $vpn_server_ca, 'vpn_server_cert'=> $vpn_server_cert, 'vpn_server_key'=> $vpn_server_key, 'vpn_server_pass'=>$vpn_server_pass);
		}
	}
	
	public function deleteSubnet($subnet_id) {
		foreach (Helper::getIpsBySubnetId($subnet_id) as $ip) {
			IpEditor::deleteIp($ip['id']);
		}
		
		$subnet_data = Helper::getSubnetDataBySubnetID($subnet_id);
		DB::getInstance()->exec("DELETE FROM subnets WHERE id='$subnet_id';");
		$message[] = array("Das Subnetz $GLOBALS[net_prefix].$subnet_data[host]/$subnet_data[netmask] wurde gelöscht.",1);
		Message::setMessage($message);
		return true;
	}
}
?>