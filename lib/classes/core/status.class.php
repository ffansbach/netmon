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
  function __construct(&$smarty) {
    if (!isset($_GET['section'])) {
      $smarty->assign('status', $this->getCrawlerStatus());
      $smarty->assign('newest_user', $this->getNewestUser());
      $smarty->assign('newest_node', $this->getNewestNode());
      $smarty->assign('newest_service', $this->getNewestService());

      $smarty->assign('net_prefix', $GLOBALS['net_prefix']);
      $smarty->assign('zeit', date("H:i:s", time()));
      $smarty->assign('get_content', "status");
    }
  }

  public function getCrawlerStatus() {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT id, UNIX_TIMESTAMP(crawl_time_end) as last_crawl
			       FROM crawls
			       ORDER BY id DESC LIMIT 1;");
    
    while($row = mysql_fetch_assoc($result)) {
      $last_crawl = $row['last_crawl'];
    }
    unset($db);

    $toleranzgrenze = 300; //sekunden
    if ((time()-$last_crawl)>($GLOBALS['timeBetweenCrawls']*60+$toleranzgrenze)) {
      $return['status'] = "Offline";
    } else {
      $return['status'] = "Online";
    }

    $return['last_crawl'] = date("H:i:s", $last_crawl);
    $return['next_crawl'] = date("H:i:s", $GLOBALS['timeBetweenCrawls']*60+$last_crawl);

    return $return;
  }

  public function getNewestUser() {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT id, nickname, create_date
			       FROM users
			       ORDER BY id DESC LIMIT 1;");
    
    while($row = mysql_fetch_assoc($result)) {
      $newest_user = $row;
    }
    unset($db);

    return $newest_user;    
  }

  public function getNewestNode() {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT nodes.id, nodes.user_id, nodes.node_ip, DATE_FORMAT(nodes.create_date, '%D %M %Y') as create_date,
					  users.nickname,
					  subnets.title, subnets.subnet_ip
				  FROM nodes
				   LEFT JOIN users ON (users.id=nodes.user_id)
				   LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
			       ORDER BY nodes.id DESC LIMIT 1;");
    
    while($row = mysql_fetch_assoc($result)) {
      $newest_node = $row;
    }
    unset($db);

    return $newest_node;    
  }

  public function getNewestService() {
    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT services.node_id, services.title,
					  nodes.node_ip,
					  subnets.subnet_ip
				  FROM services
				  LEFT JOIN nodes ON (nodes.id = services.node_id)
				   LEFT JOIN subnets ON (subnets.id=nodes.subnet_id)
				  WHERE services.typ LIKE 'service'
			       ORDER BY services.id DESC LIMIT 1;");
    
    while($row = mysql_fetch_assoc($result)) {
      $newest_user = $row;
    }
    unset($db);

    return $newest_user;    
  }

}

?>