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

require_once $path.'lib/classes/core/service.class.php';

class ServiceEditor {
	public function addService($router_id, $title, $description, $port, $visible, $notify, $notification_wait, $use_netmons_url=false, $url='') {
		DB::getInstance()->exec("INSERT INTO services (router_id, title, description, port, visible, notify, notification_wait, use_netmons_url, url, create_date)
					 VALUES ('$router_id', '$title', '$description', '$port', '$visible', '$notify', '$notification_wait', '$use_netmons_url', '$url', NOW());");
		$service_id = DB::getInstance()->lastInsertId();
		
		try {
			$sql = "select hostname FROM routers WHERE id='$router_id'";
			$result = DB::getInstance()->query($sql);
			$router_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		$message[] = array("Ein Service auf Port $port wurde dem Router $router_data[hostname] hinzugefügt.",1);
		Message::setMessage($message);
		
		return array("result"=>true, "service_id"=>$service_id, "router_id"=>$router_id);
	}

	public function deleteService($service_id) {
		$service_data = Service::getServiceByServiceId($service_id);

		//Delete Service
		try {
			DB::getInstance()->exec("DELETE FROM services WHERE id='$service_id';");
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		$message[] = array("Der Service $service_data[title] wurde entfernt.",1);
		Message::setMessage($message);
		return true;
	}
}
?>