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

require_once 'lib/classes/core/service.class.php';

class ApiService {
	public function insertCrawl($nickname, $password, $service_id, $status, $crawled_ipv4_addr) {
		$session = login::user_login($nickname, $password);
		
		$service_data = Service::getServiceByServiceId($service_id);

		//If is owning user or if root
		if(UserManagement::isThisUserOwner($service_data['user_id'], $session['user_id']) OR $session['permission']==120) {
			Service::insertCrawl($service_id, $status, $crawled_ipv4_addr);
		}

		return(array('status'=>"success", 'error_message'=>""));
	}

	public function getServiceList($view) {
		$servicelist = Service::getServiceList($view);
		return $servicelist;
	}
}
?>