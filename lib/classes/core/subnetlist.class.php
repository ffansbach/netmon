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
 * This file contains the class for the subnetlist site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class subnetlist {
	function __construct(&$smarty) {
		if (!isset($_GET['section'])) {
		  $smarty->assign('subnetlist', $this->getList());
		  $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
	      $smarty->assign('get_content', "subnetlist");
		}
		
	}
	
	function getList() {
		
		$db = new mysqlClass;
    	$result = $db->mysqlQuery("SELECT subnets.id, subnets.subnet_ip, subnets.title, subnets.user_id,
users.nickname,
COUNT(nodes.id) as nodes_in_net
FROM subnets
LEFT JOIN users ON (users.id=subnets.user_id)
LEFT JOIN nodes ON (nodes.subnet_id=subnets.id)
GROUP BY subnets.id
ORDER BY subnets.subnet_ip ASC
    ");
    
    	while($row = mysql_fetch_assoc($result)) {
      		$subnetlist[] = $row;
    	}
    	return $subnetlist;
	}	
}

?>