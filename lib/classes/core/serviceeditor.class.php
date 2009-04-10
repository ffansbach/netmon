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
 *  This file contains the class for editing a service.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class serviceeditor {
  function __construct(&$smarty) {
    if ($_GET['section'] == "new") {
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('node_data', Helper::getNodeInfo($_GET['node_id']));	
	$smarty->assign('get_content', "add_service");
    } 
    if ($_GET['section'] == "insert_service") {
      if ($_POST['ips'] > 0) {
	$node_info = Helper::getNodeInfo($_GET['node_id']);
	$range = editingHelper::getFreeIpZone($node_info['subnet_id'], $_POST['ips'], 0);
      } else {
	$range['start'] = "NULL";
	$range['end'] = "NULL";
      }
	editingHelper::addNodeTyp($_GET['node_id'], $_POST['title'], $_POST['description'], $_POST['typ'], $_POST['crawler'], $range['start'], $range['end']);
	$smarty->assign('message', message::getMessage());
	$smarty->assign('get_content', "desktop");
    }
    if ($_GET['section'] == "edit") {
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('servicedata', Helper::getServiceDataByServiceId($_GET['service_id']));
	$smarty->assign('message', message::getMessage());
	$smarty->assign('get_content', "service_edit");
    }
    if ($_GET['section'] == "insert_edit") {
	$smarty->assign('servicedata', $this->insertEditService($_GET['service_id'], $_POST['typ'], $_POST['crawler'], $_POST['title'], $_POST['description']));
	$smarty->assign('message', message::getMessage());
	$smarty->assign('get_content', "desktop");
    }
    if ($_GET['section'] == "delete") {
	$this->deleteService($_GET['service_id']);
	$smarty->assign('message', message::getMessage());
	$smarty->assign('get_content', "desktop");
    }
  }

  public function insertEditService($service_id, $typ, $crawler, $title, $description) {
    //Mach DB Eintrag
    $db = new mysqlClass;
    $db->mysqlQuery("UPDATE services SET
title = '$title',
description = '$description',
typ = '$typ',
crawler = '$crawler'
WHERE id = '$service_id'
");
    $ergebniss = $db->mysqlAffectedRows();
    unset($db);
    if ($ergebniss>0) {
      $message[] = array("Der Service mit der ID ".$service_id." wurde geändert.", 1);
      message::setMessage($message);
      return true;
    } else {
      $message[] = array("Der Service mit der ID ".$service_id." wurde nicht geändert, da keine Änderungen vorgenommen wurde.", 2);
      message::setMessage($message);
      return false;
    }
  }

  public function deleteService($service_id) {
	  $db = new mysqlClass;
	  $db->mysqlQuery("DELETE FROM services WHERE id='$service_id';");
	  unset($db);
	  $message[] = array("Der Service mit der ID ".$service_id." wurde gelöscht.",1);
	  message::setMessage($message);
  }
  
}

?> 
