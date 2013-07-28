<?php
	/**
	 * This Script provides data for the freifunk.net api described in this blog post:
	 * http://blog.freifunk.net/2013/die-neue-freifunk-api-aufruf-zum-mitmachen
	 *
	 * Some data ist stored statically and need to be updated manually. Other data is fetched
	 * from Netmons XML REST API and is generated dynamically if the data changes in Netmon.
	 */
	
	//initialize variables used in the folowing script
	$api_key = "netmon api key from user with root permission (120)";
	$mydocument = array();
	$doc = new DOMDocument();
	
	$mydocument['api'] = "0.1";
	$mydocument['state']['lastchange'] = time();
	//get community name
	$doc->load('http://netmon.freifunk-ol.de/api/rest/config/community_name?api_key='.$api_key);
	$xpath = new DOMXPath($doc);	
	$mydocument['name'] = $xpath->evaluate('string(/netmon_response/config/value/text())');
	
	$mydocument['url'] = "http://freifunk-ol.de";
	$mydocument['location']['city'] = "Oldenburg";
	
	//get lat and lon
	$doc->load('http://netmon.freifunk-ol.de/api/rest/config/community_location_latitude?api_key='.$api_key);
	$xpath = new DOMXPath($doc);	
	$mydocument['location']['lat'] = $xpath->evaluate('string(/netmon_response/config/value/text())');
	$doc->load('http://netmon.freifunk-ol.de/api/rest/config/community_location_longitude?api_key='.$api_key);
	$xpath = new DOMXPath($doc);	
	$mydocument['location']['lon'] = $xpath->evaluate('string(/netmon_response/config/value/text())');
	
	$mydocument['contact']['email'] = "fragen@freifunk-ol.de";
	
	//TODO: Write Network API module to be able to fetch network information to provide information about the networks we use
	//TODO: Fetch number of active nodes from Router module of the Netmon API: http://wiki.freifunk-ol.de/w/Netmon/API#Routerlist
	
	echo json_encode($mydocument, JSON_PRETTY_PRINT);
?>