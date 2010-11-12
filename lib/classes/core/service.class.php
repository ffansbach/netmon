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
 * This file contains the class for the service site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

require_once $path.'lib/classes/extern/xmpphp/XMPP.php';
require_once $path.'lib/classes/core/crawling.class.php';
require_once $path.'lib/classes/core/interfaces.class.php';

class Service {
	public function getServiceList() {
		$last_ended_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		$servicelist = array();
		try {
			$sql = "SELECT  services.id as service_id, services.router_id, services.title, services.description, services.port, services.url_prefix, services.visible, services.notify, services.notification_wait, services.notified, services.last_notification, services.use_netmons_url, services.url, services.create_date,
					routers.hostname, routers.user_id,
					users.id as user_id, users.nickname,
					crawl_routers.status as router_status
					FROM services
					LEFT JOIN routers on (routers.id=services.router_id)
					LEFT JOIN users on (users.id=routers.user_id)
					LEFT JOIN crawl_routers on (crawl_routers.crawl_cycle_id='$last_ended_crawl_cycle[id]' AND crawl_routers.router_id=services.router_id)";
			$result = DB::getInstance()->query($sql);
			foreach($result as $key=>$row) {
				$interfaces = Interfaces::getIPv4InterfacesByRouterId($row['router_id']);
				if(!empty($interfaces) AND !empty($row['url_prefix']) AND !empty($row['port'])) {
					$row['combined_url_to_service'] = $row['url_prefix'].$interfaces[0]['ipv4_addr'].":".$row['port'];
				}

				$servicelist[] = $row;
			}
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $servicelist;
	}

	public function getServiceListByUserId($user_id) {
		$last_ended_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		$servicelist = array();
		try {
			$sql = "SELECT  services.id as service_id, services.router_id, services.title, services.description, services.port, services.url_prefix, services.visible, services.notify, services.notification_wait, services.notified, services.last_notification, services.use_netmons_url, services.url, services.create_date,
					routers.hostname, routers.user_id,
					users.id as user_id, users.nickname,
					crawl_routers.status as router_status
					FROM services
					LEFT JOIN routers on (routers.id=services.router_id)
					LEFT JOIN users on (users.id=routers.user_id)
					LEFT JOIN crawl_routers on (crawl_routers.crawl_cycle_id='$last_ended_crawl_cycle[id]' AND crawl_routers.router_id=services.router_id)
					WHERE routers.user_id='$user_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $key=>$row) {
				$interfaces = Interfaces::getIPv4InterfacesByRouterId($row['router_id']);
				if(!empty($interfaces) AND !empty($row['url_prefix']) AND !empty($row['port'])) {
					$row['combined_url_to_service'] = $row['url_prefix'].$interfaces[0]['ipv4_addr'].":".$row['port'];
				}

				$servicelist[] = $row;
			}
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $servicelist;
	}

	public function getServiceByServiceId($service_id) {
		try {
			$sql = "SELECT  *
					FROM services
					WHERE id='$service_id'";
			$result = DB::getInstance()->query($sql);
			$service_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $service_data;
	}

	public function getServicesByRouterId($router_id) {
		$servicelist = array();
		try {
			$sql = "SELECT  *
					FROM services
					WHERE router_id='$router_id'";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$servicelist[] = $row;
			}
		}
		catch(PDOException $e) {
		  echo $e->getMessage();
		}
		return $servicelist;
	}



	public function insertEditService($service_id, $typ, $crawler, $title, $description, $visible, $notify, $notification_wait, $use_netmons_url, $url) {
		DB::getInstance()->exec("UPDATE services SET
										title = '$title',
										description = '$description',
										typ = '$typ',
										crawler = '$crawler',
										visible = '$visible',
										notify = '$notify',
										notification_wait = '$notification_wait',
										use_netmons_url = '$use_netmons_url',
										url = '$url'
								WHERE id = '$service_id'");
		
		$message[] = array("Der Service mit der ID ".$service_id." wurde geändert.", 1);
		Message::setMessage($message);
		return array("result"=>true, "service_id"=>$service_id);
	}

	public function deleteService($service_id, $force=false) {
		if ($_POST['delete']=="true") {

			$service_data = Helper::getServiceDataByServiceId($service_id);
			if(count(Helper::getServicesByIpId($service_data['ip_id']))<2 AND !$force) {
				$link1 = "<a href=\"./serviceeditor.php?section=new&ip_id=$service_data[ip_id]\">hier</a>";
				$link2 = "<a href=\"./ipeditor.php?section=edit&id=$service_data[ip_id]\">hier</a>";
				$message[] = array("Sie können diesen Service nicht löschen da eine IP durch mindestens einen Service spezifiziert werden muss.<br>"
									."Um einen 2. Service zu erstellen klicken Sie bitte $link1<br>"
									."Um die IP zu komplett zu löschen, klicken Sie bitte $link2", 2);
				Message::setMessage($message);
				return false;
			} else {
				DB::getInstance()->exec("DELETE FROM services WHERE id='$service_id';");
				$message[] = array("Der Service mit der ID ".$service_id." wurde gelöscht.",1);
				
				DB::getInstance()->exec("DELETE FROM crawl_data WHERE service_id='$service_id';");
				$message[] = array("Die Crawl-Daten des Service mit der ID ".$service_id." wurden gelöscht.",1);

				DB::getInstance()->exec("DELETE FROM history WHERE object='service' AND object_id='$service_id';");
				$message[] = array("Die History-Daten des Service mit der ID ".$service_id." wurden gelöscht.",1);
				
				Message::setMessage($message);
				return true;
			}
		} else {
			$message[] = array("Zum löschen des Services bitte das Häckchen bei \"Ja\" setzen.",2);

			Message::setMessage($message);
			return false;
		}
	}



	public function getCrawlHistory($service_id, $count) {
		$last_crawl = array();
		try {
			$sql = "SELECT * FROM crawl_data
					WHERE service_id='$service_id' ORDER BY id DESC LIMIT $count";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$last_crawl[] = $row;
			}
		 }
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $last_crawl;
	}
}
?>