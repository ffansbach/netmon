		<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
		<script type="text/javascript" src="./templates/js/OpenLayers.js"></script>
		<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
		<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
<!--		<script type="text/javascript" src="http://osm.cdauth.de/map/prototypes.js"></script>-->

<h1>Service vom Typ <i>{$service_data.typ}</i> auf der IP <a href="./node.php?id={$service_data.node_id}">{$net_prefix}.{$service_data.subnet_ip}.{$service_data.node_ip}</a></h1>

<div style="width: 100%; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 50%;">

{if !empty($service_data.title)}
  <h2>{$service_data.title}</h2>
{/if}
<b>Service Typ:</b> {$service_data.typ}<br>
<b>DHCP-bereich:</b> {$service_data.zone_start} bis {$service_data.zone_end}<br>
<b>Crawl-Art:</b> {$service_data.crawler}<br>
<b>Benutzer:</b> <a href="./user.php?id={$service_data.user_id}">{$service_data.nickname}</a><br>
<b>Eingetragen am:</b> {$service_data.create_date}<br>

{if !empty($service_data.description)}
  <h2>Beschreibung</h2>
  <p>{$service_data.description}</p>
{/if}

<h2>Daten</h2>
<p>
email: {$last_online_crawl.email}<br>
hostname: {$last_online_crawl.hostname}<br>
prefix: {$last_online_crawl.prefix}<br>
ssid: {$last_online_crawl.ssid}<br>
location: {$last_online_crawl.location}<br>
luciname: {$last_online_crawl.luciname}<br>
luciversion: {$last_online_crawl.luciversion}<br>
distname: {$last_online_crawl.distname}<br>
distversion: {$last_online_crawl.distversion}<br>
chipset: {$last_online_crawl.chipset}<br>
cpu: {$last_online_crawl.cpu}<br>
memory: {$last_online_crawl.memory_total} Kb<br>
free memory: {$current_crawl.memory_free} Kb<br>
loadaverage: {$current_crawl.loadavg}<br>
processes: {$current_crawl.processes}<br>
</p>

<h2>Nachbarn</h2>

{foreach key=count item=olsrd_neighbors from=$current_crawl.olsrd_neighbors}
  {$olsrd_neighbors.IPaddress} {if $olsrd_neighbors.2HopNeighbors eq '0'}(Direkter Client){/if}<br>
{/foreach}




<h2>Aktueller Status</h2>

<div id="nodeitem" style="width: 345px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 100px;"><b>Status</b></div>
    <div style="float:left; width: 95px;"><b>Uptime</b></div>
    <div style="float:left; width: 150px;"><b>Stand</b></div>
  </div>
</div>

<div id="nodeitem" style="width: 345px; overflow: hidden;">
  <div style="white-space: nowrap;">
    {if $current_crawl.status=="online"}
      <div style="float:left; width: 100px;; background-color: green;">{$current_crawl.status}</div>
    {elseif $current_crawl.status=="offline"}
      <div style="float:left; width: 100px; background-color: red;">{$current_crawl.status}</div>
    {elseif $current_crawl.status=="ping"}
      <div style="float:left; width: 100px; background-color: #00c5cc;">{$current_crawl.status}</div>
    {/if}
    <div style="float:left; width: 95px;">{$current_crawl.uptime}</div>
    <div style="float:left; width: 150px;">{$current_crawl.crawl_time} Uhr</div>
  </div>
</div>

<h2>Historie</h2>
<div id="nodeitem" style="width: 345px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 100px;"><b>Status</b></div>
    <div style="float:left; width: 95px;"><b>Uptime</b></div>
    <div style="float:left; width: 150px;"><b>Stand</b></div>
  </div>
</div>

{foreach key=count item=history from=$crawl_history}
<div id="nodeitem" style="width: 345px; overflow: hidden;">
  <div style="white-space: nowrap;">
    {if $history.status=="online"}
      <div style="float:left; width: 100px;; background-color: green;">{$history.status}</div>
    {elseif $history.status=="offline"}
      <div style="float:left; width: 100px; background-color: red;">{$history.status}</div>
    {elseif $history.status=="ping"}
      <div style="float:left; width: 100px; background-color: #00c5cc;">{$history.status}</div>
    {/if}
    <div style="float:left; width: 95px;">{$history.uptime}</div>
    <div style="float:left; width: 150px;">{$history.crawl_time} Uhr</div>
  </div>
</div>
{/foreach}

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
	AddKmlLayer("offline Nodes", "./api.php?class=apiMap&section=getgoogleearthkmlfile_offline&highlighted_service={$service_data.service_id}");
	AddKmlLayer("Verbindungen", "./api.php?class=apiMap&section=conn");
	AddKmlLayer("online Nodes", "./api.php?class=apiMap&section=getgoogleearthkmlfile_online&highlighted_service={$service_data.service_id}");
		</script>
	</div>
{else}
<p>Keine Standortinformationen verfügbar</p>
{/if}

<h2>Aktionen</h2>

<p>
  <a href="./serviceeditor.php?section=edit&service_id={$service_data.service_id}">Service editieren</a><br>
</p>

<img src="./tmp/service_ping_history.png"><br>
<img src="./tmp/loadaverage_history.png"><br>
<img src="./tmp/memory_free_history.png">





    </div>



  </div>
</div>