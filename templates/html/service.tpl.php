		<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
		<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
		<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
		<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
<!--		<script type="text/javascript" src="http://osm.cdauth.de/map/prototypes.js"></script>-->

<h1>Service vom Typ <i>{$service_data.typ}</i> auf der IP <a href="./ip.php?id={$service_data.ip_id}">{$net_prefix}.{$service_data.ip}</a></h1>

<h2>Status Historie:</h2>
{if $current_crawl.status=='online'}
<div class="notice">Dieser Service ist gerade online, alle Daten sind aktuell.</div>
{elseif $current_crawl.status=='offline'}
<div class="error">Dieser Service ist gerade offline, es werden die Daten des letzten online-Crawls gezeigt.</div>
{elseif $current_crawl.status=='unbekannt'}
<div class="unknown">Dieser Service wurde noch nicht gecrawlt, daher sind keine Invormationen verfügbar.</div>
{/if}

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
    <div style="float:left; width: 50%;">

{if !empty($service_data.title)}
  <h2>{$service_data.title}</h2>
{else}
	<h2>Service Grunddaten</h2>
{/if}
<b>Service IP:</b> <a href="./ip.php?id={$service_data.ip_id}">{$net_prefix}.{$service_data.ip}</a><br>
<b>Service Typ:</b> {$service_data.typ}<br>
<b>Crawl-Art:</b> {$service_data.crawler}<br>
<b>Letzter Crawl:</b> {$current_crawl.crawl_time}<br>
{if $current_crawl.status=='offline'}
<b>Letzter Online Crawl: </b>{$last_online_crawl.crawl_time}<br>
{/if}
<b>Benutzer:</b> <a href="./user.php?id={$service_data.user_id}">{$service_data.nickname}</a><br>
<b>Eingetragen am:</b> {$service_data.create_date}<br>

{if !empty($service_data.description)}
  <h2>Beschreibung</h2>
  <p>{$service_data.description}</p>
{/if}

<h2>Hardware Daten</h2>
<p>
	{if !empty($last_online_crawl.chipset)}
		<b>Chipset:</b> {$last_online_crawl.chipset}<br>
		<b>Cpu:</b> {$last_online_crawl.cpu}<br>
		<b>Memory:</b> {$last_online_crawl.memory_total} Kb<br>
	{else}
		Keine Daten vorhanden
	{/if}
</p>

<h2>Software</h2>
<p>
	{if !empty($last_online_crawl.luciname)}
		<b>Luciname:</b> {$last_online_crawl.luciname}<br>
		<b>Luciversion:</b> {$last_online_crawl.luciversion}<br>
		<b>Distname:</b> {$last_online_crawl.distname}<br>
		<b>Distversion:</b> {$last_online_crawl.distversion}<br>
	{else}
		Keine Daten vorhanden
	{/if}
</p>

<h2>Konfiguration</h2>
<p>
	{if !empty($last_online_crawl.luciname)}
		<b>Email:</b> {$last_online_crawl.email}<br>
		<b>Hostname:</b> {$last_online_crawl.hostname}<br>
		<b>Prefix:</b> {$last_online_crawl.prefix}<br>
		<b>SSID:</b> {$last_online_crawl.ssid}<br>
	{else}
		Keine Daten vorhanden
	{/if}
</p>

<h2>Status</h2>
<p>
	{if !empty($last_online_crawl.memory_total)}
		<b>Free memory:</b> {$current_crawl.memory_free}/{$last_online_crawl.memory_total} Kb<br>
		<b>Loadaverage:</b> {$current_crawl.loadavg}<br>
		<b>Processes:</b> {$current_crawl.processes}<br>
	{else}
		Keine Daten vorhanden
	{/if}
</p>

<h2>Benachbarte IP´s</h2>

{assign var="tmp" value="2 Hop Neighbors"}
{assign var="tmp2" value="IP address"}


{if !empty($current_crawl.olsrd_neighbors)}
	{foreach key=count item=olsrd_neighbors from=$current_crawl.olsrd_neighbors}
<!--		{if $olsrd_neighbors.$tmp eq '0'}<span style="background-color: yellow;">{/if}{if $olsrd_neighbors.netmon_ip_id}<a href="./ip.php?id={$olsrd_neighbors.netmon_ip_id}">{/if}{$olsrd_neighbors.$tmp2}{if $olsrd_neighbors.netmon_ip_id}</a>{/if} {if $olsrd_neighbors.$tmp eq '0'}(Direkter Client)</span>{/if}<br>-->
		{if $olsrd_neighbors.netmon_is_client==true}<span style="background-color: yellow;">{/if}{if $olsrd_neighbors.netmon_ip_id}<a href="./ip.php?id={$olsrd_neighbors.netmon_ip_id}">{/if}{$olsrd_neighbors.$tmp2}{if $olsrd_neighbors.netmon_ip_id}</a>{/if} {if $olsrd_neighbors.netmon_is_client==true}(Direkter Client)</span>{/if}<br>
	{/foreach}
{else}
	Keine benachbarten IP´s
{/if}


    </div>

    <div style="float:left; width: 50%;">

	<h2>Standort (Hellblau markiert)</h2>
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
				fullmap();

				/* Zoom to the subnet's center */
				point = new OpenLayers.LonLat(lon, lat);
				point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
				map.setCenter(point, zoom);

				/* Create the Subnet Layer */
/*				SubnetLayer("Subnet", lon, lat, radius);*/
			{/literal}
			AddKmlLayer("Verbindungen", "./api.php?class=apiMap&section=conn");
			AddKmlLayer("online and offline Nodes", "./api.php?class=apiMap&section=getOnlineAndOfflineServiceKML&highlighted_service={$service_data.service_id}");
		</script>
	</div>
	{if !empty($last_online_crawl.location)}
	<p><b>Standortbeschreibung:</b> {$last_online_crawl.location}<br></p>
	{/if}
{else}
<p>Keine Standortinformationen verfügbar</p>
{/if}

<h2>Grafische Historie</h2>

{if !empty($dont_show_indicator)}
	<p>
		{if empty($ping_exception)}
			<img src="./tmp/service_ping_history.png"><br>
		{else}
			Beim erstellen der Grafik <i>Ping History</i>ist ein Fehler aufgetreten.
		{/if}
	</p>
	<p>
		{if empty($loadaverage_exception)}
			<img src="./tmp/loadaverage_history.png"><br>
		{else}
			Beim erstellen der Grafik <i>Loadaverage History</i>ist ein Fehler aufgetreten.
		{/if}
	</p>
	<p>
		{if empty($memory_free_exception)}
			<img src="./tmp/memory_free_history.png">
		{else}
			Beim erstellen der Grafik <i>Memory Free History</i>ist ein Fehler aufgetreten.
		{/if}
	</p>
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