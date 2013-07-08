    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
		<title>{$community_name} | Netmon</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<link href="./templates/{$template}/css/central_netmon.css" rel="stylesheet" type="text/css"/>
	</head>
	<body style="background: #FFFFFF; text-align:left; width: 200px; max-width: 200px">
		<div style="border-style: solid; border-width: 1px; border-color: #99CCFF; background: #EBF5FF;">
			{if (!empty($router_data.latitude) AND !empty($router_data.longitude)) OR (!empty($router_last_crawl.latitude) AND !empty($router_last_crawl.longitude))}
				<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
				<script type="text/javascript" src="./templates/{$template}/js/OpenStreetMap.js"></script>
				<script type="text/javascript" src="./templates/{$template}/js/OsmFreifunkMap.js"></script>
				<div id="map" style="height:170px; width:198px;">
					<script type="text/javascript">
						{if !empty($router_last_crawl.latitude)}
							var lat = {$router_last_crawl.latitude};
							var lon = {$router_last_crawl.longitude};
						{else}
							var lat = {$router_data.latitude};
							var lon = {$router_data.longitude};
						{/if}
						
						var radius = 30
						var zoom = 17;
						
						/* Initialize Map */
						router_map_embed({$router_data.router_id});
					</script>
				</div>
				</p>
			{/if}
		
			<div style="padding: 5px">
		
			<h1 style="font-size: 13pt;">Router {$router_data.hostname}</h1>
			<h2 style="font-size: 9.5pt;">Was ist das?</h2>
			<p style="font-size: 8pt;">Ich bin <a href="https://de.wikipedia.org/wiki/Freies_Funknetz" target="_blank">Freifunker</a> und der Blaue Punkt ist
			mein Freifunkrouter.
			<a href="http://wiki.freifunk-ol.de/" target="_blank">Mach mit</a> und Verbinde dich per WLAN zum offenen Netzwerk <i>{$community_essid}</i>.

			<h2 style="font-size: 9.5pt;">Aktuelle Statusdaten</h2>
			<p style="font-size: 8pt;">
				<b>Status:</b> {if $router_status.status=="online"}
										<img width="13" src="./templates/{$template}/img/ffmap/status_up_small.png" alt="online">
									{elseif $router_status.status=="offline"}
										<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="offline">
									{elseif $router_status.status=="unknown"}
										<img src="./templates/{$template}/img/ffmap/status_unknown_small.png" title="unknown" alt="unknown">
									{/if}<br>
				<b>Verbundene Clients:</b> {$router_status.client_count}
			</p>
			
			<h2 style="font-size: 9.5pt;">Links</h2>
			<p style="font-size: 8pt;">
				<a href="./router_status.php?router_id={$router_data.router_id}" target="_blank">Vollständigen Status ansehen</a><br>
				<a href="./map.php" target="_blank">Alle Freifunkrouter ansehen</a><br>
				<a href="http://blog.freifunk-ol.de/2013/06/30/freifunkrouter-in-sidebar-der-eigenen-website-einbinden/" target="_blank">Eigenen Router in Website einbetten</a>
			</p>
			</div>
		</div>
	</body>
</html>