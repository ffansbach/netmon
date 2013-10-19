<?php
	/**
	 * This script fetches dns zones and theit ressource reccords from the
	 * Netmon API and generates zone files for the nameserver Bind.
	 */
	
	//path where the generated zone files should be placed on the host system
	$zonedir_path = "/var/named/";
	$netmon_url = "http://netmon.freifunk-ol.de";
	
	//This script can only be called by the server
	if(!empty($_SERVER['REMOTE_ADDR'])) {
		die("This script can only be run by the server directly.");
	}
	
	$doc = new DOMDocument();
	//fetch all dns zones from netmons api
	$doc->load($netmon_url."/api/rest/dns_zone_list?offset=0&limit=-1");
	$xpath = new DOMXPath($doc);
	
	//get all nodes of type dns_zone from the xml and loop through them
	$dns_zone_list = $xpath->query('/netmon_response/dns_zone_list/dns_zone');
	foreach($dns_zone_list as $dns_zone) {
		$output = "";
		$output .= '$TTL	'.$xpath->evaluate('string(./ttl/text())', $dns_zone)."\n";
		$output .= '@	IN	SOA	'.$xpath->evaluate('string(./pri_dns/text())', $dns_zone)."	hostmaster.domain.tdl. (\n";
		$output .= "	".$xpath->evaluate('string(./serial/text())', $dns_zone)."; Serial\n";
		$output .= "	".$xpath->evaluate('string(./refresh/text())', $dns_zone)."; Refresh\n";
		$output .= "	".$xpath->evaluate('string(./retry/text())', $dns_zone)."; Retry\n";
		$output .= "	".$xpath->evaluate('string(./expire/text())', $dns_zone)."; Expire\n";
		$output .= "	".$xpath->evaluate('string(./ttl/text())', $dns_zone)."; Negative Cache TTL\n";
		$output .= ")\n\n";
		
		$output .= "		NS	".$xpath->evaluate('string(./pri_dns/text())', $dns_zone)."\n";
		$output .= "		NS	".$xpath->evaluate('string(./sec_dns/text())', $dns_zone)."\n";
		
		$dns_zone_id = $xpath->evaluate('string(./dns_zone_id/text())', $dns_zone);
		$doc2 = new DOMDocument();
		//fetch all dns ressource records for this zone from netmons api
		$doc2->load($netmon_url."/api/rest/dns_zone/$dns_zone_id/dns_ressource_record_list?offset=0&limit=-1");
		$xpath2 = new DOMXPath($doc2);
		//get all nodes of type dns_ressource_record from the xml and loop through them
		$dns_ressource_record_list = $xpath2->query('/netmon_response/dns_ressource_record_list/dns_ressource_record');
		foreach($dns_ressource_record_list as $dns_ressource_record) {
			$type = $xpath2->evaluate('string(./type/text())', $dns_ressource_record);
			if($type == 'A' OR $type == 'AAAA') {
				$output .= $xpath2->evaluate('string(./host/text())', $dns_ressource_record);
				$output .= "		".$type;
				$ip_id = $xpath2->evaluate('string(./destination_id/text())', $dns_ressource_record);
				
				$doc3 = new DOMDocument();
				//fetch the ip that was given in destination_id
				$doc3->load($netmon_url."/api/rest/ip/$ip_id");
				$xpath3 = new DOMXPath($doc3);
				$output .= "	".$xpath3->evaluate('string(/netmon_response/ip/ip/text())')."\n";
			}
		}
		
		//write the generated file to harddrive
		$zone_name = $xpath->evaluate('string(./name/text())', $dns_zone);
		$zone_file_path = $zonedir_path.$zone_name;
		$datei = fopen($zone_file_path, "w+");
		fwrite($datei, $output);
		fclose($datei);
	}
?>