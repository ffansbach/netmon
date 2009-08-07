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
 * This file contains the class for editing a node (IP). 
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class nodeeditor {
  public function insertNewNode($subnet_id, $ips) {
    $node = editingHelper::getAFreeIP($subnet_id);

    if ($ips > 0) {
      $range = editingHelper::getFreeIpZone($subnet_id, $ips, $node);
    } else {
      $range['start'] = "NULL";
      $range['end'] = "NULL";
    }

    if ($range) {
      if ($node != false) {
	DB::getInstance()->exec("INSERT INTO nodes (user_id, subnet_id, node_ip, create_date) VALUES ('$_SESSION[user_id]', '$subnet_id', '$node', NOW());");
	$node_id = DB::getInstance()->lastInsertId();

	$service = editingHelper::addNodeTyp($node_id, $_POST['title'], $_POST['description'], $_POST['typ'], $_POST['crawler'], $_POST['port'], $range['start'], $range['end'], $_POST['radius'], $_POST['visible'], $_POST['notify'], $_POST['notification_wait']);

	$subnet = Helper::getSubnetById($subnet_id);
	if ($ips > 0) {
	  $message[] = array("Der Node ".$GLOBALS['net_prefix'].".".$subnet.".".$node." wurde erfolgreich im Subnetz $subnet angelegt und der IP-Bereich $range[start]-$range[end] reserviert", 1);
	} else {
	  $message[] = array("Der Node ".$GLOBALS['net_prefix'].".".$subnet.".".$node." wurde erfolgreich im Subnetz $subnet angelegt. Es wurde KEIN IP-Bereich reserviert", 1);
	}
	message::setMessage($message);
	return array("result"=>true, "node_id"=>$node_id, "service_id"=>$service['service_id']);
      } else {
	$message[] = array("Der Node konnte nicht im Subnetz $subnet angelegt werden. Das Netz ist voll!", 2);
	message::setMessage($message);
	return false;
      }
    } else {
      $message[] = array("Der Node konnte nicht im Subnetz $subnet angelegt werden. Für den Node ist kein IP Bereich mehr frei!<br>Bitte wählen Sie einen kleineren Bereich oder benutzen Sie ein anderes Subnetz.", 2);
      message::setMessage($message);
      return false;
    }
  }

  public function deleteNode($node_id) {
	foreach (Helper::getServicesByNodeId($node_id) as $services) {
		serviceeditor::deleteService($services['service_id'], true);
	}

	vpn::deleteCCD($node_id);
	
	DB::getInstance()->exec("DELETE FROM nodes WHERE id='$node_id';");
	$message[] = array("Der Node mit der ID ".$node_id." wurde gelöscht.",1);
	message::setMessage($message);
  }
  
}

?>