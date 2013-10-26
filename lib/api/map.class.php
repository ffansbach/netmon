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

require_once('./lib/core/router_old.class.php');
require_once('./lib/core/interfaces.class.php');
require_once('./lib/core/batmanadvanced.class.php');
require_once('./lib/core/crawling.class.php');

class ApiMap {
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
		$routers = Router_old::getRouters();
		$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		foreach($routers as $router) {
			$router_longitude = $router['longitude'];
			$router_latitude = $router['latitude'];

			$router_crawl = Router_old::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $router['id']);
			if(!empty($router_crawl['longitude']) AND !empty($router_crawl['latitude'])) {
				$router_longitude = $router_crawl['longitude'];
				$router_latitude = $router_crawl['latitude'];
			}

			if(!empty($router_longitude) AND !empty($router_latitude)) {
				$originators = BatmanAdvanced::getCrawlBatmanAdvOriginatorsByCrawlCycleId($last_endet_crawl_cycle['id'], $router['id']);
				//$originators = unserialize($originators['originators']);

				if(!empty($originators)) {
					foreach($originators as $originator) {
						$neighbour_router = Router_old::getRouterByMacAndCrawlCycleId($originator['originator'], $last_endet_crawl_cycle['id']);
						$neighbour_router_longitude = $neighbour_router['longitude'];
						$neighbour_router_latitude = $neighbour_router['latitude'];
						$neighbour_router = Router_old::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $neighbour_router['router_id']);
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

	public function batman_advanced_conn_nexthop() {
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
		$routers = Router_old::getRouters();
		$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
		foreach($routers as $router) {
			//set own position to the position saved fix in netmon
			$router_longitude = $router['longitude'];
			$router_latitude = $router['latitude'];

			//if the router has an position in it's actual crawl data, then prefer this position
			$router_crawl = Router_old::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $router['id']);
			if(!empty($router_crawl['longitude']) AND !empty($router_crawl['latitude'])) {
				$router_longitude = $router_crawl['longitude'];
				$router_latitude = $router_crawl['latitude'];
			}

			//if the own position is not empt, then look for neighbours
			if(!empty($router_longitude) AND !empty($router_latitude)) {
				//$originators = BatmanAdvanced::getCrawlBatmanAdvOriginatorsByCrawlCycleId($last_endet_crawl_cycle['id'], $router['id']);
				$originators = BatmanAdvanced::getCrawlBatmanAdvNexthopsByCrawlCycleId($last_endet_crawl_cycle['id'], $router['id']);

				//$originators = unserialize($originators['originators']);

				if(!empty($originators)) {
					foreach($originators as $originator) {
						$neighbour_router = Router_old::getRouterByMacAndCrawlCycleId($originator['nexthop'], $last_endet_crawl_cycle['id']);
						$neighbour_router_longitude = $neighbour_router['longitude'];
						$neighbour_router_latitude = $neighbour_router['latitude'];
						$neighbour_router = Router_old::getCrawlRouterByCrawlCycleId($last_endet_crawl_cycle['id'], $neighbour_router['router_id']);
						if(!empty($neighbour_router['longitude']) AND !empty($neighbour_router['latitude'])) {
							$neighbour_router_longitude = $neighbour_router['longitude'];
							$neighbour_router_latitude = $neighbour_router['latitude'];
						}

						//check if the position is not empty
						if(!empty($neighbour_router_longitude) AND !empty($neighbour_router_longitude) AND strlen($originator['nexthop'])==17) {
							$xw->startElement('Placemark');
							$xw->startElement('Style');
							$xw->startElement('LineStyle');
							$xw->startElement('color');
							if (Config::getConfigValueByName('routervpnif') == $originator['outgoing_interface']) {
                                                                $xw->writeRaw("20ff0000");
                                                        } else {
								if($originator['link_quality']>=0 AND $originator['link_quality']<105) {
									$xw->writeRaw("ff1e1eff");
								} elseif($originator['link_quality']>=105 AND $originator['link_quality']<130) {
									$xw->writeRaw("ff4949ff");
								} elseif($originator['link_quality']>=130 AND $originator['link_quality']<155) {
									$xw->writeRaw("ff6a6aff");
								} elseif($originator['link_quality']>=155 AND $originator['link_quality']<180) {
									$xw->writeRaw("ff53acff");
								} elseif($originator['link_quality']>=180 AND $originator['link_quality']<205) {
									$xw->writeRaw("ff79ebff");
								} elseif($originator['link_quality']>=205 AND $originator['link_quality']<230) {
									$xw->writeRaw("ff7cff79");
								} elseif($originator['link_quality']>=230) {
									$xw->writeRaw("ff0aff04");
								}
							}
							$xw->endElement();
							/*$xw->startElement('width');
							$xw->writeRaw("5");
							$xw->endElement();*/
							$xw->endElement();
							$xw->endElement();

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

	public function getRouters() {
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
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/green_button.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();

				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-1');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_0_traffic_1.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-2');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_0_traffic_2.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-3');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_0_traffic_3.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-4');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_0_traffic_4.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-5');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_0_traffic_5.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-6');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_0_traffic_6.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_blue-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/ip_highlighted.png</href>');
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
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/ip_online_highlighted_1.png</href>');
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
								$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/ip_offline.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_yellow-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
								$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/ip_unknown.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();

				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Folder');
					$xw->startElement('name');
						$xw->writeRaw('create');
					$xw->endElement();

					$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
					$crawl_routers = Router_old::getCrawlRoutersByCrawlCycleId($last_endet_crawl_cycle['id']);
					foreach($crawl_routers as $crawl_router) {
						$router_data = Router_old::getRouterInfo($crawl_router['router_id']);

						$crawl_interfaces = Interfaces::getInterfacesCrawlByCrawlCycle($last_endet_crawl_cycle['id'], $crawl_router['router_id']);
						$row['traffic'] = 0;
						$traffic = 0;
						foreach($crawl_interfaces as $interface) {
							$traffic = $traffic + $interface['traffic_rx_avg'] + $interface['traffic_tx_avg'];
						}
						$traffic = round($traffic/1024,2);
						
						$batman_adv_originators = BatmanAdvanced::getCrawlBatmanAdvNexthopsByCrawlCycleId($last_endet_crawl_cycle['id'], $crawl_router['router_id']);
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
							//Make B.A.T.M.A.N advanced informaions
							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("<![CDATA[Router <a href='./router.php?router_id=".$router_data['router_id']."'>".$router_data['hostname']."</a>]]>");
								$xw->endElement();
								$xw->startElement('description');
										$box_inhalt = "<b>Status:</b> $crawl_router[status]<br>";
										$box_inhalt .= "<b>Position:</b> <span style=\"color: green;\">lat: $latitude, lon: $longitude</span><br>";
										$box_inhalt .= "<b>Clients:</b> ".$crawl_router['client_count']."<br>";
										$box_inhalt .= "<b>Benutzer:</b> <a href='./user.php?user_id=$router_data[user_id]'>$router_data[nickname]</a><br>";
										if(!empty($location)) {
											   $box_inhalt .= "<b>Standortbeschreibung:</b> $location<br>";
										}
										$box_inhalt .= "<b>Letztes Update:</b> ".date("d.m.Y H:i", strtotime($crawl_router['crawl_date']))." Uhr <br><br>";
										$box_inhalt .= "<h3>Nachbarn</h3>";
										if(!empty($batman_adv_originators)) {
											$box_inhalt = "<table>";
											$box_inhalt .= 		"<thead>";
											$box_inhalt .= 			"<tr>";
											$box_inhalt .= 				"<th>Originator</th>";
											$box_inhalt .= 				"<th>Last Seen</th>";
											$box_inhalt .= 				"<th>Quality</th>";
											$box_inhalt .= 				"<th>Nexthop</th>";
											$box_inhalt .= 				"<th>Outgoing Interface</th>";
											$box_inhalt .= 			"</tr>";
											$box_inhalt .= 		"</thead>";
											$box_inhalt .= "<tbody>";
											foreach ($batman_adv_originators as $originators) {
												$box_inhalt .= '<tr style="background-color:';
												if($originators['link_quality'] >= 0 AND $originators['link_quality'] < 105) {
													$box_inhalt .= '#ff1e1e';
												} elseif($originators['link_quality'] >= 105 AND $originators['link_quality'] < 130) {
													$box_inhalt .= '#ff4949';
												} elseif($originators['link_quality'] >= 130 AND $originators['link_quality'] < 155) {
													$box_inhalt .= '#ff6a6a';
												} elseif($originators['link_quality'] >= 155 AND $originators['link_quality'] < 180) {
													$box_inhalt .= '#ffac53';
												} elseif($originators['link_quality'] >= 180 AND $originators['link_quality'] < 205) {
													$box_inhalt .= '#ffeb79';
												} elseif($originators['link_quality'] >= 205 AND $originators['link_quality'] < 230) {
													$box_inhalt .= '#79ff7c';
												} elseif($originators['link_quality'] >= 230) {
													$box_inhalt .= '#04ff0a';
												}
												$box_inhalt .= "\">";
												$box_inhalt .= "<td><a href=\"search.php?search_range=mac_addr&search_string=$originators[originator]\">$originators[originator]</a></td>";
												$box_inhalt .= "<td>$originators[last_seen]</td>";
												$box_inhalt .= "<td>$originators[link_quality]</td>";
												$box_inhalt .= "<td>$originators[nexthop]</td>";
												$box_inhalt .= "<td>$originators[outgoing_interface]</td>";
												$box_inhalt .= "</tr>";
											}
											$box_inhalt .= "</tbody>";
											$box_inhalt .= "</table>";
										} else {
											$box_inhalt .= '	<p>Keine Originators gefunden</p>';
										}
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
								$xw->endElement();
								$xw->startElement('styleUrl');
									if(isset($_GET['highlight_router_id']) AND $_GET['highlight_router_id']==$router_data['router_id']) {
										$xw->writeRaw('#sh_blue-pushpin');
									} elseif($crawl_router['status']=='online') {
										$xw->writeRaw('#sh_green-pushpin');
									} elseif($crawl_router['status']=='offline') {
										$xw->writeRaw('#sh_red-pushpin');
									} elseif($crawl_router['status']=='unknown') {
										$xw->writeRaw('#sh_yellow-pushpin');
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

	public function getRoutersTraffic() {
		header('Content-type: text/xml');
		$xw = new xmlWriter();
		$xw->openMemory();
		$xw->startDocument('1.0','UTF-8');
			$xw->startElement ('kml'); 
				$xw->writeAttribute( 'xmlns', 'http://earth.google.com/kml/2.1');

				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-1');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/traffic_1.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-2');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/traffic_2.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-3');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/traffic_3.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-4');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/traffic_4.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-5');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/traffic_5.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_green-pushpin-6');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>2.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/traffic_6.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();

				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Folder');
					$xw->startElement('name');
						$xw->writeRaw('create');
					$xw->endElement();

					$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
					$crawl_routers = Router_old::getCrawlRoutersByCrawlCycleId($last_endet_crawl_cycle['id']);
					foreach($crawl_routers as $crawl_router) {
						$router_data = Router_old::getRouterInfo($crawl_router['router_id']);

						$crawl_interfaces = Interfaces::getInterfacesCrawlByCrawlCycle($last_endet_crawl_cycle['id'], $crawl_router['router_id']);
						$row['traffic'] = 0;
						foreach($crawl_interfaces as $interface) {
							$traffic = $traffic + $interface['traffic_rx_avg'] + $interface['traffic_tx_avg'];
						}
						$traffic = round($traffic/1024,2);
						
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
							if($crawl_router['status']=='online') {
							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("<![CDATA[Router <a href='./router.php?router_id=".$router_data['router_id']."'>".$router_data['hostname']."</a>]]>");
								$xw->endElement();
								$xw->startElement('description');
										$box_inhalt = "Traffic";
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
								$xw->endElement();
								$xw->startElement('styleUrl');


if($traffic<40)
	$xw->writeRaw('#sh_green-pushpin-1');
elseif($traffic<80)
	$xw->writeRaw('#sh_green-pushpin-2');
elseif($traffic<120)
	$xw->writeRaw('#sh_green-pushpin-3');
elseif($traffic<160)
	$xw->writeRaw('#sh_green-pushpin-3');
elseif($traffic<200)
	$xw->writeRaw('#sh_green-pushpin-4');
elseif($traffic<240)
	$xw->writeRaw('#sh_green-pushpin-5');
else
	$xw->writeRaw('#sh_green-pushpin-6');

								$xw->endElement();
								$xw->startElement('Point');
									$xw->startElement('coordinates');
										$xw->writeRaw("$longitude,$latitude,0");
									$xw->endElement();
								$xw->endElement();
							$xw->endElement();
							}
						}
					}
				$xw->endElement();
			$xw->endElement();
		$xw->endDocument();
		
		print $xw->outputMemory(true);
		return true;
	}
	
	public function getRoutersClients() {
		header('Content-type: text/xml');
		$xw = new xmlWriter();
		$xw->openMemory();
		$xw->startDocument('1.0','UTF-8');
			$xw->startElement ('kml'); 
				$xw->writeAttribute( 'xmlns', 'http://earth.google.com/kml/2.1');

				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_client-pushpin-1');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>1.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_2_1.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_client-pushpin-2');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>1.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_2_2.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_client-pushpin-3');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>1.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_2_3.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_client-pushpin-4');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>1.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_2_4.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_client-pushpin-5');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>1.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_2_5.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_client-pushpin-6');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>1.0</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/clients_2_6.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_blue-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/ip_highlighted.png</href>');
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
							$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/ip_online_highlighted_1.png</href>');
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
								$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/ip_offline.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();
				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Style');
					$xw->writeAttribute( 'id', 'sh_yellow-pushpin');
					$xw->startElement('IconStyle');
						$xw->writeRaw('<scale>0.5</scale>');
						$xw->startElement('Icon');
								$xw->writeRaw('<href>./templates/'.$GLOBALS['template'].'/img/ffmap/ip_unknown.png</href>');
						$xw->endElement();
					$xw->endElement();
				$xw->endElement();

				$xw->startElement('ListStyle');
				$xw->endElement();
				$xw->startElement('Folder');
					$xw->startElement('name');
						$xw->writeRaw('create');
					$xw->endElement();

					$last_endet_crawl_cycle = Crawling::getLastEndedCrawlCycle();
					$crawl_routers = Router_old::getCrawlRoutersByCrawlCycleId($last_endet_crawl_cycle['id']);
					foreach($crawl_routers as $crawl_router) {
						$router_data = Router_old::getRouterInfo($crawl_router['router_id']);

						$crawl_interfaces = Interfaces::getInterfacesCrawlByCrawlCycle($last_endet_crawl_cycle['id'], $crawl_router['router_id']);
						$row['traffic'] = 0;
						foreach($crawl_interfaces as $interface) {
							$traffic = $traffic + $interface['traffic_rx_avg'] + $interface['traffic_tx_avg'];
						}
						$traffic = round($traffic/1024,2);
						
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
							$xw->startElement('Placemark');
								$xw->startElement('name');
									$xw->writeRaw("<![CDATA[Router <a href='./router.php?router_id=".$router_data['router_id']."'>".$router_data['hostname']."</a>]]>");
								$xw->endElement();
								$xw->startElement('description');
										$box_inhalt = "Clients";
										$xw->writeRaw("<![CDATA[$box_inhalt]]>");
								$xw->endElement();
								$xw->startElement('styleUrl');
if($crawl_router['client_count']==0)
	$xw->writeRaw('#sh_client-pushpin-0');
elseif($crawl_router['client_count']==1)
	$xw->writeRaw('#sh_client-pushpin-1');
elseif($crawl_router['client_count']==2)
	$xw->writeRaw('#sh_client-pushpin-2');
elseif($crawl_router['client_count']==3)
	$xw->writeRaw('#sh_client-pushpin-3');
elseif($crawl_router['client_count']==4)
	$xw->writeRaw('#sh_client-pushpin-4');
elseif($crawl_router['client_count']==5)
	$xw->writeRaw('#sh_client-pushpin-5');
elseif($crawl_router['client_count']>=6)
	$xw->writeRaw('#sh_client-pushpin-6');
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
