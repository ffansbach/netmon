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
	
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/Ip.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterface.class.php');
	require_once(ROOT_DIR.'/lib/core/Router.class.php');
	require_once(ROOT_DIR.'/lib/core/subnetcalculator.class.php');
	
	$smarty->assign('message', Message::getMessage());
	
	if(isset($_POST['what']) AND $_POST['what'] == 'ip' AND $_POST['ipv']==6) {
		$ip = Ip::ipv6Expand($_POST['ip']);
		
		//first try to determine network of given address
		$network = Ip::ipv6NetworkFromAddr($ip, (int)$_POST['netmask']);
		$network = new Network(false, false, $network, (int)$_POST['netmask'], 6);
		if($network->fetch()) {
			//if network found, then try to add ip address
			$ip = new Ip(false, false, $network->getNetworkId(), $ip);
			if($ip->fetch()) {
				$networkinterface = new Networkinterface($ip->getInterfaceId());
				$networkinterface->fetch();
				
				$router = new Router($networkinterface->getRouterId());
				$router->fetch();
				
				$smarty->assign('object', "router");
				$smarty->assign('object_data', $router);
			}
		}
	} elseif(isset($_POST['what']) AND $_POST['what'] == 'ip' AND $_POST['ipv']==4) {
		//first try to determine network of given address
		$network = SubnetCalculator::getDqNet($_POST['ip'], (int)$_POST['netmask']);
		$network = new Network(false, false, $network, (int)$_POST['netmask'], 4);
		if($network->fetch()) {
			//if network found, then try to add ip address
			$ip = new Ip(false, false, $network->getNetworkId(), $_POST['ip']);
			if($ip->fetch()) {
				$networkinterface = new Networkinterface($ip->getInterfaceId());
				$networkinterface->fetch();
				
				$router = new Router($networkinterface->getRouterId());
				$router->fetch();
				
				$smarty->assign('object', "router");
				$smarty->assign('object_data', $router);
			}
		}
	} elseif(isset($_POST['what']) AND $_POST['what'] == 'mac_add') {

	}
	
	$smarty->display("header.tpl.php");
	$smarty->display("search.tpl.php");
	$smarty->display("footer.tpl.php");
?>