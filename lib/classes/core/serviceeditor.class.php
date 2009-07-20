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
  public function insertEditService($service_id, $typ, $crawler, $title, $description, $radius, $visible) {
    //Mach DB Eintrag
    $db = new mysqlClass;
    $db->mysqlQuery("UPDATE services SET
title = '$title',
description = '$description',
typ = '$typ',
crawler = '$crawler',
radius = $radius,
visible = '$visible'
WHERE id = '$service_id'
");
    $ergebniss = $db->mysqlAffectedRows();
    unset($db);
    if ($ergebniss>0) {
      $message[] = array("Der Service mit der ID ".$service_id." wurde geändert.", 1);
      message::setMessage($message);
      return array("result"=>true, "service_id"=>$service_id);
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

		$db = new mysqlClass;
		$db->mysqlQuery("DELETE FROM crawl_data WHERE service_id='$service_id';");
		unset($db);
		$message[] = array("Die Crawl-Daten des Service mit der ID ".$service_id." wurde gelöscht.",1);

		message::setMessage($message);
	}
  
}

?>