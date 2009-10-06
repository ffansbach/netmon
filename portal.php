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
$graph = new ezcGraphPieChart();
$graph->palette = new verfuegbarkeitsPalette();
$graph->driver = new ezcGraphGdDriver(); 
$graph->options->font = './templates/fonts/verdana.ttf';

 $graph->title = 'Aktuelle Ip-Verfügbarkeit';

 $graph->data['Access statistics'] = new ezcGraphArrayDataSet( array(
 'Online' => $ip_status['online'],
 'Offline' => $ip_status['offline'],
 'Unbekannt' => $ip_status['unbekannt']
) );
 $graph->data['Access statistics']->highlight['Online'] = true;

 $graph->render( 300, 200, './tmp/ip_status.png' );
//-----------
$vpn_status = Helper::countServiceStatusByType('vpn');

 $graph = new ezcGraphPieChart();
$graph->palette = new verfuegbarkeitsPalette();
$graph->driver = new ezcGraphGdDriver(); 
$graph->options->font = './templates/fonts/verdana.ttf';
 $graph->title = 'Aktuelle VPN-Verfügbarkeit';

 $graph->data['Access statistics'] = new ezcGraphArrayDataSet( array(
 'Online' => $vpn_status['online'],
 'Offline' => $vpn_status['offline'],
 'Unbekannt' => $vpn_status['unbekannt']
) );
 $graph->data['Access statistics']->highlight['Online'] = true;

 $graph->render( 300, 200, './tmp/vpn_status.png' );
//------------------
$service_status = Helper::countServiceStatusByType('service');

 $graph = new ezcGraphPieChart();
$graph->palette = new verfuegbarkeitsPalette();
$graph->driver = new ezcGraphGdDriver(); 
$graph->options->font = './templates/fonts/verdana.ttf';
 $graph->title = 'Aktuelle Service-Verfügbarkeit';

 $graph->data['Access statistics'] = new ezcGraphArrayDataSet( array(
 'Online' => $service_status['online'],
 'Offline' => $service_status['offline'],
 'Unbekannt' => $service_status['unbekannt']
) );
 $graph->data['Access statistics']->highlight['Online'] = true;
 $graph->render( 300, 200, './tmp/service_status.png' );

	require_once('./lib/classes/core/history.class.php');
	
	$history = History::getNetworkHistory($countlimit, 5);
	$smarty->assign('history', $history);

$smarty->assign('message', message::getMessage());
$smarty->display("header.tpl.php");
$smarty->display("portal.tpl.php");
$smarty->display("footer.tpl.php");

?>