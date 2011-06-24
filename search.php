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

require_once('runtime.php');

require_once('./lib/classes/core/search.class.php');

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
		$smarty->assign('search_result_crawled_interfaces', Search::searchAll($search_string));
	} elseif($search_range=='mac_addr') {
		$smarty->assign('search_result_crawled_interfaces', Search::searchForMacAddress($search_string));
	}
	elseif($search_range=='ipv6_addr') {
		$smarty->assign('search_result_crawled_interfaces', Search::searchForIPv6Address($search_string));
	}
}


$smarty->display("header.tpl.php");
$smarty->display("search.tpl.php");
$smarty->display("footer.tpl.php");

?>