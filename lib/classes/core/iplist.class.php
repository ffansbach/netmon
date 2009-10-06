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
 * This file contains the class for the iplist site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class iplist {
	function getIpList() {
	  $services = Helper::getServicesByType('node');
	  foreach ($services as $service) {
	    $crawl_data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
		$crawl_data['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($crawl_data['crawl_time']));
	    $iplist[] = array_merge($service, $crawl_data);
	  }
	  return $iplist;
	}

	function getVpnList() {
	  $services = Helper::getServicesByType('vpn');
	  foreach ($services as $service) {
	    $crawl_data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
		$crawl_data['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($crawl_data['crawl_time']));
	    $iplist[] = array_merge($service, $crawl_data);
	  }
	  return $iplist;
	}
	
	function getServiceList() {
	  $services = Helper::getServicesByType('service');
	  foreach ($services as $service) {
	    $crawl_data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
		$crawl_data['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($crawl_data['crawl_time']));
	    $iplist[] = array_merge($service, $crawl_data);

	  }
	  return $iplist;
	}

	function getClientList() {
	  $services = Helper::getServicesByType('client');
	  foreach ($services as $service) {
	    $crawl_data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
		$crawl_data['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($crawl_data['crawl_time']));
	    $iplist[] = array_merge($service, $crawl_data);

	  }
	  return $iplist;
	}

	function getServiceListByUserId($user_id) {
	  $services = Helper::getServicesByUserId($user_id);
	  foreach ($services as $service) {
	    $crawl_data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
		$crawl_data['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($crawl_data['crawl_time']));
	    $iplist[] = array_merge($service, $crawl_data);

	  }
	  return $iplist;
	}
  

}

?>