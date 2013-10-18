<?php
	/**
	 * This Script provides data for the freifunk.net api described in this blog post:
	 * http://blog.freifunk.net/2013/die-neue-freifunk-api-aufruf-zum-mitmachen
	 *
	 * Some data ist stored statically and need to be updated manually. Other data is fetched
	 * from Netmons XML REST API and is generated dynamically if the data changes in Netmon.
	 */
	
	//initialize variables used in the folowing script
	$api_key = "aisdufh3bhdskbsd8838v6hj9864dsrg";
	$mydocument = array();
	$doc = new DOMDocument();
	
	$mydocument['api'] = "0.1";

	
	//community name
	$doc->load('http://netmon.freifunk-ol.de/api/rest/config/community_name?api_key='.$api_key);
	$xpath = new DOMXPath($doc);	
	$mydocument['name'] = $xpath->evaluate('string(/netmon_response/config/value/text())');

	//url
	$mydocument['url'] = "http://freifunk-ol.de";
	
	//state
	//number of nodes
	$doc->load('http://netmon.freifunk-ol.de/api/rest/routerlist/?status=online&offset=0&limit=0');
	$xpath = new DOMXPath($doc);	
	$mydocument['state']['nodes'] = $xpath->evaluate('string(/netmon_response/routerlist/@total_count)');
	
	//community slogan
	$doc->load('http://netmon.freifunk-ol.de/api/rest/config/community_slogan?api_key='.$api_key);
	$xpath = new DOMXPath($doc);	
	$mydocument['state']['message'] = $xpath->evaluate('string(/netmon_response/config/value/text())');
	
	//timestamp
	$mydocument['state']['lastchange'] = time();
	
	//location
	$mydocument['location']['city'] = "Oldenburg";
	$mydocument['location']['address']['Name'] = "Mainframe";
	$mydocument['location']['address']['Street'] = "Raiffeisenstraße 27";
	$mydocument['location']['address']['Zipcode'] = "26122";
	
	//get lat and lon
	$doc->load('http://netmon.freifunk-ol.de/api/rest/config/community_location_latitude?api_key='.$api_key);
	$xpath = new DOMXPath($doc);	
	$mydocument['location']['lat'] = $xpath->evaluate('string(/netmon_response/config/value/text())');
	$doc->load('http://netmon.freifunk-ol.de/api/rest/config/community_location_longitude?api_key='.$api_key);
	$xpath = new DOMXPath($doc);	
	$mydocument['location']['lon'] = $xpath->evaluate('string(/netmon_response/config/value/text())');
	
	//contact
	$mydocument['contact']['email'] = "info@freifunk-ol.de";
	$mydocument['contact']['facebook'] = "https://www.facebook.com/FreifunkOL";
	$mydocument['contact']['irc'] = "irc://chat.freenode.net:6665/ffol";
	$mydocument['contact']['ml'] = "freifunk-ol@lists.nord-west.net";
	$mydocument['contact']['twitter'] = "https://twitter.com/ff_ol";
	
	//feeds
	//[0]
	$mydocument['feeds'][0]['name'] = "Freifunk Oldenburg Blog";
	$mydocument['feeds'][0]['category'] = "blog";
	$mydocument['feeds'][0]['type'] = "atom";
	$mydocument['feeds'][0]['url'] = "http://blog.freifunk-ol.de/feed/atom/";
	//[1]
	$mydocument['feeds'][1]['name'] = "Freifunk Oldenburg Wiki";
	$mydocument['feeds'][1]['category'] = "wiki";
	$mydocument['feeds'][1]['type'] = "atom";
	$mydocument['feeds'][1]['url'] = "http://wiki.freifunk-ol.de/index.php?title=Spezial:Letzte_%C3%84nderungen&feed=atom";
	
	//techdetails
	$mydocument['techDetails']['stoererhaftung'] = "Nutzung eines Gateways vom Förderverein Freie Netze e.V. in Berlin (Accessprovider) in Kombination mit dem Zapp-Script.";
	$mydocument['techDetails']['bootstrap'] = "Neuen Router mit der Freifunk Oldenburg Firmware flashen, ans Internet anschließen oder in der Nähe eines anderen Freifunkknotens aufstellen und dann in Netmon aus der Liste der neuen Router übernehmen.";
	$mydocument['techDetails']['firmware']['url'] = "http://wiki.freifunk-ol.de/w/Firmware";
	$mydocument['techDetails']['firmware']['name'] = "Freifunk Firmware Oldenburg";
	
	//networks
	//fetch all ipv4 networks and overwrite the standart api limit of 50 with -1 to get all networks
	$doc->load("https://netmon.freifunk-ol.de/api/rest/networklist?ipv=4?offset=0&limit=-1");
	$xpath = new DOMXPath($doc);
	$networklist = $xpath->query('/netmon_response/networklist/network');
	foreach($networklist as $key=>$network) {
		//parse node values of the nodes inside the router node
		$mydocument['techDetails']['networks']['ipv4'][$key]['netmask'] = $xpath->evaluate('string(./netmask/text())', $network);
		$mydocument['techDetails']['networks']['ipv4'][$key]['network'] = $xpath->evaluate('string(./ip/text())', $network);
	}
	
	//fetch all ipv4 networks and overwrite the standart api limit of 50 with -1 to get all networks
	$doc->load("https://netmon.freifunk-ol.de/api/rest/networklist?ipv=6?offset=0&limit=-1");
	$xpath = new DOMXPath($doc);
	$networklist = $xpath->query('/netmon_response/networklist/network');
	foreach($networklist as $key=>$network) {
		//parse node values of the nodes inside the router node
		$mydocument['techDetails']['networks']['ipv6'][$key]['prefixlength'] = $xpath->evaluate('string(./netmask/text())', $network);
		$mydocument['techDetails']['networks']['ipv6'][$key]['prefix'] = $xpath->evaluate('string(./ip/text())', $network);
	}
	
	//routing etc.
	$mydocument['techDetails']['routing'] = "B.A.T.M.A.N. advanced";
	$mydocument['techDetails']['updatemode'] = "manually (sysupgrade)";
	$mydocument['techDetails']['vpn'] = "fastd";

	echo json_encode($mydocument, JSON_PRETTY_PRINT);
?>