<script type="text/javascript">
	document.body.id='tab1';
</script>
<ul id="tabnav">
	<li class="tab1"><a href="./router_status.php?router_id={$router_data.router_id}">Router Status</a></li>
	<li class="tab2"><a href="./router_config.php?router_id={$router_data.router_id}">Router Konfiguration</a></li>
</ul>


<h1>Daten des Routers {$router_data.hostname}</h1>

<div style="width: 100%; overflow: hidden;">
    <div style="float:left; width: 50%;">
		<h2>Grunddaten</h2>
		<b>Benutzer:</b> <a href="./user.php?id={$router_data.user_id}">{$router_data.nickname}</a><br>
		<b>Angelegt am:</b> {$router_data.create_date|date_format:"%e.%m.%Y %H:%M"} Uhr<br>

		<h2>System Monitoring</h2>
		<h3><u>Allgemein</u></h3>
			<b>Status:</b> 
    {if $router_last_crawl.status=="online"}
      <img src="./templates/img/ffmap/status_up_small.png" alt="online">
    {elseif $router_last_crawl.status=="offline"}
      <img src="./templates/img/ffmap/status_down_small.png" alt="offline">
    {/if}
<br>
			<b>Datenquelle:</b> {if $router_data.crawl_method=='router'}Router sendet Daten{elseif $router_data.crawl_method=='crawler'}Netmon Crawler{/if}<br>
			<b>Letzter Crawl:</b> {$router_last_crawl.crawl_date|date_format:"%e.%m.%Y %H:%M"}<br>
			<b>Crawl Intervall:</b> alle {$crawl_cycle} Minuten<br>

		<h3><u>Community Daten des Webinterfaces</u></h3>
			{if !empty($router_last_crawl.community_nickname)}
				<b>Nickname:</b> {$router_last_crawl.community_nickname}<br>
			{/if}
			{if !empty($router_last_crawl.community_email)}
				<b>Email:</b> {$router_last_crawl.community_email}<br>
			{/if}
			{if !empty($router_last_crawl.community_prefix)}
				<b>Netzwerk Prefix:</b> {$router_last_crawl.community_prefix}<br>
			{/if}
			{if !empty($router_last_crawl.community_essid)}
				<b>ESSID:</b> {$router_last_crawl.community_essid}
			{/if}

		<h3><u>Netzwerk</u></h3>
			{if !empty($router_last_crawl.hostname)}
				<b>Hostname:</b> {$router_last_crawl.hostname}<br>
			{/if}
			{if !empty($router_last_crawl.ping)}
				<b>Ping:</b> {$router_last_crawl.ping}<br>
			{/if}

		<h3><u>Hardware</u></h3>
		<p>
			{if !empty($router_last_crawl.chipset)}
				<b>Chipset:</b> {$router_last_crawl.chipset}<br>
			{/if}
			{if !empty($router_last_crawl.cpu)}
				<b>Cpu:</b> {$router_last_crawl.cpu}<br>
			{/if}
			{if !empty($router_last_crawl.memory_total)}
				<b>Memory:</b> {$router_last_crawl.memory_total} Kb
			{/if}
		</p>

		<h3><u>Software</u></h3>
		<p>
			{if !empty($router_last_crawl.luciname)}
				<b>Luciname:</b> {$router_last_crawl.luciname}<br>
			{/if}
			{if !empty($router_last_crawl.luciversion)}
				<b>Luciversion:</b> {$router_last_crawl.luciversion}<br>
			{/if}
			{if !empty($router_last_crawl.distname)}
				<b>Distname:</b> {$router_last_crawl.distname}<br>
			{/if}
			{if !empty($router_last_crawl.distversion)}
				<b>Distversion:</b> {$router_last_crawl.distversion}
			{/if}
		</p>

		<h3><u>Status</u></h3>
		<p>
			{if !empty($router_last_crawl.memory_free)}
				<b>Free memory:</b> {$router_last_crawl.memory_free}/{$router_last_crawl.memory_total} Kb<br>
			{/if}
			{if !empty($router_last_crawl.loadavg)}
				<b>Loadaverage:</b> {$router_last_crawl.loadavg}<br>
			{/if}
			{if !empty($router_last_crawl.processes)}
				<b>Processes:</b> {$router_last_crawl.processes}<br>
			{/if}
			{if !empty($router_last_crawl.uptime)}
				<b>Uptime:</b> {$router_last_crawl.uptime/60/60|round:1} Stunden<br>
			{/if}
			{if !empty($router_last_crawl.idletime)}
				<b>Idletime:</b> {$router_last_crawl.idletime/60/60|round:1} Stunden
			{/if}
		</p>
	</div>
	<div style="float:left; width: 50%;">
			{if (!empty($router_data.latitude) AND !empty($router_data.longitude)) OR (!empty($router_last_crawl.latitude) AND !empty($router_last_crawl.longitude))}
			<h2>Standort ({if !empty($router_last_crawl.latitude)}crawl{else}Netmon{/if})</h2>
			<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
			<script src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.2&mkt=en-us"></script>
			
			<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
			
			<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
			<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>

			<div id="map" style="height:300px; width:450px; border:solid 1px black;font-size:9pt;">
				<script type="text/javascript">
					{if !empty($router_last_crawl.latitude)}
						var lat = {$router_last_crawl.latitude};
						var lon = {$router_last_crawl.longitude};

					{else}
						var lat = {$router_data.latitude};
						var lon = {$router_data.longitude};
					{/if}

					var radius = 30
					var zoom = 16;

					/* Initialize Map */
					ipmap({$service_data.service_id});
				</script>
			</div>
			<p><b>Standortbeschreibung:</b><br>
				{if !empty($router_last_crawl.latitude)}
					Lat: {$router_last_crawl.latitude}
					Lon: {$router_last_crawl.longitude}
				{else}
					Lat: {$router_data.latitude}
					Lon: {$router_data.longitude}
				{/if}
				<br><br>
				{if !empty($router_last_crawl.latitude) AND !empty($router_last_crawl.location)}
					 {$router_last_crawl.location}
				{elseif !empty($router_data.latitude) AND !empty($router_data.location)}
					{$router_last_crawl.location}
				{/if}
			</p>

		{/if}

	<h2>History</h2>
	{if !empty($router_history)}
		<ul>
			{foreach item=hist from=$router_history}
				<li>
					<b>{$hist.create_date|date_format:"%e.%m.%Y %H:%M"}:</b> 
					{if $hist.data.action == 'status' AND $hist.data.to == 'online'}
						Router geht <span style="color: #007B0F;">online</span>
					{/if}
					{if $hist.data.action == 'status' AND $hist.data.to == 'offline'}
						Router geht <span style="color: #CB0000;">offline</span>
					{/if}
				</li>
			{/foreach}
		</ul>
	{else}
		<p>Keine Daten vorhanden</p>
	{/if}

	<h2>Grafische Historie</h2>
	<p>
		<img src="./tmp/router_{$router_data.router_id}_memory.png">
	</p>

	</div>
