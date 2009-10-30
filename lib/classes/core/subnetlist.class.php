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
	function getList() {
		$subnetlist = array();
		try {
			$sql = "SELECT subnets.id, subnets.host, subnets.netmask, subnets.title, subnets.user_id,
						   users.nickname,
						   COUNT(ips.id) as ips_in_net
					FROM subnets
					LEFT JOIN users ON (users.id=subnets.user_id)
					LEFT JOIN ips ON (ips.subnet_id=subnets.id)
					GROUP BY subnets.id
					ORDER BY subnets.host ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$subnetlist[] = $row;
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}

    	return $subnetlist;
	}	
}

?>