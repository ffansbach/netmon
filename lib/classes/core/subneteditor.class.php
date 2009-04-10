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
 * This file contains the class for editing a subnet.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class subneteditor {
  function __construct(&$smarty) {
    if ($_GET['section'] == "new") {
	$smarty->assign('message', message::getMessage());
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('avalailable_subnets', editingHelper::getFreeSubnets());
	$smarty->assign('subnets_with_defined_vpnserver', $this->getSubnetsWithDefinedVpnserver());
	$smarty->assign('get_content', "subnet_new");
    }
    if ($_GET['section'] == "insert") {
      $checkdata = $this->checkSubnetData();
      if ($checkdata) {
		$this->createNewSubnet($checkdata);
		$smarty->assign('message', message::getMessage());
		$smarty->assign('get_content', "subnet_new");
      } else {
	$smarty->assign('message', message::getMessage());
	$smarty->assign('get_content', "subnet_new");
      }
    }
    if ($_GET['section'] == "edit") {
	$smarty->assign('message', message::getMessage());
	$smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	$smarty->assign('avalailable_subnets', editingHelper::getFreeSubnets());
	$smarty->assign('subnets_with_defined_vpnserver', $this->getSubnetsWithDefinedVpnserver());
	$smarty->assign('subnet_data', $this->getSubnetDataById($_GET['id']));
	$smarty->assign('get_content', "subnet_edit");
    }
    if ($_GET['section'] == "update") {
      if ($this->checkSubnetData($_POST['subnet'], $_POST['vpnserver'], $_POST['vpnserver_from_project'], $_POST['vpnserver_from_project_check'], $_POST['no_vpnserver_check'], $_POST['vpn_cacrt'])) {
		$this->updateSubnet($_POST['subnet'],  $_POST['vpnserver'], $_POST['vpnserver_from_project'], $_POST['vpnserver_from_project_check'], $_POST['no_vpnserver_check'], $_POST['title'], $_POST['description'], $_POST['longitude'], $_POST['latitude'], $_POST['radius'], $POST['vpn_cacrt']);
		$smarty->assign('message', message::getMessage());
		$smarty->assign('get_content', "portal");
      } else {
	$smarty->assign('message', message::getMessage());
	$smarty->assign('get_content', "subnet_new");
      }
    }
  }

  public function createNewSubnet($data) {
    //Mach DB Eintrag
    $db = new mysqlClass;
    $db->mysqlQuery("INSERT INTO subnets (subnet_ip, user_id, title, description, longitude, latitude, radius, vpn_server, vpn_server_port, vpn_server_device, vpn_server_proto, vpn_server_ca, vpn_server_cert, vpn_server_key, vpn_server_pass, create_date)
				  VALUES ('$data[subnet_ip]', '$_SESSION[user_id]', '$data[title]', '$data[description]', '$data[longitude]', '$data[latitude]', '$data[radius]', '$data[vpn_server]','$data[vpn_server_port]', '$data[vpn_server_device]', '$data[vpn_server_proto]', '$data[vpn_server_ca]', '$data[vpn_server_cert]', '$data[vpn_server_key]', '$data[vpn_server_pass]', NOW());");
    $ergebniss = $db->mysqlAffectedRows();
    unset($db);
    if ($ergebniss>0) {
      $message[] = array("Das Subnetz ".$subnet." wurde in die Datenbank eingetragen.", 1);
      message::setMessage($message);
      return true;
    } else {
      $message[] = array("Das Subnetz ".$subnet." konnte nicht in die Datenbank eingetragen werden.", 2);
      message::setMessage($message);
      return false;
    }
  }

  public function updateSubnet($subnet, $vpnserver, $vpnserver_from_project, $vpnserver_from_project_check, $no_vpnserver_check, $title, $description, $longitude, $latitude, $radius, $vpn_cacrt) {
    //Optionale Werte auf Standartwerde setzen wenn sie nicht gesetzt sind
    if (!isset($title) OR $title == "") {
      $title = "Subnetz ".$subnet;
    }
    if (!isset($description) OR $description == "") {
      $description = "Ein Unterprojekt des Freifunknetzes";
    }
    if (!isset($longitude) OR $longitude == "") {
      $longitude = $GLOBALS['geographical_center_of_net_longitude'];
    }
    if (!isset($latitude) OR $latitude == "") {
      $latitude = $GLOBALS['geographical_center_of_net_latitute'];
    }
    if (!isset($radius) OR $radius == "") {
      $radius = $GLOBALS['geographical_radius_of_net'];
    }

    $this->createUpdateSubnet($subnet, $vpnserver, $title, $description, $longitude, $latitude, $radius, $_POST['vpn_cacrt']);
  }

  public function createUpdateSubnet($subnet, $vpnserver, $title, $description, $longitude, $latitude, $radius, $vpn_cacrt) {
    //Mach DB Eintrag
    $db = new mysqlClass;
    $db->mysqlQuery("UPDATE subnet SET
subnet= '$subnet',
title = '$title',
description = '$description',
longitude = '$longitude',
latitude = '$latitude',
radius = '$radius',
vpn_server ='$vpnserver',
vpn_cacrt = '$vpn_cacrt'
WHERE id = '$_GET[id]'
");
    $ergebniss = $db->mysqlAffectedRows();
    unset($db);
    if ($ergebniss>0) {
      $message[] = array("Das Subnetz ".$subnet." wurde geändet eingetragen.", 1);
      message::setMessage($message);
      return true;
    } else {
      $message[] = array("Die Änderungen im Subnetz ".$subnet." konnten nicht in die Datenbank eingetragen werden.", 2);
      message::setMessage($message);
      return false;
    }
  }

  public function getSubnetsWithDefinedVpnserver() {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("select id, subnet_ip FROM subnets WHERE vpn_server IS NOT NULL ORDER BY subnet_ip ASC");
    while($row = mysql_fetch_assoc($result)) {
      $subnets[] = $row;
    }
    unset($db);
    return $subnets;
  }

  public function checktIfSubnetExists($subnet) {
    //Prüfen ob das Subnetz schon existiert
    $db = new mysqlClass;
    $result = $db->mysqlQuery("select * from subnets WHERE subnet_ip='$subnet'");
    $subnet = $db->mysqlAffectedRows();
    unset($db);
    if ($subnet<1) {
      return false;
    } else {
      return true;
    }
  }

  public function checkSubnetData() {
    //Prüfen ob Subnet gesetzt ist
    if (!isset($_POST['subnet_ip'])) {
      $message[] = array("Sie müssen ein freies Subnetz auswählen.",2);
    } else {
      $subnet_ip = $_POST['subnet_ip'];
    }

    //Prüfen ob die Daten zum Subnet vollständig sind
    if ($_POST['no_vpnserver_check']!="true") {
      if ($_POST['vpnserver_from_project_check']!="true") {
	if (!isset($_POST['vpn_server']) OR $_POST['vpn_server'] == "") {
	  $message[] = array("Sie müssen einen VPN-Server angeben oder eine der anderen Optionen wählen.",2);
	} else {
	  if (empty($_POST['vpn_server']) OR empty($_POST['vpn_server_port']) OR empty($_POST['vpn_server_proto']) OR empty($_POST['vpn_server_device'])) {
	    $message[] = array("Die Daten zum VPN-Server sind unvollständig!",2);
	  } elseif (empty($_POST['vpn_server_ca']) OR empty($_POST['vpn_server_cert']) OR empty($_POST['vpn_server_key'])) {
	    $message[] = array("Die Daten zu den Zertifikaten sind unvollständig!",2);
	  }
	}
      } elseif (isset($_POST['vpnserver_from_project_check']) AND empty($_POST['vpnserver_from_project'])) {
	$message[] = array("Wenn Sie die VPN-Daten von einem anderen Projekt übernehmen möchten, müssen Sie sagen von welchem!",2);
      } else {
	$vpn_data = Helper::getSubnetDataBySubnetID($_POST['vpnserver_from_project']);
	$vpn_server = $vpn_data['vpn_server'];
	$vpn_server_port = $vpn_data['vpn_server_port'];
	$vpn_server_device = $vpn_data['vpn_server_device'];
	$vpn_server_proto = $vpn_data['vpn_server_proto'];
	$vpn_server_ca = $vpn_data['vpn_server_ca'];
	$vpn_server_key = $vpn_data['vpn_server_key'];
	$vpn_server_cert = $vpn_data['vpn_server_cert'];
	$vpn_server_pass = $vpn_data['vpn_server_pass'];
      }
    } else {
      $vpn_server = "";
      $vpn_server_port = "";
      $vpn_server_device = "";
      $vpn_server_proto = "";
      $vpn_server_ca = "";
      $vpn_server_key = "";
      $vpn_server_cert = "";
      $vpn_server_pass = "";
    }

    //Standartwerte bei Optionalen Feldern setzen wenn sie nicht gesetzt sind
    if (empty($_POST['title'])) {
      $title = "Subnetz ".$subnet;
    } else {
      $title = $_POST['title'];
    }
    if (empty($_POST['description'])) {
      $description = "Ein Unterprojekt des Freifunknetzes";
    } else {
      $description = $_POST['description'];
    }
    if (empty($_POST['longitude'])) {
      $longitude = $GLOBALS['geographical_center_of_net_longitude'];
    } else {
      $longitude = $_POST['longitude'];
    }
    if (empty($_POST['latitude'])) {
      $latitude = $GLOBALS['geographical_center_of_net_latitute'];
    } else {
      $latitude = $_POST['latitude'];
    }
    if (empty($_POST['radius'])) {
      $radius = $GLOBALS['geographical_radius_of_net'];
    } else {
      $radius = $_POST['radius'];
    }

    //Rückgabe
    if (isset($message) AND count($message)>0) {
      message::setMessage($message);
      return false;
    } else {
      return array('subnet_ip'=>$subnet_ip, 'title'=>$title, 'desciption'=>$desciption, 'longitude'=>$longitude, 'latitude'=>$latitude, 'radius'=>$radius, 'vpn_server'=>$vpn_server, 'vpn_server_port'=> $vpn_server_port, 'vpn_server_device'=> $vpn_server_device, 'vpn_server_proto'=> $vpn_server_proto, 'vpn_server_ca'=> $vpn_server_ca, 'vpn_server_cert'=> $vpn_server_cert, 'vpn_server_key'=> $vpn_server_key, 'vpn_server_pass'=>$vpn_server_pass);
    }
  }

  public function getSubnetDataById($subnet_id) {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT * FROM subnets
			       WHERE id='$subnet_id';");
    while($row = mysql_fetch_assoc($result)) {
      $subnet = $row;
    }
    unset($db);
    return $subnet;
  }



}
?>