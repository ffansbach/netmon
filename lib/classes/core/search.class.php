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
 * This class contains the menustructure.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/crawling.class.php');

class Search {
	public function searchForMacAddress($search_string) {
		try {
			$sql = "SELECT  *
					FROM crawl_interfaces
					WHERE mac_addr = '$search_string'
					GROUP BY router_id
					ORDER BY id DESC;";
			$result = DB::getInstance()->query($sql);
			foreach($result as $key=>$row) {
				$search_result_crawled_interfaces[$key] = $row;
				$search_result_crawled_interfaces[$key]['object_type'] = 'crawled_interface';
				$search_result_crawled_interfaces[$key]['router_data'] = Router::getRouterInfo($row['router_id']);
				$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
				$search_result_crawled_interfaces[$key]['router_crawl_data'] = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $row['router_id']);
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $search_result_crawled_interfaces;
	}

	public function searchForIPv6Address($search_string) {
		try {
			$sql = "SELECT  *
					FROM crawl_interfaces
					WHERE ipv6_addr LIKE '%$search_string%' OR ipv6_link_local_addr LIKE '%$search_string%'
					GROUP BY router_id
					ORDER BY id DESC;";
			$result = DB::getInstance()->query($sql);
			foreach($result as $key=>$row) {
				$search_result_crawled_interfaces[$key] = $row;
				$search_result_crawled_interfaces[$key]['object_type'] = 'crawled_interface';
				$search_result_crawled_interfaces[$key]['router_data'] = Router::getRouterInfo($row['router_id']);
				$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
				$search_result_crawled_interfaces[$key]['router_crawl_data'] = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $row['router_id']);
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

		return $search_result_crawled_interfaces;
	}

	public function searchAll($search_string) {
		$result[] = Search::searchForMacAddress($search_string);
		$result[] = Search::searchForIPv6Address($search_string);
		$return = array();
		if(!empty($result)) {
			foreach($result as $r) {
				if(is_array($r)) {
					$return = array_merge($return, $r);
				}
			}
		}

		return $return;
	}

}

?>