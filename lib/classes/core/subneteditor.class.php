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
		$result = DB::getInstance()->exec("INSERT INTO subnets (subnet_type, host, netmask, real_host, real_netmask, dhcp_kind, user_id, title, description, dns_server, essid, bssid, channel, website, polygons, vpn_server, vpn_server_port, vpn_server_device, vpn_server_proto, vpn_server_ca, vpn_server_cert, vpn_server_key, vpn_server_pass, ftp_sync, ftp_ccd_folder, ftp_ccd_username, ftp_ccd_password, create_date)
										   VALUES ('$_POST[subnet_type]', '$data[host]', '$data[netmask]', '$data[real_host]', '$data[real_netmask]', '$data[dhcp_kind]', '$_SESSION[user_id]', '$data[title]', '$data[description]', '$_POST[dns_server]', '$_POST[essid]', '$_POST[bssid]', '$_POST[channel]', '$_POST[website]', '$data[polygons]', '$data[vpn_server]','$data[vpn_server_port]', '$data[vpn_server_device]', '$data[vpn_server_proto]', '$data[vpn_server_ca]', '$data[vpn_server_cert]', '$data[vpn_server_key]', '$data[vpn_server_pass]', '$data[ftp_sync]', '$data[ftp_ccd_folder]', '$data[ftp_ccd_username]', '$data[ftp_ccd_password]', NOW());");
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
		$result = DB::getInstance()->exec("UPDATE subnets SET
							subnet_type = '$_POST[subnet_type]',
							host = '$data[host]',
							netmask = '$data[netmask]',
							real_host = '$data[real_host]',
							real_netmask = '$data[real_netmask]',
							dhcp_kind = '$data[dhcp_kind]',
							title = '$data[title]',
							description = '$data[description]',
							dns_server = '$_POST[dns_server]',
							essid = '$_POST[essid]',
							bssid = '$_POST[bssid]',
							channel = '$_POST[channel]',
							website = '$_POST[website]',
							polygons = '$data[polygons]',
							vpn_server ='$data[vpn_server]',
							vpn_server_port = '$data[vpn_server_port]',
							vpn_server_device = '$data[vpn_server_device]',
							vpn_server_proto = '$data[vpn_server_proto]',
							vpn_server_ca = '$data[vpn_server_ca]',
							vpn_server_cert = '$data[vpn_server_cert]',
							vpn_server_key = '$data[vpn_server_key]',
							vpn_server_pass = '$data[vpn_server_pass]',
							ftp_ccd_folder = '$data[ftp_ccd_folder]',
							ftp_ccd_username = '$data[ftp_ccd_username]',
							ftp_ccd_password = '$data[ftp_ccd_password]'
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

	public function getSubnetIpRope() {
		foreach(EditingHelper::getExistingSubnets() as $subnet) {
			$subnet_host =  SubnetCalculator::getDqNet($GLOBALS['net_prefix'].".".$subnet['host'], $subnet['netmask']);
			$subnet_ips = SubnetEditor::getPosibleIpsBySubnetId($subnet['id']);
			$subnet_broadcast =  SubnetCalculator::getDqBcast($GLOBALS['net_prefix'].".".$subnet['host'], $subnet['netmask']);

			$iplist[$subnet_host] = $subnet_host;
			$iplist = $iplist+$subnet_ips;
			$iplist[$subnet_broadcast] = $subnet_broadcast;
		}
		return $iplist;
	}

	public function checkIfSubnetIsFree($host, $netmask) {
		$iplist = SubnetEditor::getSubnetIpRope();
		
		$new_subnet_host = SubnetCalculator::getDqNet($host, $netmask);
		$new_subnet_bcast = SubnetCalculator::getDqBcast($host, $netmask);
		$new_subnet_host = explode(".", $new_subnet_host);
		$new_subnet_bcast = explode(".", $new_subnet_bcast);

		$subnet_is_free = true;
		for($i=$new_subnet_host[2]; $i<=$new_subnet_bcast[2]; $i++) {
			for($ii=$new_subnet_host[3]; $ii<=$new_subnet_bcast[3]; $ii++) {
				if($iplist["$GLOBALS[net_prefix].$i.$ii"] == "$GLOBALS[net_prefix].$i.$ii") {
					$subnet_is_free = false;
					break 2;
				}
			}
		}

		return $subnet_is_free;
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
					$iplist[$GLOBALS['net_prefix'].".".$i.".".$ii] = $GLOBALS['net_prefix'].".".$i.".".$ii;
				}
			}
			return $iplist;
		}
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
	}

	public function getFreeSubnet($netmask) {
		$subnet_rope = SubnetEditor::getSubnetIpRope();

		for ($i=0; $i<=254; $i++) {
			for ($ii=0; $ii<=254; $ii++) {
				$new_subnet_host = SubnetCalculator::getDqNet($GLOBALS['net_prefix'].".".$i.".".$ii, $netmask);
				$new_subnet_bcast = SubnetCalculator::getDqBcast($GLOBALS['net_prefix'].".".$i.".".$ii, $netmask);
//Debug
/*if("$i.$ii"=="9.0") {
echo "new_subnet_host: $new_subnet_host<br>";
echo "new_subnet_bcast: $new_subnet_bcast<br>";
echo "subnet_rope_host: ".$subnet_rope[$new_subnet_host]."<br>";
echo "subnet_rope_bcast: ".$subnet_rope[$new_subnet_bcast]."<br>";
}*/
				//Shortcheck if bcast IP and host IP are free
				if ($new_subnet_host!=$subnet_rope[$new_subnet_host] AND $new_subnet_bcast!=$subnet_rope[$new_subnet_bcast]) {
					//Check if subnet is really free
					if (SubnetEditor::checkIfSubnetIsFree($new_subnet_host, $netmask)) {
						return $new_subnet_host;
					}
				}
			}
		}
		return false;
	}

	public function checkSubnetData($edit=false) {
		if($_POST['subnet_kind'] == "simple") {

			$netmask = $_POST['only_netmask'];
			$host = SubnetEditor::getFreeSubnet($netmask);
			if(!$host) {
				$message[] = array("Es konnte kein Subnet mit der Netzmaske $netmask angelegt werden",2);
			} else {
				$host = explode(".", $host);
				$host = $host[2].".".$host[3];
			}
		} elseif ($_POST['subnet_kind'] == "extend") {
			if (empty($_POST['host']) OR empty($_POST['netmask']))
				$message[] = array("Bitte geben Sie Subnethost und Netzmaske an",2);

			 $host = SubnetCalculator::getDqNet($GLOBALS['net_prefix'].".".$_POST['host'], $_POST['netmask']);
			 $host = explode(".", $host);
			 $host = $host[2].".".$host[3];

			 $netmask = $_POST['netmask'];

			//Check if the chosen subnet is still free
			if(!SubnetEditor::checkIfSubnetIsFree($GLOBALS['net_prefix'].".".$host, $netmask) AND !$edit) {
				$message[] = array("Das Subnetz ist schon belegt, bitte wählen Sie ein anderes",2);
			}
		}

		$dhcp_kind = $_POST['dhcp_kind'];

		if ($_POST['use_real_network']=='true') {
			$real_host = $_POST['real_host'];
			$real_netmask = $_POST['real_netmask'];
		} else {
			$real_host = '0';
			$real_netmask = '0';
		}

		//Check if the data for the vpn server is complete
		if ($_POST['subnet_type']=='wlan' OR $_POST['subnet_type']=='cable') {
			$vpn_server = "";
			$vpn_server_port = "";
			$vpn_server_device = "";
			$vpn_server_proto = "";
			$vpn_server_ca = "";
			$vpn_server_key = "";
			$vpn_server_cert = "";
			$vpn_server_pass = "";
			$ftp_sync = 0;
			$ftp_ccd_folder = "";
			$ftp_ccd_username = "";
			$ftp_ccd_password = "";
		} elseif ($_POST['vpn_kind']=='other') {
			$vpn_data = Helper::getSubnetById($_POST['vpnserver_from_project']);
			$vpn_server = $vpn_data['vpn_server'];
			$vpn_server_port = $vpn_data['vpn_server_port'];
			$vpn_server_device = $vpn_data['vpn_server_device'];
			$vpn_server_proto = $vpn_data['vpn_server_proto'];
			$vpn_server_ca = $vpn_data['vpn_server_ca'];
			$vpn_server_key = $vpn_data['vpn_server_key'];
			$vpn_server_cert = $vpn_data['vpn_server_cert'];
			$vpn_server_pass = $vpn_data['vpn_server_pass'];
			$ftp_sync = $vpn_data['ftp_sync'];
			$ftp_ccd_folder = $vpn_data['ftp_ccd_folder'];
			$ftp_ccd_username = $vpn_data['ftp_ccd_username'];
			$ftp_ccd_password = $vpn_data['ftp_ccd_password'];
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
				$ftp_sync = $_POST['ftp_sync'];
				$ftp_ccd_folder = $_POST['ftp_ccd_folder'];
				$ftp_ccd_username = $_POST['ftp_ccd_username'];
				$ftp_ccd_password = $_POST['ftp_ccd_password'];
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
			return array('host'=>$host, 'netmask'=>$netmask, 'real_host'=>$real_host, 'real_netmask'=>$real_netmask, 'dhcp_kind'=>$dhcp_kind, 'title'=>$title, 'description'=>$description, 'polygons'=>$polygons, 'vpn_server'=>$vpn_server, 'vpn_server_port'=> $vpn_server_port, 'vpn_server_device'=> $vpn_server_device, 'vpn_server_proto'=> $vpn_server_proto, 'vpn_server_ca'=> $vpn_server_ca, 'vpn_server_cert'=> $vpn_server_cert, 'vpn_server_key'=> $vpn_server_key, 'vpn_server_pass'=>$vpn_server_pass, 'ftp_sync'=>$ftp_sync, 'ftp_ccd_folder'=>$ftp_ccd_folder, 'ftp_ccd_username'=>$ftp_ccd_username, 'ftp_ccd_password'=>$ftp_ccd_password);
		}
	}
	
	public function deleteSubnet($subnet_id) {
		foreach (Helper::getIpsBySubnetId($subnet_id) as $ip) {
			IpEditor::deleteIp($ip['id']);
		}
		
		$subnet_data = Helper::getSubnetById($subnet_id);
		DB::getInstance()->exec("DELETE FROM subnets WHERE id='$subnet_id';");
		$message[] = array("Das Subnetz $GLOBALS[net_prefix].$subnet_data[host]/$subnet_data[netmask] wurde gelöscht.",1);
		Message::setMessage($message);
		return true;
	}
}
?>