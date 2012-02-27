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

require_once 'lib/classes/core/service.class.php';

class ServiceEditor {
	public function addService($router_id, $title, $description, $ip_addresses, $port, $url_prefix, $visible, $notify, $notification_wait, $use_netmons_url=false, $url='') {
		//Create service in the database
		DB::getInstance()->exec("INSERT INTO services (router_id, title, description, port, url_prefix, visible, notify, notification_wait, use_netmons_url, url, create_date)
					 VALUES ('$router_id', '$title', '$description', '$port', '$url_prefix', '$visible', '$notify', '$notification_wait', '$use_netmons_url', '$url', NOW());");
		$service_id = DB::getInstance()->lastInsertId();

		//Link selected ip addresses to the service
		foreach($ip_addresses as $ip_id) {
			DB::getInstance()->exec("INSERT INTO service_ips (service_id, ip_id)
						VALUES ('$service_id', '$ip_id');");
		}

		//Create first crawl entry for the service with status unknown
		$crawl_cycle = Crawling::getLastEndedCrawlCycle();
		Service::insertCrawl($service_id, "unknown", "", $crawl_cycle);

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

	public function insertEditService($service_id, $title, $description, $port, $url_prefix, $visible, $notify, $notification_wait, $use_netmons_url, $url) {
		$service_data = Service::getServiceByServiceId($service_id);

		$result = DB::getInstance()->exec("UPDATE services SET
							title='$title',
							description='$description',
							port='$port',
							url_prefix='$url_prefix',
							visible='$visible',
							notify='$notify',
							notification_wait='$notification_wait',
							use_netmons_url='$use_netmons_url',
							url='$url'
						WHERE id = '$service_id'");

		if ($result>0) {
			$message[] = array("Der Dienst $title wurde geändert",1);
			Message::setMessage($message);
			return array("result"=>true, "service_id"=>$service_id, "router_id"=>$service_data['router_id']);
		} else {
			$message[] = array("Fehler!", 2);
			Message::setMessage($message);
			return array("result"=>false, "service_id"=>$service_id, "router_id"=>$service_data['router_id']);
		}
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