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
 * This file contains the class for the status site.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class status {
	public function getCrawlerStatus() {
		try {
			$sql = "SELECT id, UNIX_TIMESTAMP(crawl_time_end) as last_crawl
					FROM crawls
			        ORDER BY id DESC LIMIT 1;";
			$result = DB::getInstance()->query($sql); 
			$last_crawl = $result->fetch(PDO::FETCH_ASSOC); 
		} 
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
		
		$last_crawl = $last_crawl['last_crawl'];
		$toleranzgrenze = 300; //seconds
		if ((time()-$last_crawl)>($GLOBALS['timeBetweenCrawls']*60+$toleranzgrenze))
			$return['status'] = "Offline";
		else
			$return['status'] = "Online";
		
		$return['last_crawl'] = date("H:i:s", $last_crawl);
		$return['next_crawl'] = date("H:i:s", $GLOBALS['timeBetweenCrawls']*60+$last_crawl);
		return $return;
	}

  public function getNewestUser() {
		try {
			$sql = "SELECT id, nickname, create_date
					FROM users
					ORDER BY id DESC LIMIT 1;";
			$result = DB::getInstance()->query($sql); 
			$newest_user = $result->fetch(PDO::FETCH_ASSOC); 
		} 
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
		
		return $newest_user;    
	}

  public function getNewestNode() {
		try {
			$sql = "SELECT nodes.id, nodes.user_id, nodes.node_ip, DATE_FORMAT(nodes.create_date, '%D %M %Y') as create_date,
						   users.nickname,
						   subnets.title, subnets.subnet_ip
					FROM nodes
					LEFT JOIN users ON (users.id=nodes.user_id)
					LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
					ORDER BY nodes.id DESC LIMIT 1;";
			$result = DB::getInstance()->query($sql); 
			$newest_node = $result->fetch(PDO::FETCH_ASSOC); 
		} 
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
		
		return $newest_node;    
	}

  public function getNewestService() {
		try {
			$sql = "SELECT services.node_id, services.title,
					  nodes.node_ip,
					  subnets.subnet_ip
				  FROM services
				  LEFT JOIN nodes ON (nodes.id = services.node_id)
				   LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
				  WHERE services.typ LIKE 'service'
			       ORDER BY services.id DESC LIMIT 1;";
			$result = DB::getInstance()->query($sql); 
			$newest_service = $result->fetch(PDO::FETCH_ASSOC); 
		} 
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
		
		return $newest_service;    
	}
}

?>