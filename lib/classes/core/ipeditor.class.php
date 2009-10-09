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

class ipeditor {
  public function insertNewIp($subnet_id, $ips) {
    $ip = editingHelper::getAFreeIP($subnet_id);

    if ($ips > 0) {
      $range = editingHelper::getFreeIpZone($subnet_id, $ips, $ip);
    } else {
      $range['start'] = "NULL";
      $range['end'] = "NULL";
    }

    if ($range) {
      if ($ip != false) {
	DB::getInstance()->exec("INSERT INTO ips (user_id, subnet_id, ip_ip, zone_start, zone_end, radius, create_date) VALUES ('$_SESSION[user_id]', '$subnet_id', '$ip', '$range[start]', '$range[end]', '$_POST[radius]', NOW());");
	$ip_id = DB::getInstance()->lastInsertId();

	$service = editingHelper::addIpTyp($ip_id, $_POST['title'], $_POST['description'], $_POST['typ'], $_POST['crawler'], $_POST['port'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait']);

	$subnet = Helper::getSubnetById($subnet_id);
	if ($ips > 0) {
	  $message[] = array("Die Ip ".$GLOBALS['net_prefix'].".".$subnet.".".$ip." wurde erfolgreich im Subnetz $GLOBALS[net_prefix].$subnet.0/24 angelegt.", 1);
	  $message[] = array("Der IP-Bereich ".$GLOBALS['net_prefix'].".".$subnet.".".$range['start']." - ".$GLOBALS['net_prefix'].".".$subnet.".".$range['end']." wurde zur Vergabe über DHCP reserviert", 1);
	} else {
	  $message[] = array("Die Ip ".$GLOBALS['net_prefix'].".".$subnet.".".$ip." wurde erfolgreich im Subnetz $GLOBALS[net_prefix].$subnet.0/24 angelegt.", 1);
	}
	message::setMessage($message);
	return array("result"=>true, "ip_id"=>$ip_id, "service_id"=>$service['service_id']);
      } else {
	$message[] = array("Der Ip konnte nicht im Subnetz $subnet angelegt werden. Das Netz ist voll!", 2);
	message::setMessage($message);
	return false;
      }
    } else {
      $message[] = array("Der Ip konnte nicht im Subnetz $subnet angelegt werden. Für den Ip ist kein IP Bereich mehr frei!<br>Bitte wählen Sie einen kleineren Bereich oder benutzen Sie ein anderes Subnetz.", 2);
      message::setMessage($message);
      return false;
    }
  }

  public function deleteIp($ip_id) {
	foreach (Helper::getServicesByIpId($ip_id) as $services) {
		serviceeditor::deleteService($services['service_id'], true);
	}

	vpn::deleteCCD($ip_id);
	$ip_data = Helper::getIpInfo($ip_id);
	DB::getInstance()->exec("DELETE FROM ips WHERE id='$ip_id';");
	$message[] = array("Die IP $GLOBALS[net_prefix].$ip_data[subnet_ip].$ip_data[ip_ip] wurde gelöscht.",1);
	message::setMessage($message);
  }
  
}

?>