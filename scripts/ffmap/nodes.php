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

	$doc = new DOMDocument();
	
	//fetch all routers from netmons api
	$doc->load("http://netmon.freifunk-ol.de/api/rest/routerlist?offset=0&limit=-1&status=online");
	$xpath = new DOMXPath($doc);
	
	//get all nodes of type router from the xml and loop through them
	$routers = $xpath->query('/netmon_response/routerlist/router');
	foreach($routers as $router) {
		//parse node values of the nodes inside the router node
		$node = array(
					'flags' => array('client' => false,
									 'gateway' => false,
									 'online' => (($xpath->evaluate('string(./statusdata/status/text())', $router)=='online') ? true : false)),
					'geo' => array((float)$xpath->evaluate('string(./latitude/text())', $router),
								   (float)$xpath->evaluate('string(./longitude/text())', $router)),
					'macs' => "",
					'name' => $xpath->evaluate('string(./hostname/text())', $router),
					'id' => $xpath->evaluate('string(./router_id/text())', $router));
		$node_index = array_push($nodes, $node)-1;
		
		//get clients
		$client_count = (int)$xpath->evaluate('number(./statusdata/client_count/text())', $router);
		for($j=0; $j<$client_count; $j++) {
			$client = array(
						'flags' => array('client' => true,
										 'gateway' => false,
										 'online' => true),
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
	}
	
	//get mac addresses
	$doc_networkinterfaces = new DOMDocument();
	$doc_networkinterfaces->load("http://netmon.freifunk-ol.de/api/rest/networkinterfacelist?offset=0&limit=-1");
	$xpath_interfaces = new DOMXPath($doc_networkinterfaces);
	$interfaces = $xpath_interfaces->query('/netmon_response/networkinterfacelist/networkinterface');
	foreach($interfaces as $interface) {
		$node_id = $xpath_interfaces->evaluate('string(./router_id/text())', $interface);
		foreach($nodes as $key=>$node) {
			if($node_id == $node['id']) {
				if($nodes[$key]['macs']!="")
					$nodes[$key]['macs'] .= ", ";
				$nodes[$key]['macs'] .= $xpath_interfaces->evaluate('string(./statusdata/mac_addr/text())', $interface);
				break;
			}
		}
	}
	
	//get originators
	$doc_originators = new DOMDocument();
	$doc_originators->load("http://netmon.freifunk-ol.de/api/rest/originator_status_list?offset=0&limit=-1");
	$xpath_originators = new DOMXPath($doc_originators);
	$originator_status_list = $xpath_originators->query('/netmon_response/originator_status_list/originator_status');
	foreach($originator_status_list as $originator_status) {
		$node_id = $outgoing_interface = trim($xpath_originators->evaluate('string(./router_id/text())', $originator_status));
		$outgoing_interface = trim($xpath_originators->evaluate('string(./outgoing_interface/text())', $originator_status));
		$link_quality = trim($xpath_originators->evaluate('string(./link_quality/text())', $originator_status));
		$nexthop = trim($xpath_originators->evaluate('string(./nexthop/text())', $originator_status));
		$originator = trim($xpath_originators->evaluate('string(./originator/text())', $originator_status));
		$type = (stripos($outgoing_interface, 'VPN')!==false) ? "vpn" : "wlan";

		//put all direct neighbours into tmp originator array
		if($originator==$nexthop) {
			$source = 0;
			$target = 0;
			foreach($nodes as $key=>$node) {
				if(stripos($node['macs'], $originator)!==false)
					$target = $key;
				elseif($node['id']==$node_id)
					$source = $key;
				
				if($source!=0 AND $target!=0) {
					//TODO: mege quality from the second link and build one link only
					$links[] = array(
								'id' => $nodes[$source]['id']."-".$nodes[$target]['id'],
								'source' => $source,
								'quality' => (($link_quality*(-1)+255)/10).", ".(($link_quality*(-1)+255)/10),
								'target' => $target,
								'type' => $type);
					break;
				}
			}
		}
	}
	
	//put everything together in a big array
	$mydocument = array(
					'nodes' => $nodes,
					'meta' => $meta,
					'links' => $links);
	
//	print_r($mydocument);
	//encode the array to json and output
//	echo "<pre>";
	echo json_encode($mydocument, JSON_PRETTY_PRINT);
?>