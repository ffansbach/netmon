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
 * This file contains the class for exporting informations.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class getinfo {

  function __construct() {
    eval("getinfo::".$_GET['section']."();");
    die();
  }
  

   //Funktion modified by Floh1111 on 01.11.2009 oldenburg.freifunk.net
   //Prints a KML file that can be used with OpenStreetmap and the Modified freifunkmap.php
   /**
    * print a xml file to use with GoogleEarth
    */
/*   public function getgoogleearthkmlfile( ) {
   	header('Content-type: text/xml');
		$xw = new xmlWriter();
    $xw->openMemory();
   
    $xw->startDocument('1.0','UTF-8');
    $xw->startElement ('kml'); 
    $xw->writeAttribute( 'xmlns', 'http://earth.google.com/kml/2.1');
  
    $xw->startElement('Document');   
    $xw->writeElement ('name', '200903170407-200903170408');
   
  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'lineStyleCreated');
    $xw->startElement('PolyStyle');
      $xw->writeRaw('<color>0000ffff</color>');
    $xw->endElement();
    $xw->startElement('LineStyle');
      $xw->writeRaw('<color>cc00ffff</color>');
      $xw->writeRaw('<width>2</width>');
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'lineStyleModified');
    $xw->startElement('PolyStyle');
      $xw->writeRaw('<color>00ff0000</color>');
    $xw->endElement();
    $xw->startElement('LineStyle');
      $xw->writeRaw('<color>ccff0000</color>');
      $xw->writeRaw('<width>3</width>');
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'lineStyleDeleted');
    $xw->startElement('PolyStyle');
      $xw->writeRaw('<color>000000ff</color>');
    $xw->endElement();
    $xw->startElement('LineStyle');
      $xw->writeRaw('<color>cc0000ff</color>');
      $xw->writeRaw('<width>4</width>');
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'sh_ylw-pushpin');
    $xw->startElement('IconStyle');
      $xw->writeRaw('<scale>0.5</scale>');
      $xw->startElement('Icon');
	$xw->writeRaw('<href>http://freifunk-ol.de/netmon/templates/img/ffmap/node.png</href>');
      $xw->endElement();
      $xw->startElement('hotSpot');
	$xw->writeAttribute( 'x', '20');
	$xw->writeAttribute( 'y', '2');
	$xw->writeAttribute( 'xunits', 'pixels');
	$xw->writeAttribute( 'yunits', 'pixels');
      $xw->endElement();
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'sh_blue-pushpin');
    $xw->startElement('IconStyle');
      $xw->writeRaw('<scale>1.3</scale>');
      $xw->startElement('Icon');
	$xw->writeRaw('<href>http://maps.google.com/mapfiles/kml/pushpin/blue-pushpin.png</href>');
      $xw->endElement();
      $xw->startElement('hotSpot');
	$xw->writeAttribute( 'x', '20');
	$xw->writeAttribute( 'y', '2');
	$xw->writeAttribute( 'xunits', 'pixels');
	$xw->writeAttribute( 'yunits', 'pixels');
      $xw->endElement();
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('ListStyle');
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'sh_red-pushpin');
    $xw->startElement('IconStyle');
      $xw->writeRaw('<scale>1.3</scale>');
      $xw->startElement('Icon');
	$xw->writeRaw('<href>http://maps.google.com/mapfiles/kml/pushpin/red-pushpin.png</href>');
      $xw->endElement();
      $xw->startElement('hotSpot');
	$xw->writeAttribute( 'x', '20');
	$xw->writeAttribute( 'y', '2');
	$xw->writeAttribute( 'xunits', 'pixels');
	$xw->writeAttribute( 'yunits', 'pixels');
      $xw->endElement();
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('ListStyle');
  $xw->endElement();

  $xw->startElement('Folder');
    $xw->startElement('name');
      $xw->writeRaw('create');
    $xw->endElement();



	$db = new mysqlClass;
    	$result = $db->mysqlQuery("SELECT id FROM services WHERE typ='node' ORDER BY id");
      	while($row = mysql_fetch_assoc($result)) {
	  $serviceses[] = $row['id'];
    	}
	unset($db);

	foreach ($serviceses as $services) {
	  $db = new mysqlClass;
	  $result = $db->mysqlQuery("SELECT 
crawl_data.crawl_time, crawl_data.uptime, crawl_data.status, crawl_data.longitude, crawl_data.latitude,
services.id as service_id, services.title as services_title, services.typ, services.crawler,
nodes.user_id, nodes.node_ip, nodes.id as node_id, nodes.subnet_id,
subnets.subnet_ip, subnets.title,
users.nickname

FROM crawl_data

LEFT JOIN services ON (services.id = crawl_data.service_id)
LEFT JOIN nodes ON (nodes.id = services.node_id)
LEFT JOIN subnets ON (subnets.id = nodes.subnet_id)
LEFT JOIN users ON (users.id = nodes.user_id)

WHERE service_id='$services' ORDER BY crawl_data.id DESC LIMIT 1");
      	while($row = mysql_fetch_assoc($result)) {
	  $nodelist[] = $row;
    	}
	unset($db);
}
    
    foreach($nodelist as $entry) {
    $xw->startElement('Placemark');
      $xw->startElement('name');
	$xw->writeRaw("<![CDATA[Node <a href='http://freifunk-ol.de/netmon/index.php?get=node&id=$entry[id]'>$GLOBALS[net_prefix].$entry[subnet_ip].$entry[node_ip]</a>]]>");
      $xw->endElement();
      $xw->startElement('description');
	$box_inhalt = "Benutzer: <a href='http://www.freifunk-ol.de/netmon/index.php?get=user&id=$entry[user_id]'>$entry[nickname]</a><br>
			DHCP-Range: $entry[zone_start]-$entry[zone_end]<br>
			Letzter Crawl: $entry[crawl_time]<br><br><br>";

	$xw->writeRaw("<![CDATA[$box_inhalt]]>");
      $xw->endElement();
      $xw->startElement('styleUrl');
	$xw->writeRaw('#sh_ylw-pushpin');
      $xw->endElement();
      $xw->startElement('Point');
	$xw->startElement('coordinates');
	  $xw->writeRaw("$entry[longitude],$entry[latitude],0");
	$xw->endElement();
      $xw->endElement();
    $xw->endElement();
}

  $xw->endElement();

$xw->endDocument();




    print $xw->outputMemory(true);   





  }*/
   public function getgoogleearthkmlfile( ) {
   	header('Content-type: text/xml');
		$xw = new xmlWriter();
    $xw->openMemory();
   
    $xw->startDocument('1.0','UTF-8');
    $xw->startElement ('kml'); 
    $xw->writeAttribute( 'xmlns', 'http://earth.google.com/kml/2.1');
  
    $xw->startElement('Document');   
    $xw->writeElement ('name', '200903170407-200903170408');
   
  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'lineStyleCreated');
    $xw->startElement('PolyStyle');
      $xw->writeRaw('<color>0000ffff</color>');
    $xw->endElement();
    $xw->startElement('LineStyle');
      $xw->writeRaw('<color>cc00ffff</color>');
      $xw->writeRaw('<width>2</width>');
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'lineStyleModified');
    $xw->startElement('PolyStyle');
      $xw->writeRaw('<color>00ff0000</color>');
    $xw->endElement();
    $xw->startElement('LineStyle');
      $xw->writeRaw('<color>ccff0000</color>');
      $xw->writeRaw('<width>3</width>');
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'lineStyleDeleted');
    $xw->startElement('PolyStyle');
      $xw->writeRaw('<color>000000ff</color>');
    $xw->endElement();
    $xw->startElement('LineStyle');
      $xw->writeRaw('<color>cc0000ff</color>');
      $xw->writeRaw('<width>4</width>');
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'sh_ylw-pushpin');
    $xw->startElement('IconStyle');
      $xw->writeRaw('<scale>0.5</scale>');
      $xw->startElement('Icon');
	$xw->writeRaw('<href>http://freifunk-ol.de/netmon/templates/img/ffmap/node.png</href>');
      $xw->endElement();
      $xw->startElement('hotSpot');
	$xw->writeAttribute( 'x', '20');
	$xw->writeAttribute( 'y', '2');
	$xw->writeAttribute( 'xunits', 'pixels');
	$xw->writeAttribute( 'yunits', 'pixels');
      $xw->endElement();
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'sh_blue-pushpin');
    $xw->startElement('IconStyle');
      $xw->writeRaw('<scale>1.3</scale>');
      $xw->startElement('Icon');
	$xw->writeRaw('<href>http://maps.google.com/mapfiles/kml/pushpin/blue-pushpin.png</href>');
      $xw->endElement();
      $xw->startElement('hotSpot');
	$xw->writeAttribute( 'x', '20');
	$xw->writeAttribute( 'y', '2');
	$xw->writeAttribute( 'xunits', 'pixels');
	$xw->writeAttribute( 'yunits', 'pixels');
      $xw->endElement();
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('ListStyle');
  $xw->endElement();

  $xw->startElement('Style');
  $xw->writeAttribute( 'id', 'sh_red-pushpin');
    $xw->startElement('IconStyle');
      $xw->writeRaw('<scale>1.3</scale>');
      $xw->startElement('Icon');
	$xw->writeRaw('<href>http://maps.google.com/mapfiles/kml/pushpin/red-pushpin.png</href>');
      $xw->endElement();
      $xw->startElement('hotSpot');
	$xw->writeAttribute( 'x', '20');
	$xw->writeAttribute( 'y', '2');
	$xw->writeAttribute( 'xunits', 'pixels');
	$xw->writeAttribute( 'yunits', 'pixels');
      $xw->endElement();
    $xw->endElement();
  $xw->endElement();

  $xw->startElement('ListStyle');
  $xw->endElement();

  $xw->startElement('Folder');
    $xw->startElement('name');
      $xw->writeRaw('create');
    $xw->endElement();



	$db = new mysqlClass;
    	$result = $db->mysqlQuery("SELECT id FROM services WHERE typ='node' ORDER BY id");
      	while($row = mysql_fetch_assoc($result)) {
	  $serviceses[] = $row['id'];
    	}
	unset($db);

	foreach ($serviceses as $services) {
	  $db = new mysqlClass;
	  $result = $db->mysqlQuery("SELECT 
crawl_data.crawl_time, crawl_data.uptime, crawl_data.status, crawl_data.longitude, crawl_data.latitude, crawl_data.ssid,
services.id as service_id, services.node_id, services.title as service_title, services.description as service_description, services.typ, services.crawler, services.zone_start, services.zone_end, services.create_date as service_create_date,
nodes.user_id, nodes.node_ip, nodes.id as node_id, nodes.subnet_id,
subnets.subnet_ip, subnets.title,
users.nickname

FROM crawl_data

LEFT JOIN services ON (services.id = crawl_data.service_id)
LEFT JOIN nodes ON (nodes.id = services.node_id)
LEFT JOIN subnets ON (subnets.id = nodes.subnet_id)
LEFT JOIN users ON (users.id = nodes.user_id)

WHERE service_id='$services' ORDER BY crawl_data.id DESC LIMIT 1");
      	while($row = mysql_fetch_assoc($result)) {
	  $nodelist[] = $row;
    	}
	unset($db);
}
    
    foreach($nodelist as $entry) {
    $xw->startElement('Placemark');
      $xw->startElement('name');
	$xw->writeRaw("<![CDATA[Node <a href='http://freifunk-ol.de/netmon/index.php?get=service&service_id=$entry[service_id]'>$GLOBALS[net_prefix].$entry[subnet_ip].$entry[node_ip]</a>]]>");
      $xw->endElement();
      $xw->startElement('description');
      $entry['ips'] = $entry['zone_end']-$entry['zone_start']+1;
	$box_inhalt = "Benutzer: <a href='http://www.freifunk-ol.de/netmon/index.php?get=user&id=$entry[user_id]'>$entry[nickname]</a><br>
			DHCP-Range: $entry[zone_start]-$entry[zone_end] ($entry[ips] IP's, X davon belegt)<br>
			SSID: $entry[ssid]<br>
			Letzter Crawl: $entry[crawl_time]<br><br><br>";

	$xw->writeRaw("<![CDATA[$box_inhalt]]>");
      $xw->endElement();
      $xw->startElement('styleUrl');
	$xw->writeRaw('#sh_ylw-pushpin');
      $xw->endElement();
      $xw->startElement('Point');
	$xw->startElement('coordinates');
	  $xw->writeRaw("$entry[longitude],$entry[latitude],0");
	$xw->endElement();
      $xw->endElement();
    $xw->endElement();
}

  $xw->endElement();

$xw->endDocument();

    print $xw->outputMemory(true); 
  }
}
?>