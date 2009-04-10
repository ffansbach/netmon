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
 * This file contains a class for some statistical graphics.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

require_once 'Image/Graph.php';

  class graphmonitor {
  
  function __construct() {
    eval("graphmonitor::".$_GET['section']."();");
    die();
  }

  function getStatus() {

// create the graph
$Graph =& Image_Graph::factory('graph', array(400, 300)); 

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Verfuegbarkeit im Laufe der letzten 12 Stunden', 12)),        
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);
$Legend->setPlotarea($Plotarea);        


    $crawl_from = time()-(60*60*48);
    $crawl_now = time();
    $jedenXten = 12;

    $db = new mysqlClass;
    $result = $db->mysqlQuery("SELECT id FROM crawls WHERE MOD(ID, $jedenXten) = '0' AND UNIX_TIMESTAMP(crawl_time_start)>$crawl_from AND UNIX_TIMESTAMP(crawl_time_end)<=$crawl_now");
    while($row = mysql_fetch_assoc($result)) {
      $crawls[] = $row['id'];
    }
    unset($db);


    $Datasets[0] =& Image_Graph::factory('dataset'); 
    $Datasets[1] =& Image_Graph::factory('dataset'); 
    $Datasets[2] =& Image_Graph::factory('dataset');

    foreach ($crawls as $key=>$crawl_id) {
      $db = new mysqlClass;
      $result = $db->mysqlQuery("SELECT count(*) as count FROM crawl_data WHERE  crawl_id=$crawl_id AND status='online'");
      while($row = mysql_fetch_assoc($result)) {
	$Datasets[0]->addPoint($key, $row['count']);
      }
      unset($db);

      $db = new mysqlClass;
      $result = $db->mysqlQuery("SELECT count(*) as count FROM crawl_data WHERE  crawl_id=$crawl_id AND status='offline'");
      while($row = mysql_fetch_assoc($result)) {
	$Datasets[1]->addPoint($key, $row['count']);
      }
      unset($db);

      $db = new mysqlClass;
      $result = $db->mysqlQuery("SELECT count(*) as count FROM crawl_data WHERE  crawl_id=$crawl_id AND status='ping'");
      while($row = mysql_fetch_assoc($result)) {
	$Datasets[2]->addPoint($key, $row['count']);
      }
      unset($db);

    }

// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Area', array($Datasets, 'stacked'));

// set a line color
$Plot->setLineColor('gray');

$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('green');
$FillArray->addColor('red');
$FillArray->addColor('blue');

// set a standard fill style
$Plot->setFillStyle($FillArray);
	
// output the Graph
$Graph->done();


    }
    
  }

?>