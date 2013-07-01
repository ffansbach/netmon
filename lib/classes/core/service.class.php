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

require_once(ROOT_DIR.'/lib/classes/extern/xmpphp/XMPP.php');
require_once(ROOT_DIR.'/lib/classes/core/crawling.class.php');
require_once(ROOT_DIR.'/lib/classes/core/interfaces.class.php');

class Service {
	public function getServiceList($view="", $user_id=false, $router_id=false) {
		if($view=='public') {
			$sql_add = 'WHERE visible=1';
		} elseif($view=='all' OR empty($view)) {
			$sql_add = '';
		}

		if ($user_id!=false AND is_numeric($user_id)) {
			if ($sql_add=='') {
				$sql_add.=' WHERE ';
			} else {
				$sql_add.=' AND ';
			}
			$sql_add.="routers.user_id='$user_id'";
		}

		if ($router_id!=false AND is_numeric($router_id)) {
			if ($sql_add=='') {
				$sql_add.=' WHERE ';
			} else {
				$sql_add.=' AND ';
			}
			$sql_add.="routers.id='$router_id'";
		}

		$last_ended_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		$servicelist = array();
		try {
			$stmt = DB::getInstance()->prepare("SELECT  services.id as service_id, services.router_id, services.title, services.description, services.port, services.url_prefix, services.visible, services.notify, services.notification_wait, services.notified, services.last_notification, services.use_netmons_url, services.url, services.create_date,
								    routers.hostname, routers.user_id,
								    users.id as user_id, users.nickname,
								    crawl_routers.status as router_status
							    FROM services
							    LEFT JOIN routers on (routers.id=services.router_id)
							    LEFT JOIN users on (users.id=routers.user_id)
							    LEFT JOIN crawl_routers on (crawl_routers.crawl_cycle_id=? AND crawl_routers.router_id=services.router_id)
							    $sql_add");
			$stmt->execute(array($last_ended_crawl_cycle['id']));
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		
		foreach($rows as $key=>$row) {
			//Get ip addresses assigned to the service
			try {
				$stmt = DB::getInstance()->prepare("SELECT ips.ip, ips.ipv
								    FROM ips, service_ips
								    WHERE ips.id=service_ips.ip_id AND service_ips.service_id=?");
				$stmt->execute(array($row['service_id']));
				$rows2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}

			foreach($rows2 as $key2=>$row2) {
				$row['ips'][] = $row2;
			}
			
			$service_crawl = Service::getCrawlServiceByCrawlCycleId($last_ended_crawl_cycle['id'], $row['service_id']);
			$row['service_status'] = $service_crawl['status'];
			$row['service_status_crawled_ipv4_addr'] = $service_crawl['crawled_ipv4_addr'];
			$row['service_status_crawl_date'] = $service_crawl['crawl_date'];
			
			$servicelist[] = $row;
		}
		return $servicelist;
	}

	public function getServiceByServiceId($service_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM services WHERE id=?");
			$stmt->execute(array($service_id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}

	public function getServicesByRouterId($router_id, $view) {
		if($view=='public') {
			$sql_add = 'AND visible=1';
		} elseif($view=='all') {
			$sql_add = '';
		} else {
			$sql_add = '';
		}

		try {
			$stmt = DB::getInstance()->prepare("SELECT * FROM services WHERE router_id=? $sql_add");
			$stmt->execute(array($router_id));
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
	
	public function insertCrawl($service_id, $status, $crawled_ipv4_addr, $crawl_cycle=array()) {
		if (empty($crawl_cycle)) {
			$crawl_cycle = Crawling::getActualCrawlCycle();
		}

		//Check if interface has already been crawled in current crawl cycle
		$crawl_service = Service::getCrawlServiceByCrawlCycleId($service_id, $crawl_cycle['id']);
		
		//Make DB insert if service has not been crawled in current crawl cycle
		if(empty($crawl_service)) {
			try {
				$stmt = DB::getInstance()->prepare("INSERT INTO crawl_services (service_id, crawl_cycle_id, crawl_date, status, crawled_ipv4_addr)
								    VALUES (?, ?, NOW(), ?, ?");
				$stmt->execute(array($service_id, $crawl_cycle['id'], $status, $crawled_ipv4_addr));
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
		}
	}

	public function getCrawlServiceByCrawlCycleId($crawl_cycle_id, $service_id) {
		try {
			$stmt = DB::getInstance()->prepare("SELECT  *
							    FROM crawl_services
							    WHERE service_id=? AND crawl_cycle_id=?");
			$stmt->execute(array($service_id, $crawl_cycle_id));
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
	}
}
?>