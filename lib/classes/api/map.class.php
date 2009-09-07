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
 * This file contains the class for the Map part of the Netmon API.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class apiMap {
	//Funktion modified by Floh1111 on 01.11.2009 oldenburg.freifunk.net
	//Prints a KML file that can be used with OpenStreetmap and the Modified freifunkmap.php
	/**
	* print a xml file to use with GoogleEarth
	*/
	public function getgoogleearthkmlfile_online() {
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
							$xw->writeRaw('<href>./templates/img/ffmap/node.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_blue-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/node_highlighted.png</href>');
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
						
					$services = Helper::getServicesByType("node");
					foreach ($services as $service) {
						$data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
						if ($data['status']=='online') {
							$crawl = $data;
							if(!is_array($crawl)) {
								$crawl = array();
							}
							$data = Helper::getServiceDataByServiceId($service['service_id']);
							$clients=0;
							if (is_array($crawl['neightbors'])) {
								foreach ($crawl['neightbors'] as $neightbor) {
									if ($neightbor['2HopNeightbors']) {
										$clients++;
									}
								}
							}
							$nodelist[] = array_merge($crawl, $data, array('clients'=>$clients));
						}
					}
					foreach($nodelist as $entry) {
						$entry['crawl_time'] = Helper::makeSmoothNodelistTime(strtotime($entry['crawl_time']));
						if (!empty($entry['longitude']) AND !empty($entry['latitude'])) {
							$xw->startElement('Placemark');
								$xw->startElement('name');
									if(!empty($entry['title']))
										$title =  "($entry[title])";
									else
										$title = "";
									$xw->writeRaw("<![CDATA[Node <a href='./service.php?service_id=$entry[service_id]'>$GLOBALS[net_prefix].$entry[subnet_ip].$entry[node_ip]</a> $title]]>");
								$xw->endElement();
								$xw->startElement('description');
									$entry['ips'] = $entry['zone_end']-$entry['zone_start']+1;
									$box_inhalt = "Benutzer: <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
												   DHCP-Range: $entry[zone_start]-$entry[zone_end] ($entry[ips] IP's, $entry[clients] davon belegt)<br>
												   SSID: $entry[ssid]<br>
												   Beschreibung: $entry[description]<br>
												   Letzter Crawl: $entry[crawl_time]<br>";
									$xw->writeRaw("<![CDATA[$box_inhalt]]>");
								$xw->endElement();
								$xw->startElement('styleUrl');
									if(isset($_GET['highlighted_service']) AND $_GET['highlighted_service']==$entry['service_id'])
										$xw->writeRaw('#sh_blue-pushpin');
									else
										$xw->writeRaw('#sh_ylw-pushpin');
								$xw->endElement();
								$xw->startElement('Point');
									$xw->startElement('coordinates');
										$xw->writeRaw("$entry[longitude],$entry[latitude],0");
									$xw->endElement();
								$xw->endElement();
							$xw->endElement();
						}
					}
				$xw->endElement();
			$xw->endElement();
		$xw->endDocument();
		
		print $xw->outputMemory(true);
		return true;
	}
	
	/**
	 * print a xml file to use with GoogleEarth
	 */
	public function getgoogleearthkmlfile_offline( ) {
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
								$xw->writeRaw('<href>http://freifunk-ol.de/netmon/templates/img/ffmap/node_offline.png</href>');
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
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/node_highlighted.png</href>');
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
						try {
							$sql = "SELECT id FROM services WHERE typ='node' ORDER BY id";
							$result = DB::getInstance()->query($sql);
							foreach($result as $row) {
								$services[] = $row['id'];
							}
						}
						catch(PDOException $e) {
							echo $e->getMessage();
						}
						foreach ($services as $service) {
							$data = Helper::getCurrentCrawlDataByServiceId($service);
							if ($data['status']=='offline') {
								$crawl = Helper::getLastOnlineCrawlDataByServiceId($service);
								if (!is_array($crawl)) {
									$crawl = array();
								} 
								$data = Helper::getServiceDataByServiceId($service);
								$nodelist[] = array_merge($crawl, $data);
							}
						}
						
						foreach($nodelist as $entry) {
							$entry['crawl_time'] = Helper::makeSmoothNodelistTime(strtotime($entry['crawl_time']));
							if (!empty($entry['longitude']) AND !empty($entry['latitude'])) {
								if(!empty($entry['title']))
									$title =  "($entry[title])";
								else
									$title = "";
								$xw->startElement('Placemark');
									$xw->startElement('name');
										$xw->writeRaw("<![CDATA[Node <a href='./service.php?service_id=$entry[service_id]'>$GLOBALS[net_prefix].$entry[subnet_ip].$entry[node_ip]</a>]]>");
									$xw->endElement();
									$xw->startElement('description');
										$entry['ips'] = $entry['zone_end']-$entry['zone_start']+1;
										$box_inhalt = "Benutzer: <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
													   DHCP-Range: $entry[zone_start]-$entry[zone_end]<br>
													   <br><b>Dieser Node ist offline</b><br>
													   Letztes mal online: $entry[crawl_time]<br>";
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
									$xw->endElement();
									$xw->startElement('styleUrl');
									if(isset($_GET['highlighted_service']) AND $_GET['highlighted_service']==$entry['service_id'])
										$xw->writeRaw('#sh_blue-pushpin');
									else
										$xw->writeRaw('#sh_ylw-pushpin');
									$xw->endElement();
									$xw->startElement('Point');
										$xw->startElement('coordinates');
											$xw->writeRaw("$entry[longitude],$entry[latitude],0");
										$xw->endElement();
									$xw->endElement();
								$xw->endElement();
							}
						}
					$xw->endElement();
				$xw->endElement();
			$xw->endElement();
		$xw->endDocument();

		print $xw->outputMemory(true);
		return true;
	}

	public function conn() {
		header('Content-type: text/xml');
		$xw = new xmlWriter();
		$xw->openMemory();
		$xw->startDocument('1.0','UTF-8');
			$xw->startElement ('kml'); 
				$xw->writeAttribute( 'xmlns', 'http://earth.google.com/kml/2.1');
				$xw->startElement('Document');   
					$xw->writeElement ('name', '200903170407-200903170408');
					$xw->startElement('Folder');
						$xw->startElement('name');
							$xw->writeRaw('create');
						$xw->endElement();

						//Hole Alle Services vom Typ node die Online sind
						$services = Helper::getServicesByType("node");
						foreach ($services as $key1=>$service) {
							$data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
							if ($data['status']=='online') {
								foreach($data['olsrd_neighbors'] as $key2=>$neighbours) {
									//Hole die Service-ID der Nachbarnodes
									$neighbourServiceIds = Helper::getServicesByTypeAndNodeId('node', Helper::getNodeIdByIp($neighbours['IPaddress']));
									foreach($neighbourServiceIds as $key=>$neighbourServiceId) {
										$neighbourServiceCrawlData = Helper::getCurrentCrawlDataByServiceId($neighbourServiceId['service_id']);
										if(!empty($neighbourServiceCrawlData['longitude']) AND !empty($neighbourServiceCrawlData['latitude'])) {
											$linedata[$key1.$key2.$key]['my_service_id'] = $service['service_id'];
											$linedata[$key1.$key2.$key]['my_lon'] = $data['longitude'];
											$linedata[$key1.$key2.$key]['my_lat'] = $data['latitude'];
											
											$linedata[$key1.$key2.$key]['neighbour_service_id'] = $neighbourServiceId['service_id'];
											$linedata[$key1.$key2.$key]['neighbour_lon'] = $neighbourServiceCrawlData['longitude'];
											$linedata[$key1.$key2.$key]['neighbour_lat'] = $neighbourServiceCrawlData['latitude'];
										}
									}
								}
							}
						}
						
						foreach($linedata as $line) {
							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("myname");
								$xw->endElement();
								$xw->startElement('Polygon');
									$xw->startElement('outerBoundaryIs');
										$xw->startElement('LinearRing');
											$xw->startElement('coordinates');
												$xw->writeRaw("$line[my_lon],$line[my_lat],0
															    $line[neighbour_lon],$line[neighbour_lat],0");
											$xw->endElement();
										$xw->endElement();
									$xw->endElement();
								$xw->endElement();
							$xw->endElement();
						}
					$xw->endElement();
				$xw->endElement();
			$xw->endElement();
		$xw->endDocument();

		print $xw->outputMemory(true);
		return true;
	}
}
?>