</div>

{if !empty($router_batman_adv_interfaces)}
<div style="width: 100%; overflow: hidden;">
    <div style="float:left; width: 50%;">

	<h2>B.A.T.M.A.N advanced Monitoring</h2>
	<h3>Interfaces and status</h3>
	{if !empty($batman_adv_interfaces)}
		<ul>
			{foreach item=interface from=$batman_adv_interfaces}
				<li>
					<b>{$interface.name}</b> {$interface.status} ({$interface.crawl_date|date_format:"%e.%m.%Y %H:%M"})
				</li>
			{/foreach}
		</ul>
	{else}
		<p>Keine Interfaces gefunden</p>
	{/if}
	
	<h3>Originators</h3>
	{if !empty($batman_adv_originators.originators)}
		<ul>
			{foreach item=originators from=$batman_adv_originators.originators}
				<li>
					{$originators}
				</li>
			{/foreach}
		</ul>
	{else}
		<p>Keine Originators gefunden</p>
	{/if}
	</div>
	<div style="float:left; width: 50%;">
		<img src="./tmp/router_{$router_data.router_id}_originators.png">
	</div>
{/if}

{if !empty($router_olsr_interfaces)}
<div style="width: 100%; overflow: hidden;">
	<div style="float:left; width: 50%;">
			<h2>Olsr Monitoring - Links</h2>
			<div id="ipitem" style="width: 370px; overflow: hidden;">
			<div nstyle="white-space: nowrap;">
			<div style="float:left; width: 150px;"><b>Link IP</b></div>
			<div style="float:left; width: 150px;"><b>Local interface IP</b></div>
			<div style="float:left; width: 70px;"><b>ETX</b></div>
	</div>
</div>

