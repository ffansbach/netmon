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

require_once('./lib/classes/core/router.class.php');
require_once('./lib/classes/core/interfaces.class.php');
require_once('./lib/classes/core/batmanadvanced.class.php');
require_once('./lib/classes/core/olsr.class.php');
require_once('./lib/classes/core/crawling.class.php');


  require_once('./lib/classes/core/olsr.class.php');

class ApiMap {
	//Function modified by Floh1111 on 01.11.2009 oldenburg.freifunk.net
	//Prints a KML file that can be used with OpenStreetmap and the Modified freifunkmap.php
	/**
	* print a xml file to use with GoogleEarth
	*/

	public function olsr_conn() {
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
						$routers = Router::getRouters();
						$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
						foreach($routers as $router) {
							if(!empty($router['latitude']) AND !empty($router['longitude'])) {
								$originators = Olsr::getCrawlOlsrDataByCrawlCycleId($last_endet_crawl_cycle['id'], $router['id']);
								$olsr_entrys = unserialize($originators['olsrd_links']);
								if(!empty($olsr_entrys)) {
									$router = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $router['id']);
									foreach($olsr_entrys as $olsr_entry) {
										$tmp1 = 'Remote IP';
										$neighbour_router = Router::getRouterByIPv4Addr($olsr_entry[$tmp1]);
										$neighbour_router = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $neighbour_router['router_id']);
										if(!empty($neighbour_router['longitude']) AND !empty($neighbour_router['longitude'])) {
											$xw->startElement('Placemark');
												$xw->startElement('name');
													$xw->writeRaw("myname");
												$xw->endElement();
												$xw->startElement('Polygon');
													$xw->startElement('outerBoundaryIs');
														$xw->startElement('LinearRing');
															$xw->startElement('coordinates');
																$xw->writeRaw("$router[longitude],$router[latitude],0
																			    $neighbour_router[longitude],$neighbour_router[latitude],0");
															$xw->endElement();
														$xw->endElement();
													$xw->endElement();
												$xw->endElement();
											$xw->endElement();
										}
									}
								}
							}
						}
					$xw->endElement();
				$xw->endElement();
			$xw->endElement();
		$xw->endDocument();

