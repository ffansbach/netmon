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
 * This file contains a class with many helpfull methods used for editing.
 *
 * @author	Clemens John <clemens-john@gmx.de>
 * @version	0.1
 * @package	Netmon Freifunk Netzverwaltung und Monitoring Software
 */

class editingHelper {
	public function getAFreeIP($subnet_id, $zone_start=false, $zone_end=false) {
		//Alle irgendwie im Subnet existierende IP's holen
		$existingips = editingHelper::getExistingIps($subnet_id);
		
		//Für den Node bestimmte Range den Existierenden IP's hinzufügen
		if ($zone_start AND $zone_end) {
			for ($i=$zone_start; $i<=$zone_end; $i++) {
				array_push($existingips, $i);
			}
		}
		
		//Erste freie IP nehmen
		for ($i=1; ($i<=245 AND !isset($available_node)); $i++) {
			if(!in_array($i, $existingips)) {
				$available_node = $i;
			}
		}
		
		if (isset($available_node)) {
			return $available_node;
		} else {
			return false;
		}
	}
	
	public function getExistingSubnets() {
		$subnets = array();
		try {
			$sql = "select subnet_ip FROM subnets ORDER BY subnet_ip ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$subnets[] = $row['subnet_ip'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $subnets;
	}

	public function getExistingSubnetsWithID() {
		try {
			$sql = "select id, subnet_ip FROM subnets ORDER BY subnet_ip ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$subnets[] = array('id'=>$row['id'], 'subnet_ip'=>$row['subnet_ip']);
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $subnets;
	}
	
	public function getFreeSubnets() {
		$subnets = editingHelper::getExistingSubnets();
		for ($i=0; $i<=255; $i++) {
			if(!in_array($i, $subnets)) {
				$available_subnets[] = $i;
			}
		}
		return $available_subnets;
	}
	
	public function getExistingNodes($subnet_id) {
		$nodes = array();
		try {
			$sql = "SELECT node_ip FROM nodes  WHERE subnet_id='$subnet_id' ORDER BY node_ip ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$nodes[$row['node_ip']] = $row['node_ip'];
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $nodes;
	}

	public function getExistingNodesWithID($subnet_id) {
		$nodes = array();
		try {
			$sql = "SELECT id, node_ip FROM nodes WHERE subnet_id='$subnet_id' ORDER BY node_ip ASC";
			$result = DB::getInstance()->query($sql);
			foreach($result as $row) {
				$nodes[$row['node_ip']] = array('node_ip'=>$row['node_ip'], 'id'=>$row['id']);
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		return $nodes;
	}
	
	public function getExistingRanges($subnet_id) {
		$services = Helper::getServiceseBySubnetId($subnet_id);
		$zones = array();
		foreach ($services as $service) {
			for ($i=$service['zone_start']; $i<=$service['zone_end']; $i++) {
				$zones[$i] = $i;
			}
		}
		return $zones;
	}

	public function getExistingIps($subnet_id) {
		return array_merge(editingHelper::getExistingNodes($subnet_id), editingHelper::getExistingRanges($subnet_id));
	}
	
	public function getFreeIpZone($subnet_id, $range, $node) {
		//Anzahl der zu reservierenden IP's
		$range = $range-1;
		$used_zones = array();
		$zones = array();
		
		$existing_ips = editingHelper::getExistingIps($subnet_id);
		
		//Array aller nicht mit Zonen oder Nodes belegter IPs erstellen
		for ($i=1; $i<=255; $i++) {
			if (!in_array($i, $existing_ips) AND $i!=$node) {
				$zonestrahl[] = $i;	
			}
		}
		
		//Nach freier Zone suchen
		$stop = false;
		for ($i=0; ($i<(count($zonestrahl)-$range) AND !$stop); $i++ ) {
			//Hol den ersten freien Raum der irgendwie erreichbar ist
			if ($range==($zonestrahl[$i+$range]-$zonestrahl[$i])) {
				$zone_start_first = $zonestrahl[$i];
				$zone_end_first = $zonestrahl[$i+$range];
				$stop = true;
			}
		}
		
		$stop = false;
		for ($i=0; ($i<(count($zonestrahl)-$range) AND !$stop); $i++ ) {
			//Hol einen freien Raum der genau zwischen zwei andere belegte Räume passt
			if (($range==($zonestrahl[$i+$range]-$zonestrahl[$i])) AND ($zonestrahl[$i-1]!=($zonestrahl[$i]-1)) AND (($zonestrahl[$i+$range+1])!=$zonestrahl[$i]+$range+1)) {
				$zone_start_between = $zonestrahl[$i];
				$zone_end_between = $zonestrahl[$i+$range];
				$stop = true;
			}
		}
		
		if (isset($zone_start_between) AND isset($zone_end_between)) {
			$zone_start = $zone_start_between;
			$zone_end = $zone_end_between;
			$return = true;
		} elseif (isset($zone_start_first) AND isset($zone_end_first)) {
			$zone_start = $zone_start_first;
			$zone_end = $zone_end_first;
			$return = true;
		} else {
			$return = false;
		}
		
		if ($range > 0) {
			return array('return'=>$return, 'start'=>$zone_start, 'end'=>$zone_end);
		} else {
			//NULL Gibt Probleme beim ändern wenn Range vorher auch NULL ist! (Clemens)
			return array('return'=>$return, 'start'=>"NULL", 'end'=>"NULL");
		}
	}
	
	public function addNodeTyp($node_id, $title, $description, $typ, $crawler, $port, $zone_start, $zone_end, $radius=80, $visible) {
		if (!empty($port)) {
			$crawler = $port;
		}
		
		DB::getInstance()->exec("INSERT INTO services (node_id, title, description, typ, crawler, zone_start, zone_end, radius, visible, create_date) VALUES ('$node_id', '$title', '$description', '$typ', '$crawler', '$zone_start', '$zone_end', '$radius', '$visible', NOW());");
		$service_id = DB::getInstance()->lastInsertId();
		
		try {
			$sql = "select nodes.node_ip, subnets.subnet_ip FROM nodes LEFT JOIN subnets ON (subnets.id=nodes.subnet_id) WHERE nodes.id='$node_id'";
			$result = DB::getInstance()->query($sql);
			$node_data = $result->fetch(PDO::FETCH_ASSOC);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		$message[] = array("Ein Nodetyp  vom Typ ".$typ." wurde dem Node mit der IP $GLOBALS[net_prefix].$node_data[subnet_ip].$node_data[node_ip] hinzugefügt.",1);
		message::setMessage($message);
		
		return array("result"=>true, "service_id"=>$service_id);
	}
}

?>