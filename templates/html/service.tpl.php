		<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
		<script type="text/javascript" src="./templates/js/OpenLayers.js"></script>
		<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
		<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
<!--		<script type="text/javascript" src="http://osm.cdauth.de/map/prototypes.js"></script>-->

<h1>Service vom Typ <i>{$service_data.typ}</i> auf der IP <a href="./ip.php?id={$service_data.ip_id}">{$net_prefix}.{$service_data.subnet_ip}.{$service_data.ip_ip}</a></h1>

<h2>Status Historie:</h2>

<div style="width: 800px; overflow: hidden;">
{foreach key=count item=history from=$crawl_history}
    {if $history.status=="online"}
		<div style="float:left; width: 20px; height: 50px; background-color: green; border-width: 1px; border-style:solid;">&nbsp;</div>
    {elseif $history.status=="offline"}
		<div style="float:left; width: 20px; height: 50px; background-color: red; border-width: 1px; border-style:solid;">&nbsp;</div>
    {/if}
{/foreach}
</div>
<br>

<div style="width: 100%; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 50%;">

{if !empty($service_data.title)}
  <h2>{$service_data.title}</h2>
{else}
	<h2>Service Grunddaten</h2>
{/if}
<b>Service Typ:</b> {$service_data.typ}<br>
<b>DHCP-bereich:</b> {if $service_data.zone_start==0 OR $service_data.zone_end==0}
						Kein DHCP-Bereich reserviert
						{else}
						{$service_data.zone_start} bis {$service_data.zone_end}
					{/if}<br>
<b>Crawl-Art:</b> {$service_data.crawler}<br>
<b>Benutzer:</b> <a href="./user.php?id={$service_data.user_id}">{$service_data.nickname}</a><br>
<b>Eingetragen am:</b> {$service_data.create_date}<br>

{if !empty($service_data.description)}
  <h2>Beschreibung</h2>
  <p>{$service_data.description}</p>
{/if}

<h2>Hardware Daten</h2>
<p>
	{if !empty($last_online_crawl)}
		<b>Email:</b> {$last_online_crawl.email}<br>
		<b>Hostname:</b> {$last_online_crawl.hostname}<br>
		<b>Prefix:</b> {$last_online_crawl.prefix}<br>
		<b>SSID:</b> {$last_online_crawl.ssid}<br>
		<b>Standort:</b> {$last_online_crawl.location}<br>
		<b>Luciname:</b> {$last_online_crawl.luciname}<br>
		<b>Luciversion:</b> {$last_online_crawl.luciversion}<br>
		<b>Distname:</b> {$last_online_crawl.distname}<br>
		<b>Distversion:</b> {$last_online_crawl.distversion}<br>
		<b>Chipset:</b> {$last_online_crawl.chipset}<br>
		<b>Cpu:</b> {$last_online_crawl.cpu}<br>
		<b>Memory:</b> {$last_online_crawl.memory_total} Kb<br>
		<b>Free memory:</b> {$current_crawl.memory_free} Kb<br>
		<b>Loadaverage:</b> {$current_crawl.loadavg}<br>
		<b>Processes:</b> {$current_crawl.processes}<br>
	{else}
		Keine Daten vorhanden
	{/if}
</p>

<h2>Benachbarte IP´s</h2>

{if !empty($current_crawl.olsrd_neighbors)}
	{foreach key=count item=olsrd_neighbors from=$current_crawl.olsrd_neighbors}
		{if $olsrd_neighbors.2HopNeighbors eq '0'}<span style="background-color: yellow;">{/if}{$olsrd_neighbors.IPaddress} {if $olsrd_neighbors.2HopNeighbors eq '0'}(Direkter Client)</span>{/if}<br>
	{/foreach}
{else}
	Keine benachbarten IP´s
{/if}


    </div>

    <div style="float:left; width: 50%;">

	<h2>Standort (Gelb markiert)</h2>
{if !empty($last_online_crawl.longitude) AND !empty($last_online_crawl.latitude)}
	<!--<small style="font-size: 9pt;">Map Data from <a href="http://openstreetmap.org">OpenStreetMap</a></small>-->

	<script type="text/javascript" src="./templates/js/OpenLayers.js"></script>
	<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
	<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
	<div id="map" style="height:300px; width:300px; border:solid 1px black;font-size:9pt;">
		<script type="text/javascript">
			var lon = {$last_online_crawl.longitude};
			var lat = {$last_online_crawl.latitude};
			var radius = 30
			var zoom = 15;

			{literal}
				/* Initialize Map */
				init();

				/* Controls for the small map */
				MiniMapControls();

				/* Zoom to the subnet's center */
				point = new OpenLayers.LonLat(lon, lat);
				point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
				map.setCenter(point, zoom);

				/* Create the Subnet Layer */
/*				SubnetLayer("Subnet", lon, lat, radius);*/
			{/literal}
	AddKmlLayer("offline Ips", "./api.php?class=apiMap&section=getgoogleearthkmlfile_offline&highlighted_service={$service_data.service_id}");
	AddKmlLayer("Verbindungen", "./api.php?class=apiMap&section=conn");
	AddKmlLayer("online Ips", "./api.php?class=apiMap&section=getgoogleearthkmlfile_online&highlighted_service={$service_data.service_id}");
		</script>
	</div>
{else}
<p>Keine Standortinformationen verfügbar</p>
{/if}

<h2>Grafische Historie</h2>

{if !empty($last_online_crawl)}
	<img src="./tmp/service_ping_history.png"><br>
	<img src="./tmp/loadaverage_history.png"><br>
	<img src="./tmp/memory_free_history.png">
{else}
	Keine Daten vorhanden
{/if}

{if $isOwner}
	<h2>Aktionen</h2>
		<p>
			<a href="./serviceeditor.php?section=edit&service_id={$service_data.service_id}">Service editieren</a><br>
		</p>
{/if}
</div>
  </div>
</div>