		print $xw->outputMemory(true);
		return true;
	}

	public function batman_advanced_conn() {
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
						$routers = Router::getRouters();
						$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
						foreach($routers as $router) {
							$router_longitude = $router['longitude'];
							$router_latitude = $router['latitude'];

							$router_crawl = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $router['id']);
							if(!empty($router_crawl['longitude']) AND !empty($router_crawl['latitude'])) {
								$router_longitude = $router_crawl['longitude'];
								$router_latitude = $router_crawl['latitude'];
							}

							if(!empty($router_longitude) AND !empty($router_latitude)) {
								$originators = BatmanAdvanced::getCrawlBatmanAdvOriginatorsByCrawlCycleId($last_endet_crawl_cycle['id'], $router['id']);
								$originators = unserialize($originators['originators']);

								if(!empty($originators)) {
									foreach($originators as $originator) {
										$neighbour_router = Router::getRouterByMacAndCrawlCycleId($originator['originator'], $last_endet_crawl_cycle['id']);
										$neighbour_router_longitude = $neighbour_router['longitude'];
										$neighbour_router_latitude = $neighbour_router['latitude'];
										$neighbour_router = Router::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $neighbour_router['router_id']);
										if(!empty($neighbour_router['longitude']) AND !empty($neighbour_router['latitude'])) {
											$neighbour_router_longitude = $neighbour_router['longitude'];
											$neighbour_router_latitude = $neighbour_router['latitude'];
										}

										if(!empty($neighbour_router)) {
											$xw->startElement('Placemark');
												$xw->startElement('name');
													$xw->writeRaw("myname");
												$xw->endElement();
												$xw->startElement('Polygon');
													$xw->startElement('outerBoundaryIs');
														$xw->startElement('LinearRing');
															$xw->startElement('coordinates');
																$xw->writeRaw("$router[longitude],$router[latitude],0
																			    $neighbour_router_longitude,$neighbour_router_latitude,0");
															$xw->endElement();
														$xw->endElement();
													$xw->endElement();
												$xw->endElement();
											$xw->endElement();
										}
									}
								}
							}
						}
					$xw->endElement();
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
					$xw->writeAttribute( 'id', 'sh_ip_online_highlighted_pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_online_highlighted_1.png</href>');
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

					$iplist = array();
					foreach ($services as $key => $service) {
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
							$iplist[$key]['crawl_data'] = $current_crawl;
							$iplist[$key]['service_data'] = $service;
							$iplist[$key]['ip_data'] = Helper::getIpInfo($service['ip_id']);
						}
					}
	

					foreach($iplist as $entry) {
						$entry['crawl_data']['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($entry['crawl_data']['crawl_time']));
						if (!empty($entry['ip_data']['longitude']) AND !empty($entry['ip_data']['latitude'])) {
							if(!empty($entry['service_data']['title']))
								$title =  "($entry[service_data][title])";
							else
								$title = "";

							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("<![CDATA[Ip <a href='./ip.php?id=".$entry['service_data']['ip_id']."'>".$GLOBALS['net_prefix'].".".$entry['ip_data']['ip']."</a>]]>");
								$xw->endElement();
								$xw->startElement('description');
										unset($links);
										foreach ($entry['crawl_data']['olsrd_links'] as $olsrd_link) {
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

										$box_inhalt = "<b>Position:</b> <span style=\"color: green;\">lat: ".$entry['ip_data']['latitude'].", lon: ".$entry['ip_data']['longitude']."</span><br>
												   <b>Benutzer:</b> <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
												   <b>DHCP-Range:</b> ".$GLOBALS['net_prefix'].".".$entry['service_data']['zone_start']." bis ".$GLOBALS['net_prefix'].".".$entry['service_data']['zone_end']." (".$entry['service_data']['ips']." IP's)<br>
												   <b>SSID:</b> ".$entry['crawl_data']['ssid']."<br>
												   <b>Standortbeschreibung:</b> ".$entry['crawl_data']['location']."<br>
												   <b>Letzter Crawl:</b> ".$entry['crawl_data']['crawl_time']."<br><br>
												   <b>Olsr Verbindungen:</b><br>
												    ".$links."
												    ";
												   
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
								$xw->endElement();
								$xw->startElement('styleUrl');
									if(isset($_GET['highlighted_service']) AND $_GET['highlighted_service']==$entry['service_data']['service_id'])
										$xw->writeRaw('#sh_blue-pushpin');
									elseif(isset($_GET['highlighted_subnet']) AND $_GET['highlighted_subnet']==$entry['ip_data']['subnet_id'])
										$xw->writeRaw('#sh_ip_online_highlighted_pushpin');
									else {
										$xw->writeRaw('#sh_green-pushpin');
									}
								$xw->endElement();
								$xw->startElement('Point');
									$xw->startElement('coordinates');
										$xw->writeRaw($entry['ip_data']['longitude'].",".$entry['ip_data']['latitude'].",0");
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

					foreach ($services as $key => $service) {
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
							
							$iplist[$key]['crawl_data'] = $current_crawl;
							$iplist[$key]['service_data'] = $service;
							$iplist[$key]['ip_data'] = Helper::getIpInfo($service['ip_id']);
						}
					}
	

					foreach($iplist as $entry) {
						$entry['crawl_data']['crawl_time'] = Helper::makeSmoothIplistTime(strtotime($entry['crawl_data']['crawl_time']));
						if ((!empty($entry['ip_data']['longitude']) AND !empty($entry['ip_data']['latitude']))) {
							if(!empty($entry['service_data']['title']))
								$title =  "($entry[service_data][title])";
							else
								$title = "";

							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("<![CDATA[Router <a href='./ip.php?id=".$entry['service_data']['ip_id']."'>".$GLOBALS['net_prefix'].".".$entry['ip_data']['ip']."</a>]]>");
								$xw->endElement();
								$xw->startElement('description');
									$box_inhalt = "<b>Position:</b> <span style=\"color: green;\">lat: ".$entry['ip_data']['longitude'].", lon: ".$entry['ip_data']['latitude']."</span><br>
											   <b>Benutzer:</b> <a href='./user.php?id=$entry[user_id]'>$entry[nickname]</a><br>
											   <b>DHCP-Range:</b> ".$GLOBALS['net_prefix'].".".$entry['service_data']['zone_start']." bis ".$GLOBALS['net_prefix'].".".$entry['service_data']['zone_end']." (".$entry['service_data']['ips']." IP's)<br>
											   <b>SSID:</b> ".$entry['crawl_data']['ssid']."<br>
											   <b>Standortbeschreibung:</b> ".$entry['crawl_data']['location']."<br>
												<b style=\"color: red;\">Dieser Router ist offline!</b><br>
											   <b>Letztes Mal online:</b> ".$entry['crawl_data']['crawl_time']."<br>";

									$xw->writeRaw("<![CDATA[$box_inhalt]]>");
								$xw->endElement();
								$xw->startElement('styleUrl');
									if(isset($_GET['highlighted_service']) AND $_GET['highlighted_service']==$entry['service_data']['service_id'])
										$xw->writeRaw('#sh_blue-pushpin');
									elseif(isset($_GET['highlighted_subnet']) AND $_GET['highlighted_subnet']==$entry['ip_data']['subnet_id'])
										$xw->writeRaw('#sh_ip_offline_highlighted_pushpin');
									else {
										$xw->writeRaw('#sh_red-pushpin');
									}
								$xw->endElement();
								$xw->startElement('Point');
									$xw->startElement('coordinates');
										$xw->writeRaw($entry['ip_data']['longitude'].",".$entry['ip_data']['latitude'].",0");
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

	public function getOnlineRouters() {
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
					$xw->writeAttribute( 'id', 'sh_ip_online_highlighted_pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/img/ffmap/ip_online_highlighted_1.png</href>');
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

$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
$crawl_routers = Router::getCrawlRoutersByCrawlCycleIdAndStatus($last_endet_crawl_cycle['id'], 'online');

foreach($crawl_routers as $crawl_router) {
	$router_data = Router::getRouterInfo($crawl_router['router_id']);

	//Make coordinates and location information
	if(!empty($crawl_router['longitude']) AND !empty($crawl_router['latitude'])) {
		$longitude = $crawl_router['longitude'];
		$latitude = $crawl_router['latitude'];
		$location = $crawl_router['location'];
		$do = true;
	} elseif(!empty($router_data['longitude']) AND !empty($router_data['latitude'])) {
		$longitude = $router_data['longitude'];
		$latitude = $router_data['latitude'];
		$location = $router_data['location'];
		$do = true;
	} else {
		$do = false;
	}
	
	if($do) {


	//Make Olsr Informations
/*										unset($links);
										foreach ($entry['crawl_data']['olsrd_links'] as $olsrd_link) {
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
										}*/


	//Make B.A.T.M.A.N advanced informaions



	
							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("<![CDATA[Router <a href='./router_status.php?router_id=".$router_data['router_id']."'>".$router_data['hostname']."</a>]]>");
								$xw->endElement();
								$xw->startElement('description');
										$box_inhalt = "<b>Position:</b> <span style=\"color: green;\">lat: $latitude, lon: $longitude</span><br>
												   <b>Benutzer:</b> <a href='./user.php?id=$router_data[user_id]'>$router_data[nickname]</a><br>
												   <b>Standortbeschreibung:</b> $location<br>
												   <b>Letzter Crawl:</b> ".$crawl_router['crawl_date']."<br><br>";
												   
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
								$xw->endElement();
								$xw->startElement('styleUrl');
									if(isset($_GET['highlighted_router']) AND $_GET['highlighted_router']==$router_data['router_id'])
										$xw->writeRaw('#sh_blue-pushpin');
									else {
										$xw->writeRaw('#sh_green-pushpin');
									}
								$xw->endElement();
								$xw->startElement('Point');
									$xw->startElement('coordinates');
										$xw->writeRaw("$longitude,$latitude,0");
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
}
?>