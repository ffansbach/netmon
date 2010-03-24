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

  require_once('./lib/classes/core/olsr.class.php');

class ApiMap {
	//Function modified by Floh1111 on 01.11.2009 oldenburg.freifunk.net
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
							$xw->writeRaw('<href>./templates/img/ffmap/ip.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_blue-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_highlighted.png</href>');
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
							/*if (is_array($crawl['neightbors'])) {
								foreach ($crawl['neightbors'] as $neightbor) {
									if ($neightbor['2HopNeightbors']) {
										$clients++;
									}
								}
							}*/
							$iplist[] = array_merge($crawl, $data, array('clients'=>$clients));
						}
					}
					foreach($iplist as $entry) {
						$entry['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($entry['crawl_time']));
						if (!empty($entry['longitude']) AND !empty($entry['latitude'])) {
							$xw->startElement('Placemark');
								$xw->startElement('name');
									if(!empty($entry['title']))
										$title =  "($entry[title])";
									else
										$title = "";
									$xw->writeRaw("<![CDATA[Ip <a href='./service.php?service_id=$entry[service_id]'>$GLOBALS[net_prefix].$entry[ip]</a> $title]]>");
								$xw->endElement();
								$xw->startElement('description');
									$entry['ips'] = $entry['zone_end']-$entry['zone_start']+1;
									$box_inhalt = "Benutzer: <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
												   DHCP-Range: $entry[zone_start]-$entry[zone_end] ($entry[ips] IP's)<br>
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
								$xw->writeRaw('<href>http://freifunk-ol.de/netmon/templates/img/ffmap/ip_offline.png</href>');
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
							$xw->writeRaw('<href>./templates/img/ffmap/ip_highlighted.png</href>');
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
								$iplist[] = array_merge($crawl, $data);
							}
						}
						
						foreach($iplist as $entry) {
							$entry['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($entry['crawl_time']));
							if (!empty($entry['longitude']) AND !empty($entry['latitude'])) {
								if(!empty($entry['title']))
									$title =  "($entry[title])";
								else
									$title = "";
								$xw->startElement('Placemark');
									$xw->startElement('name');
										$xw->writeRaw("<![CDATA[Ip <a href='./service.php?service_id=$entry[service_id]'>$GLOBALS[net_prefix].$entry[ip]</a>]]>");
									$xw->endElement();
									$xw->startElement('description');
										$box_inhalt = "Benutzer: <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
													   DHCP-Range: $entry[zone_start]-$entry[zone_end]<br>
													   <br><b>Dieser Ip ist offline</b><br>
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

						//Hole Alle Services vom Typ ip die Online sind
						$services = Helper::getServicesByType("node");
						foreach ($services as $key1=>$service) {
							$crawl_data = Helper::getCurrentCrawlDataByServiceId($service['service_id']);
							$olsr_data = Olsr::getOlsrCrawlDataByCrawlId($crawl_data['id']);
							$data = array_merge($crawl_data, $olsr_data);

							if ($data['status']=='online') {
								$data['olsrd_neighbors'] = unserialize($data['olsrd_neighbors']);
								foreach($data['olsrd_neighbors'] as $key2=>$neighbours) {
									//Hole die Service-ID der Nachbarips
									$tmp1 = 'IP address';
									$neighbourServiceIds = Helper::getServicesByTypeAndIpId('node', Helper::getIpIdByIp($neighbours[$tmp1]));
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

	public function getOnlineAndOfflineServiceKML() {
		$time1 = microtime();
		header('Content-type: text/xml');
		$xw = new xmlWriter();
		$xw->openMemory();
		$xw->startDocument('1.0','UTF-8');
			$xw->startElement ('kml'); 
				$xw->writeAttribute( 'xmlns', 'http://earth.google.com/kml/2.1');

				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_blue-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_highlighted.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_ip_highlighted_pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_highlighted_1.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_ip_offline_highlighted_pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_offline_hightlighted_1.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_red-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
								$xw->writeRaw('<href>./templates/img/ffmap/ip_offline.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();


				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Folder');
					$xw->startElement('name');
						$xw->writeRaw('create');
					$xw->endElement();

					$services = Helper::getServicesByType("node");

					foreach ($services as $service) {
						//Get current crawl data
						try {
							$sql = "SELECT *
								FROM crawl_data
							        WHERE service_id='$service[service_id]'
								ORDER BY id DESC LIMIT 1";
							$result = DB::getInstance()->query($sql);
							$current_crawl = $result->fetch(PDO::FETCH_ASSOC);
							if(empty($current_crawl['status']))
								$current_crawl['status'] = "unbekannt";
						}
						catch(PDOException $e) {
							echo $e->getMessage();
						}
						
						if ($current_crawl['status']=='online') {
							$iplist[] = array_merge($current_crawl, $service);
						} elseif ($current_crawl['status']=='offline') {
							try {
								$sql = "SELECT * FROM crawl_data
									WHERE service_id='$service[service_id]' AND status='online' ORDER BY id DESC LIMIT 1";
								$result = DB::getInstance()->query($sql);
								$last_online_crawl = $result->fetch(PDO::FETCH_ASSOC);
								$last_online_crawl['status'] = "offline";
							}
							catch(PDOException $e) {
								echo $e->getMessage();
							}
							
							$iplist[] = array_merge($last_online_crawl, $service);
						}
					}
	
					foreach($iplist as $entry) {
						$entry['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($entry['crawl_time']));
						if (!empty($entry['longitude']) AND !empty($entry['latitude'])) {
							if(!empty($entry['title']))
								$title =  "($entry[title])";
							else
								$title = "";
							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("<![CDATA[Ip <a href='./ip.php?id=$entry[ip_id]'>$GLOBALS[net_prefix].$entry[ip]</a>]]>");
								$xw->endElement();
								$xw->startElement('description');
									if ($entry['status']=='online') {
										$box_inhalt = "<b>Position:</b> <span style=\"color: green;\">lat: $entry[latitude], lon: $entry[longitude]</span><br>
												   <b>Benutzer:</b> <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
												   <b>DHCP-Range:</b> $GLOBALS[net_prefix].$entry[zone_start] bis $GLOBALS[net_prefix].$entry[zone_end] ($entry[ips] IP's)<br>
												   <b>SSID:</b> $entry[ssid]<br>
												   <b>Standortbeschreibung:</b> $entry[location]<br>
												   <b>Letzter Crawl:</b> $entry[crawl_time]<br>";
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
									} elseif ($entry['status']=='offline') {
										$box_inhalt = "<b>Position:</b> <span style=\"color: red;\">lat: $entry[latitude], lon: $entry[longitude]</span><br>
												   <b>Benutzer:</b> <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
												   <b>DHCP-Range:</b> $GLOBALS[net_prefix].$entry[zone_start] bis $GLOBALS[net_prefix].$entry[zone_end]<br>
												   <b>SSID:</b> $entry[ssid]<br>
												   <b>Standortbeschreibung:</b> $entry[location]<br>
													<b style=\"color: red;\">Diese Ip ist offline!</b><br>
												   <b>Letztes Mal online:</b> $entry[crawl_time]<br>";
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
									}
								$xw->endElement();
								$xw->startElement('styleUrl');
									if(isset($_GET['highlighted_service']) AND $_GET['highlighted_service']==$entry['service_id'])
										$xw->writeRaw('#sh_blue-pushpin');
									elseif(isset($_GET['highlighted_subnet']) AND $_GET['highlighted_subnet']==$entry['subnet_id'])
										$xw->writeRaw('#sh_ip_offline_highlighted_pushpin');
									else {
										if ($entry['status']=='online') {
											$xw->writeRaw('#sh_green-pushpin');
										} elseif ($entry['status']=='offline') {
											$xw->writeRaw('#sh_red-pushpin');
										}
									}
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

	public function getOnlineServiceKML() {
		header('Content-type: text/xml');
		$xw = new xmlWriter();
		$xw->openMemory();
		$xw->startDocument('1.0','UTF-8');
			$xw->startElement ('kml'); 
				$xw->writeAttribute( 'xmlns', 'http://earth.google.com/kml/2.1');

				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_blue-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_highlighted.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_ip_highlighted_pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_highlighted_1.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_ip_offline_highlighted_pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_offline_hightlighted_1.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_red-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
								$xw->writeRaw('<href>./templates/img/ffmap/ip_offline.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();


				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Folder');
					$xw->startElement('name');
						$xw->writeRaw('create');
					$xw->endElement();

					$services = Helper::getServicesByType("node");

					foreach ($services as $service) {
						//Get current crawl data
						try {
							$sql = "SELECT *
								FROM crawl_data
							        WHERE service_id='$service[service_id]'
								ORDER BY id DESC LIMIT 1";
							$result = DB::getInstance()->query($sql);
							$current_crawl = $result->fetch(PDO::FETCH_ASSOC);
							if(empty($current_crawl['status']))
								$current_crawl['status'] = "unbekannt";
						}
						catch(PDOException $e) {
							echo $e->getMessage();
						}
						
						if ($current_crawl['status']=='online') {
							$current_crawl['olsrd_links'] = Olsr::getCurrentlyEstablishedOLSRConnections($current_crawl['id']);
							$iplist[] = array_merge($current_crawl, $service);
						}
					}
	
					foreach($iplist as $entry) {
						$entry['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($entry['crawl_time']));
						if (!empty($entry['longitude']) AND !empty($entry['latitude'])) {
							if(!empty($entry['title']))
								$title =  "($entry[title])";
							else
								$title = "";
							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("<![CDATA[Ip <a href='./ip.php?id=$entry[ip_id]'>$GLOBALS[net_prefix].$entry[ip]</a>]]>");
								$xw->endElement();
								$xw->startElement('description');
									if ($entry['status']=='online') {
										unset($links);
										foreach ($entry['olsrd_links'] as $olsrd_link) {
											if ($olsrd_link['Cost']==0)
												$font_color = '#bb3333';
											elseif ($olsrd_link['Cost']<4)
												$font_color = '#00cc00';
											elseif ($olsrd_link['Cost']<10)
												$font_color = '#ffcb05';
											elseif ($olsrd_link['Cost']<100)
												$font_color = '#ff6600';

											$tmp = 'Remote IP';
											$links .= "<span style=\"color: $font_color;\">olsr zu ".$olsrd_link[$tmp]." (ETX: $olsrd_link[Cost])</span><br>";
										}
										$box_inhalt = "<b>Position:</b> <span style=\"color: green;\">lat: $entry[latitude], lon: $entry[longitude]</span><br>
												   <b>Benutzer:</b> <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
												   <b>DHCP-Range:</b> $GLOBALS[net_prefix].$entry[zone_start] bis $GLOBALS[net_prefix].$entry[zone_end] ($entry[ips] IP's)<br>
												   <b>SSID:</b> $entry[ssid]<br>
												   <b>Standortbeschreibung:</b> $entry[location]<br>
												   <b>Letzter Crawl:</b> $entry[crawl_time]<br><br>
												   <b>Verbindungen:</b><br>
												    $links
												    ";
												   
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
									}
								$xw->endElement();
								$xw->startElement('styleUrl');
									if(isset($_GET['highlighted_service']) AND $_GET['highlighted_service']==$entry['service_id'])
										$xw->writeRaw('#sh_blue-pushpin');
									elseif(isset($_GET['highlighted_subnet']) AND $_GET['highlighted_subnet']==$entry['subnet_id'])
										$xw->writeRaw('#sh_ip_offline_highlighted_pushpin');
									else {
										if ($entry['status']=='online') {
											$xw->writeRaw('#sh_green-pushpin');
										} elseif ($entry['status']=='offline') {
											$xw->writeRaw('#sh_red-pushpin');
										}
									}
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

	public function getOfflineServiceKML() {
		header('Content-type: text/xml');
		$xw = new xmlWriter();
		$xw->openMemory();
		$xw->startDocument('1.0','UTF-8');
			$xw->startElement ('kml'); 
				$xw->writeAttribute( 'xmlns', 'http://earth.google.com/kml/2.1');

				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_blue-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_highlighted.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_ip_highlighted_pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_highlighted_1.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_ip_offline_highlighted_pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_offline_hightlighted_1.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_red-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
								$xw->writeRaw('<href>./templates/img/ffmap/ip_offline.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();


				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Folder');
					$xw->startElement('name');
						$xw->writeRaw('create');
					$xw->endElement();

					$services = Helper::getServicesByType("node");

					foreach ($services as $service) {
						//Get current crawl data
						try {
							$sql = "SELECT *
								FROM crawl_data
							        WHERE service_id='$service[service_id]'
								ORDER BY id DESC LIMIT 1";
							$result = DB::getInstance()->query($sql);
							$current_crawl = $result->fetch(PDO::FETCH_ASSOC);
							if(empty($current_crawl['status']))
								$current_crawl['status'] = "unbekannt";
						}
						catch(PDOException $e) {
							echo $e->getMessage();
						}
						
						 if ($current_crawl['status']=='offline') {
							try {
								$sql = "SELECT * FROM crawl_data
									WHERE service_id='$service[service_id]' AND status='online' ORDER BY id DESC LIMIT 1";
								$result = DB::getInstance()->query($sql);
								$last_online_crawl = $result->fetch(PDO::FETCH_ASSOC);
								$last_online_crawl['status'] = "offline";
							}
							catch(PDOException $e) {
								echo $e->getMessage();
							}
							
							$iplist[] = array_merge($last_online_crawl, $service);
						}
					}
	
					foreach($iplist as $entry) {
						$entry['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($entry['crawl_time']));
						if (!empty($entry['longitude']) AND !empty($entry['latitude'])) {
							if(!empty($entry['title']))
								$title =  "($entry[title])";
							else
								$title = "";
							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("<![CDATA[Ip <a href='./ip.php?id=$entry[ip_id]'>$GLOBALS[net_prefix].$entry[ip]</a>]]>");
								$xw->endElement();
								$xw->startElement('description');
									if ($entry['status']=='online') {
										$box_inhalt = "<b>Position:</b> <span style=\"color: green;\">lat: $entry[latitude], lon: $entry[longitude]</span><br>
												   <b>Benutzer:</b> <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
												   <b>DHCP-Range:</b> $GLOBALS[net_prefix].$entry[zone_start] bis $GLOBALS[net_prefix].$entry[zone_end] ($entry[ips] IP's)<br>
												   <b>SSID:</b> $entry[ssid]<br>
												   <b>Standortbeschreibung:</b> $entry[location]<br>
												   <b>Letzter Crawl:</b> $entry[crawl_time]<br>";
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
									} elseif ($entry['status']=='offline') {
										$box_inhalt = "<b>Position:</b> <span style=\"color: red;\">lat: $entry[latitude], lon: $entry[longitude]</span><br>
												   <b>Benutzer:</b> <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
												   <b>DHCP-Range:</b> $GLOBALS[net_prefix].$entry[zone_start] bis $GLOBALS[net_prefix].$entry[zone_end]<br>
												   <b>SSID:</b> $entry[ssid]<br>
												   <b>Standortbeschreibung:</b> $entry[location]<br>
													<b style=\"color: red;\">Diese Ip ist offline!</b><br>
												   <b>Letztes Mal online:</b> $entry[crawl_time]<br>";
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
									}
								$xw->endElement();
								$xw->startElement('styleUrl');
									if(isset($_GET['highlighted_service']) AND $_GET['highlighted_service']==$entry['service_id'])
										$xw->writeRaw('#sh_blue-pushpin');
									elseif(isset($_GET['highlighted_subnet']) AND $_GET['highlighted_subnet']==$entry['subnet_id'])
										$xw->writeRaw('#sh_ip_offline_highlighted_pushpin');
									else {
										if ($entry['status']=='online') {
											$xw->writeRaw('#sh_green-pushpin');
										} elseif ($entry['status']=='offline') {
											$xw->writeRaw('#sh_red-pushpin');
										}
									}
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

	public function getSubnetPolygons() {
		try {
			$sql = "select polygons FROM subnets WHERE id='$_GET[subnet_id]';";
			$result = DB::getInstance()->query($sql);
			$poligons = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		echo $poligons['polygons'];

	}
}
?>