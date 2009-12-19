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

require_once("./lib/classes/core/serviceeditor.class.php");
require_once("./lib/classes/core/vpn.class.php");

/**
 * This file contains the class for editing a ip (IP). 
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class IpEditor {
  public function insertNewIp() {
	$subnet = Helper::getSubnetById($_POST['subnet_id']);

	//Get an ip or check if ip is free
	if($_POST['ip_kind']=='simple') {
		$ip = EditingHelper::getAFreeIP($_POST['subnet_id']);
	} elseif($_POST['ip_kind']=='extend') {
		if(EditingHelper::checkIfIpIsFree($_POST['ip'], $_POST['subnet_id'])) {
			$ip = $_POST['ip'];
		} else {
			$message[] = array("Die Ip ".$GLOBALS['net_prefix'].".".$_POST['ip']." existiert bereits oder gehört nicht zm Subnetz Subnetz $GLOBALS[net_prefix].$subnet[host]/$subnet[netmask].", 2);
			Message::setMessage($message);
			return false;
		}
	}

	//predefine
	$range['start'] = 'NULL';
	$range['end'] = 'NULL';
	$dhcp_host = 'NULL';
	$dhcp_netmask = 'NULL';

	if($subnet['dhcp_kind']=="ips") {
		if($_POST['dhcp_ips_kind']=='simple') {
			$range = EditingHelper::getFreeIpZone($_POST['subnet_id'], $_POST['ips'], $ip);
		} elseif($_POST['dhcp_ips_kind']=='extend') {
			if(EditingHelper::checkIfRangeIsFree($_POST['subnet_id'], $ip, $_POST['dhcp_first'], $_POST['dhcp_last'])) {
				$range['start'] = $_POST['dhcp_first'];
				$range['end'] = $_POST['dhcp_last'];
			} else {
				$message[] = array("Die DHCP-Zone existiert bereits im Subnetz $GLOBALS[net_prefix].$subnet[host]/$subnet[netmask].", 2);
				Message::setMessage($message);
				return false;
			}
		}
	} elseif($subnet['dhcp_kind']=="subnet") {
		//TODO: check if subnet is free

		$dhcp_host = $_POST['dhcp_subnet_host'];
		$dhcp_netmask = $_POST['dhcp_subnet_netmask'];
	} elseif($subnet['dhcp_kind']=="nat") {
		$dhcp_host = $_POST['dhcp_nat_host'];
		$dhcp_netmask = $_POST['dhcp_nat_netmask'];
	}


    if ($range) {
      if ($ip != false) {
	DB::getInstance()->exec("INSERT INTO ips (user_id, subnet_id, ip, zone_start, zone_end, dhcp_host, dhcp_netmask, radius, create_date)
							VALUES ('$_SESSION[user_id]', '$_POST[subnet_id]', '$ip', '$range[start]', '$range[end]', '$dhcp_host', '$dhcp_netmask', '$_POST[radius]', NOW());");
	$ip_id = DB::getInstance()->lastInsertId();

	if ($range['start']!='NULL') {
	  $message[] = array("Die Ip ".$GLOBALS['net_prefix'].".".$ip." wurde erfolgreich im Subnetz $GLOBALS[net_prefix].$subnet[host]/$subnet[netmask] angelegt.", 1);
	  $message[] = array("Der IP-Bereich ".$GLOBALS['net_prefix'].".".$range['start']." - ".$GLOBALS['net_prefix'].".".$range['end']." wurde zur Vergabe über DHCP reserviert", 1);
	} else {
	  $message[] = array("Die Ip ".$GLOBALS['net_prefix'].".".$ip." wurde erfolgreich im Subnetz $GLOBALS[net_prefix].$subnet[host]/$subnet[netmask] angelegt.", 1);
	}
	Message::setMessage($message);

	$service = EditingHelper::addIpTyp($ip_id, $_POST['title'], $_POST['description'], $_POST['typ'], $_POST['crawler'], $_POST['port'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait']);
	if(!$service['result']) {
		IpEditor::deleteIp($ip_id);
		return false;
	}

	return array("result"=>true, "ip_id"=>$ip_id, "service_id"=>$service['service_id']);
      } else {
	$message[] = array("Der Ip konnte nicht im Subnetz $subnet angelegt werden. Das Netz ist voll!", 2);
	Message::setMessage($message);
	return false;
      }
    } else {
      $message[] = array("Die Ip konnte nicht im Subnetz $GLOBALS[net_prefix].$subnet[host]/$subnet[netmask] angelegt werden. Für die Ip ist kein IP Bereich mehr frei!<br>Bitte wählen Sie einen kleineren Bereich oder benutzen Sie ein anderes Subnetz.", 2);
      Message::setMessage($message);
      return false;
    }
  }

	public function insertEditIp($ip_id, $radius) {
		DB::getInstance()->exec("UPDATE ips SET
										radius = '$radius'
								WHERE id = '$ip_id'");
		$ip_data = Helper::getIpInfo($ip_id);
		$message[] = array("Die Ip $GLOBALS[net_prefix].$ip_data[ip]. wurde geändert.", 1);
		Message::setMessage($message);
		return true;
	}

  public function deleteIp($ip_id) {
	foreach (Helper::getServicesByIpId($ip_id) as $services) {
		serviceeditor::deleteService($services['service_id'], true);
	}

	Vpn::deleteCCD($ip_id);
	$ip_data = Helper::getIpInfo($ip_id);
	DB::getInstance()->exec("DELETE FROM ips WHERE id='$ip_id';");
	$message[] = array("Die IP $GLOBALS[net_prefix].$ip_data[ip] wurde gelöscht.",1);
	Message::setMessage($message);
  }
  
}

?>