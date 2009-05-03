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
  function __construct(&$smarty) {
    if ($_GET['section'] == "new") {
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('existing_subnets', editingHelper::getExistingSubnetsWithID());
	$smarty->assign('message', message::getMessage());
	$smarty->assign('get_content', "node_new");
    }
    if ($_GET['section'] == "insert") {
		if ($this->insertNewNode($_POST['subnet_id'], $_POST['ips'])) {
			$smarty->assign('message', message::getMessage());
			$smarty->assign('get_content', "desktop");
		} else {
			$smarty->assign('message', message::getMessage());
			$smarty->assign('get_content', "nodeeditor");
		}
    }
    if ($_GET['section'] == "edit") {
		$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
		$node_data = $this->getNodeData($_GET['id']);
		$smarty->assign('node_data', $node_data);
		$smarty->assign('message', message::getMessage());
		$smarty->assign('get_content', "node_edit");
    }
    if ($_GET['section'] == "delete") {
		$this->deleteNode($_GET['id']);
		$smarty->assign('message', message::getMessage());
		$smarty->assign('get_content', "desktop");
    }
  }

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
	//Mach DB Eintrag
	$db = new mysqlClass;
	$db->mysqlQuery("INSERT INTO nodes (user_id, subnet_id, node_ip, create_date) VALUES ('$_SESSION[user_id]', '$subnet_id', '$node', NOW());");
	$id = $db->getInsertID();
	unset($db);

	editingHelper::addNodeTyp($id, $_POST['title'], $_POST['description'], $_POST['typ'], $_POST['crawler'], $_POST['port'], $range['start'], $range['end'], $_POST['radius'], $_POST['visible']);

	$subnet = Helper::getSubnetById($subnet_id);
	if ($ips > 0) {
	  $message[] = array("Der Node ".$GLOBALS['net_prefix'].".".$subnet.".".$node." wurde erfolgreich im Subnetz $subnet angelegt und der IP-Bereich $range[start]-$range[end] reserviert", 1);
	} else {
	  $message[] = array("Der Node ".$GLOBALS['net_prefix'].".".$subnet.".".$node." wurde erfolgreich im Subnetz $subnet angelegt. Es wurde KEIN IP-Bereich reserviert", 1);
	}
	message::setMessage($message);
	return true;
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

  public function getNodeData($id) {
		$db = new mysqlClass;
		$result = $db->mysqlQuery("SELECT nodes.id, nodes.user_id, nodes.node_ip, nodes.subnet_id, DATE_FORMAT(nodes.create_date, '%D %M %Y') as create_date,
					  users.nickname,
					  subnets.title, subnets.subnet_ip
				  FROM nodes
				   LEFT JOIN users ON (users.id=nodes.user_id)
				   LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
				   WHERE nodes.id='$id';");
    
		while($row = mysql_fetch_assoc($result)) {
		      $node = $row;
		}
		return $node;
  }

  public function deleteNode($node_id) {
	foreach (Helper::getServicesByNodeId($node_id) as $service_id) {
		serviceeditor::deleteService($service_id);
	}

	vpn::deleteCCD($node_id);
	
	$db = new mysqlClass;
	$db->mysqlQuery("DELETE FROM nodes WHERE id='$node_id';");
	unset($db);
	$message[] = array("Der Node mit der ID ".$node_id." wurde gelöscht.",1);
	message::setMessage($message);
  }
  
}

?>