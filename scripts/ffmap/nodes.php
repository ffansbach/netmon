<?php
	/**
	 * This Script fetches routers (and later also links) from
	 * the Netmon XML REST API and converts the output to the 
	 * API format of ffmap using xpath.
	 */
	
	//initialize variables used in the folowing script
	$mydocument = array();
	$nodes = array();
	$datetime = new DateTime();
	$meta = array(
				'timestamp' =>  $datetime->format(DateTime::W3C)
				);
	$links = array();
	$node_originators = array();
	//$node_originators_fail = array();
	$total_count = 50;
	$doc = new DOMDocument();
	
	//fetch all routers from netmons api in steps of 50 routers per loop
	for($i=0; $i<$total_count; $i+=50) {
		//fetch the next 50 the routers
		$doc->load("http://netmon.freifunk-ol.de/api/rest/routerlist?offset=$i&limit=50");
		$xpath = new DOMXPath($doc);
		
		//fetch the total number of routers in the complete list
		//we need to loop through the list in steps of 50 till we reach this number
		$total_count = $xpath->evaluate('number(/netmon_response/routerlist/@total_count)');
		
		//get all nodes of type router from the xml and loop through them
		$routers = $xpath->query('/netmon_response/routerlist/router');
		foreach($routers as $router) {
			//parse node values of the nodes inside the router node
			$node_flags = array(
						'client' => false,
						'gateway' => false,
						'online' => (($xpath->evaluate('string(./statusdata/status/text())', $router)=='online') ? true : false));
			$node_geo = array(
						(float)$xpath->evaluate('string(./latitude/text())', $router),
						(float)$xpath->evaluate('string(./longitude/text())', $router));
			
			//get mac addresses
			$mac_addresses = $xpath->query('./networkinterfacelist/networkinterface/statusdata/mac_addr', $router);
			$node_macs = "";
			$prefix = "";
			foreach($mac_addresses as $mac_addr) {
				$mac_addr = $xpath->evaluate('string(./text())', $mac_addr);
				if(!empty($mac_addr)) {
					$node_macs .= $prefix.$mac_addr;
					$prefix = ", ";
				}
			}
			
			$node = array(
						'flags' => $node_flags,
						'geo' => $node_geo,
						'macs' => $node_macs,
						'name' => $xpath->evaluate('string(./hostname/text())', $router),
						'id' => $xpath->evaluate('string(./router_id/text())', $router));
			$node_index = array_push($nodes, $node)-1;
			
			//get clients
			$client_count = (int)$xpath->evaluate('number(./statusdata/client_count/text())', $router);
			for($j=0; $j<$client_count; $j++) {
				$client_flags = array(
								'client' => true,
								'gateway' => false,
								'online' => true);
				$client = array(
							'flags' => $client_flags,
							'geo' => null,
							'macs' => "",
							'name' => "",
							'id' => $node['id']."_client_".$j);
				$client_index = array_push($nodes, $client)-1;
				
				$client_link = array(
								'id' => $node['id']."-".$client['id'],
								'source' => $client_index,
								'quality' => 'TT',
								'target' => $node_index,
								'type' => 'client');
				$links[] = $client_link;
			}
			
			//get originators
			$originators = $xpath->query('./statusdata/originators/originator', $router);
			foreach($originators as $originator) {
				$outgoing_interface = trim($xpath->evaluate('string(./outgoing_interface/text())', $originator));
				$link_quality = trim($xpath->evaluate('string(./link_quality/text())', $originator));
				$nexthop = trim($xpath->evaluate('string(./nexthop/text())', $originator));
				$originator = trim($xpath->evaluate('string(./originator/text())', $originator));
				
				//put all direct neighbours into tmp originator array
				if($originator==$nexthop) {
					$node_originators[] = array(
											'node_id' => $node['id'],
											'node_index' => $node_index,
											'originator' => $originator,
											'quality' => $link_quality,
											'outgoing_interface' => $outgoing_interface);
				}
			}
		}
	}
	
	foreach($node_originators as $originator) {
		foreach($nodes as $key=>$node) {
			if(stripos($node['macs'], $originator['originator'])!==false) {
				$type = (stripos($originator['outgoing_interface'], 'VPN')!==false) ? "vpn" : "wlan";
				
				//TODO: mege quality from the second link and build one link only
				$links[] = array(
							'id' => $originator['node_id']."-".$node['id'],
							'source' => $originator['node_index'],
							'quality' => (($originator['quality']*(-1)+255)/10).", ".(($originator['quality']*(-1)+255)/10),
							'target' => $key,
							'type' => $type);
			}
		}
	}
	
	//put everything together in a big array
	$mydocument = array(
					'nodes' => $nodes,
					'meta' => $meta,
					'links' => $links,
					'originators' => $node_originators);
	
//	print_r($mydocument);
	//encode the array to json and output
	echo json_encode($mydocument, JSON_PRETTY_PRINT);
?>