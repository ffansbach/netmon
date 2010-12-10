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

require_once('./config/runtime.inc.php');
require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/crawling.class.php');

$smarty->assign('message', Message::getMessage());

if(!empty($_POST['search_range'])) {
	$search_range = $_POST['search_range'];
} else {
	$search_range = $_GET['search_range'];
}

if(!empty($_POST['search_range'])) {
	$search_string = $_POST['search_string'];
} else {
	$search_string = $_GET['search_string'];
}

if(!empty($search_string)) {
	if($search_range=='all') {

	} elseif($search_range=='mac_addr') {
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

		$smarty->assign('search_result_crawled_interfaces', $search_result_crawled_interfaces);
		/*echo $search_string;
		echo "<pre>";
			print_r($search_result_crawled_interfaces);
		echo "</pre>";*/
	}

}


$smarty->display("header.tpl.php");
$smarty->display("search.tpl.php");
$smarty->display("footer.tpl.php");

?>