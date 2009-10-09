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

require_once('./config/runtime.inc.php');

class verfuegbarkeitsPalette extends ezcGraphPalette {
	protected $dataSetColor = array('#00b308', '#c10000', '#fff600');
	protected $dataSetSymbol = array(ezcGraph::BULLET);
	protected $fontName = 'sans-serif';
	protected $fontColor = '#555753';
}

$ip_status = Helper::countServiceStatusByType('node');
if ($ip_status['online']!=0 OR $ip_status['offline']!=0 OR $ip_status['unbekannt']!=0) {
	$graph = new ezcGraphPieChart();
	$graph->palette = new verfuegbarkeitsPalette();
	$graph->driver = new ezcGraphGdDriver(); 
	$graph->options->font = './templates/fonts/verdana.ttf';
	
	$graph->title = 'Ip Verfügbarkeit';
	
	$graph->data['Access statistics'] = new ezcGraphArrayDataSet( array(
		'Online' => $ip_status['online'],
		'Offline' => $ip_status['offline'],
		'Unbekannt' => $ip_status['unbekannt']
	) );

	$graph->data['Access statistics']->highlight['Online'] = true;
	$graph->render( 260, 200, './tmp/ip_status.png' );
	$smarty->assign('ip_status', true);
} else {
   $smarty->assign('ip_status', false);
}

$vpn_status = Helper::countServiceStatusByType('vpn');
if ($vpn_status['online']!=0 OR $vpn_status['offline']!=0 OR $vpn_status['unbekannt']!=0) {
	$graph = new ezcGraphPieChart();
	$graph->palette = new verfuegbarkeitsPalette();
	$graph->driver = new ezcGraphGdDriver(); 
	$graph->options->font = './templates/fonts/verdana.ttf';
	$graph->title = 'VPN Verfügbarkeit';
	
	$graph->data['Access statistics'] = new ezcGraphArrayDataSet( array(
		'Online' => $vpn_status['online'],
		'Offline' => $vpn_status['offline'],
		'Unbekannt' => $vpn_status['unbekannt']
	) );

	$graph->data['Access statistics']->highlight['Online'] = true;
	$graph->render( 260, 200, './tmp/vpn_status.png' );
	$smarty->assign('vpn_status', true);
} else {
	$smarty->assign('vpn_status', false);
}

$service_status = Helper::countServiceStatusByType('service');
if ($service_status['online']!=0 OR $service_status['offline']!=0 OR $service_status['unbekannt']!=0) {
	$graph = new ezcGraphPieChart();
	$graph->palette = new verfuegbarkeitsPalette();
	$graph->driver = new ezcGraphGdDriver(); 
	$graph->options->font = './templates/fonts/verdana.ttf';
	$graph->title = 'Service Verfügbarkeit';
	
	$graph->data['Access statistics'] = new ezcGraphArrayDataSet( array(
		'Online' => $service_status['online'],
		'Offline' => $service_status['offline'],
		'Unbekannt' => $service_status['unbekannt']
	) );

	$graph->data['Access statistics']->highlight['Online'] = true;
	$graph->render( 260, 200, './tmp/service_status.png' );
	$smarty->assign('service_status', true);
} else {
	$smarty->assign('service_status', false);
}



	require_once('./lib/classes/core/history.class.php');
	
	$history = History::getServiceHistory($countlimit, $GLOBALS['portal_history_hours']);
   $smarty->assign('portal_history_hours', $GLOBALS['portal_history_hours']);

	$smarty->assign('history', $history);

/*		try {
			$sql = "SELECT ip_id, zone_start, zone_end
			       FROM services";
			$result = DB::getInstance()->query($sql);
			
			foreach($result as $row) {
				DB::getInstance()->exec("UPDATE ips SET zone_start='$row[zone_start]', zone_end='$row[zone_end]' WHERE id='$row[ip_id]';");
				$services[] = $row;
			}
		}
		catch(PDOException $e) { 
			echo $e->getMessage(); 
		}
*/
$smarty->assign('message', message::getMessage());
$smarty->display("header.tpl.php");
$smarty->display("portal.tpl.php");
$smarty->display("footer.tpl.php");

?>