{foreach item=olsrd_links from=$olsrd_crawl_data.olsrd_links}
	<div id="ipitem" style="width: 370px; overflow: hidden;">
		<div style="white-space: nowrap;">
			{assign var="tmp" value="Remote IP"}
			<div style="float:left; width: 150px;">{$olsrd_links.$tmp}</div>
			{assign var="tmp2" value="Local IP"}
			<div style="float:left; width: 150px;">{$olsrd_links.$tmp2}</div>
			<div style="float:left; width: 70px; background: {if $olsrd_links.Cost==0}#bb3333{elseif $olsrd_links.Cost<4}#00cc00{elseif $olsrd_links.Cost<10}#ffcb05{elseif $olsrd_links.Cost<100}#ff6600{/if};">{$olsrd_links.Cost}</div>
		</div>
	</div>
{/foreach}

	</div>
	<div style="float:left; width: 50%;">
	<p>
		<img src="./tmp/router_{$router_data.router_id}_olsrd_links.png">
	</p>
	</div>
{/if}

<h2>Interface Monitoring</h2>
{if !empty($interface_crawl_data)}
	{foreach item=interface from=$interface_crawl_data}
		<h3>{$interface.name} {if !empty($interface.wlan_frequency)}(Wlan Interface){/if} {if $interface.is_vpn=='true'}(VPN Interface){/if}</a></h3>
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 50%;">
				<ul>
					<li>
						<b>Letzte Aktualisierung:</b> {$interface.crawl_date|date_format:"%e.%m.%Y %H:%M"}
					</li>
				</ul>
		
				<ul>
					{if !empty($interface.mac_addr)}
						<li>
							<b>Mac Adresse:</b> {$interface.mac_addr}
						</li>
					{/if}
					{if !empty($interface.ipv4_addr)}
						<li>
							<b>IPv4 Adresse:</b> {$interface.ipv4_addr}		
						</li>
					{/if}
					{if !empty($interface.ipv6_addr)}
						<li>
							<b>IPv6 Adresse:</b> {$interface.ipv6_addr}		
						</li>
					{/if}
					{if !empty($interface.traffic_info.traffic_rx_per_second_kilobyte)}
						<li>
							<b>Average traffic received:</b> {$interface.traffic_info.traffic_rx_per_second_kilobyte} Kb/sec
						</li>
					{/if}
					{if !empty($interface.traffic_info.traffic_tx_per_second_kilobyte)}
						<li>
							<b>Average traffic transmitted:</b> {$interface.traffic_info.traffic_tx_per_second_kilobyte} Kb/sec
						</li>
					{/if}
				</ul>
				
				{if !empty($interface.wlan_frequency)}
					<ul>
						{if !empty($interface.wlan_mode)}
							<li>
								<b>Mode:</b> {$interface.wlan_mode}
							</li>
						{/if}
						{if !empty($interface.wlan_frequency)}
							<li>
								<b>Channel:</b> {$interface.wlan_frequency}		
							</li>
						{/if}
						{if !empty($interface.wlan_essid)}
							<li>
								<b>ESSID:</b> {$interface.wlan_essid}		
							</li>
						{/if}
						{if !empty($interface.wlan_bssid)}
							<li>
								<b>BSSID:</b> {$interface.wlan_bssid}		
							</li>
						{/if}
						{if !empty($interface.wlan_tx_power)}
							<li>
								<b>TX-Power:</b> {$interface.wlan_tx_power}		
							</li>
						{/if}
					</ul>
				{/if}
			</div>
			<div style="float:left; width: 50%;">
				<p>
					<img src="./tmp/router_{$router_data.router_id}_interface_{$interface.name}_traffic_rx.png">
				</p>
			</div>
		</div>
		<hr>
	{/foreach}
{else}
	<p>Keine Daten zu Interfaces vorhanden</p>
{/if}


{if $ip.is_ip_owner}
<h2>Aktionen</h2>

<p>
  <a href="./serviceeditor.php?section=new&ip_id={$ip.ip_id}">Dienst hinzufügen</a>
</p>

<p>
  <a href="./ipeditor.php?section=edit&id={$ip.ip_id}">Ip Editieren</a><br>
  <a href="./imagemaker.php?section=new&ip_id={$ip.ip_id}">Image Downloaden</a><br>

<!--  <a href="./vpn.php?section=edit&ip_id={$ip.ip_id}">VPN-Optionen</a>-->
</p>

<p>
  <a href="./vpn.php?section=new&ip_id={$ip.ip_id}">Neue VPN-Zertifikate generieren</a><br>
  <a href="./vpn.php?section=info&ip_id={$ip.ip_id}">VPN-Zertifikat, VPN-Config und CCD ansehen</a><br>
  <a href="./vpn.php?section=insert_regenerate_ccd&ip_id={$ip.ip_id}">CCD neu anlegen</a><br>
  <a href="./vpn.php?section=insert_delete_ccd&ip_id={$ip.ip_id}">CCD löschen</a><br>
  <a href="./vpn.php?section=download&ip_id={$ip.ip_id}">VPN-Zertifikate und Config-Datei downloaden</a><br>
</p>

{/if}