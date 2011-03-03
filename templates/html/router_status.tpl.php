<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>


<script src="lib/classes/extern/DataTables/jquery.dataTables.js"></script>

<link rel="stylesheet" type="text/css" href="templates/css/jquery_data_tables.css">

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#batman_adv_originator_list').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false
	} );
} );
{/literal}
</script>

{literal}
	<script>
		function setBatmanAdvLinqQualityPictures(originator) {
			$(document).ready(function(){
				document.getElementById('batman_adv_link_quality_average_12_hours').style.display = 'none';
				document.getElementById('batman_adv_link_quality_average_1_day').style.display = 'none';
				document.getElementById('batman_adv_link_quality_average_1_week').style.display = 'none';
{/literal}
	{foreach $batman_adv_originators as $originators}
				document.getElementById('batman_adv_link_quality_{$originators.originator_file_path}_12_hours').style.display = 'none';
				document.getElementById('batman_adv_link_quality_{$originators.originator_file_path}_1_day').style.display = 'none';
				document.getElementById('batman_adv_link_quality_{$originators.originator_file_path}_1_week').style.display = 'none';
	{/foreach}
{literal}

				document.getElementById('batman_adv_link_quality_'+originator+'_12_hours').style.display = 'block';
				document.getElementById('batman_adv_link_quality_'+originator+'_1_day').style.display = 'block';
				document.getElementById('batman_adv_link_quality_'+originator+'_1_week').style.display = 'block';
			});
		}
	</script>
{/literal}

<script type="text/javascript">
	document.body.id='tab1';
</script>

<ul id="tabnav">
	<li class="tab1"><a href="./router_status.php?router_id={$router_data.router_id}">Router Status</a></li>
	<li class="tab2"><a href="./router_config.php?router_id={$router_data.router_id}">Router Konfiguration</a></li>
</ul>

<h1>Daten des Routers {$router_data.hostname}</h1>

<div style="width: 100%; overflow: hidden;">
	<div style="float:left; width: 47%;">
		{if !empty($next_smaller_crawl_cycle.id)}
		<a href="./router_status.php?router_id={$router_data.router_id}&crawl_cycle_id={$next_smaller_crawl_cycle.id}">&lt; zum vorherigen Datensatz</a>
		{/if}
		{if !empty($online_crawl_before.crawl_cycle_id)}
		<br>
		<a href="./router_status.php?router_id={$router_data.router_id}&crawl_cycle_id={$online_crawl_before.crawl_cycle_id}">&lt;&lt; zum vorherigen online Datensatz</a>
		{/if}
	</div>
	<div style="float:left; width: 53%; text-align: right;">

		{if !empty($next_bigger_crawl_cycle.id)}
		<a href="./router_status.php?router_id={$router_data.router_id}&crawl_cycle_id={$next_bigger_crawl_cycle.id}">zum nächsten Datensatz &gt;</a>
		{/if}
		{if !empty($online_crawl_next.crawl_cycle_id)}
		<br>
		<a href="./router_status.php?router_id={$router_data.router_id}&crawl_cycle_id={$online_crawl_next.crawl_cycle_id}">zum nächsten online Datensatz &gt;&gt;</a>
		{/if}
		{if !empty($online_crawl_next.crawl_cycle_id) OR !empty($next_bigger_crawl_cycle.id)}
		<br>
		<a href="./router_status.php?router_id={$router_data.router_id}">zum aktuellen Datensatz &gt;&gt;</a>
		{/if}
	</div>
</div>

<div style="width: 100%; overflow: hidden; margin-bottom: 20px;">
	<div style="float:left; width: 45%; padding-right: 15px;">
		<h2>Grunddaten</h2>
		<b>Benutzer:</b> <a href="./user.php?user_id={$router_data.user_id}">{$router_data.nickname}</a><br>
		<b>Angelegt am:</b> {$router_data.create_date|date_format:"%d.%m.%Y %H:%M"} Uhr<br>

		<h2>System Monitoring</h2>
		<h3><u>Allgemein</u></h3>
		<b>Status:</b> 
		{if $router_last_crawl.status=="online"}
			<img src="./templates/img/ffmap/status_up_small.png" alt="online">
		{elseif $router_last_crawl.status=="offline"}
			<img src="./templates/img/ffmap/status_down_small.png" alt="offline">
		{elseif $router_last_crawl.status=="unknown"}
			<img src="./templates/img/ffmap/status_unknown_small.png" title="unknown" alt="unknown">
		{/if}
		<br>
		<b>Zuverlässigkeit:</b> {math equation="round(x,1)" x=$router_reliability.online_percent}% online<br>
		<b>Datenquelle:</b> {if $router_data.crawl_method=='router'}Router sendet Daten{elseif $router_data.crawl_method=='crawler'}Netmon Crawler{/if}<br>
		<b>Letzter Crawl:</b> {$router_last_crawl.crawl_date|date_format:"%d.%m.%Y %H:%M"}<br>
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
				<b>Distversion:</b> {$router_last_crawl.distversion}<br>
			{/if}
			{if !empty($router_last_crawl.kernel_version)}
				<b>Kernelversion:</b> {$router_last_crawl.kernel_version}<br>
			{/if}
			{if !empty($router_last_crawl.batman_advanced_version)}
				<b>Batman advanced Version:</b> {$router_last_crawl.batman_advanced_version}
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
				<b>Uptime:</b> {math equation="round(x,1)" x=$router_last_crawl.uptime/60/60} Stunden<br>
			{/if}
			{if !empty($router_last_crawl.idletime)}
				<b>Idletime:</b> {math equation="round(x,1)" x=$router_last_crawl.idletime/60/60} Stunden
			{/if}
		</p>
		<h3><u>Clients</u></h3>
		<p><b>Verbundene Clients:</b> {$client_count}</p>
	</div>
	<div style="float:left; width: 53%;">
		{if (!empty($router_data.latitude) AND !empty($router_data.longitude)) OR (!empty($router_last_crawl.latitude) AND !empty($router_last_crawl.longitude))}
			<h2>Standort ({if !empty($router_last_crawl.latitude)}crawl{else}Netmon{/if})</h2>
			<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
			<script src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.2&mkt=en-us"></script>
			
			<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
			
			<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
			<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
			<div id="map" style="height:220px; width:400px; border:solid 1px black;font-size:9pt;">
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
					router_map({$router_data.router_id});
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
				{foreach $router_history as $hist}
					<li>
						<b>{$hist.create_date|date_format:"%e.%m. %H:%M:%S"}:</b> 
						{if $hist.data.action == 'status' AND $hist.data.to == 'online'}
							Router geht <span style="color: #007B0F;">online</span>
						{/if}
						{if $hist.data.action == 'status' AND $hist.data.to == 'offline'}
							Router geht <span style="color: #CB0000;">offline</span>
						{/if}
						{if $hist.data.action == 'reboot'}
							Router wurde <span style="color: #000f9c;">Rebootet</span>
						{/if}
					</li>
				{/foreach}
			</ul>
		{else}
			<p>Keine Daten vorhanden</p>
		{/if}

		<h2>Grafische Historie</h2>
		{literal}
			<script>
				$(document).ready(function() {
		{/literal}
				$("#tabs_router_memory").tabs();
		{literal}
				});
			</script>
		{/literal}

		<h3>Memor</h3>
		<div id="tabs_router_memory" style="width: 96%">
			<ul>
				<li><a href="#fragment-1_router_memory"><span>12 Sunden</span></a></li>
				<li><a href="#fragment-2_router_memory"><span>24 Stunden</span></a></li>
				<li><a href="#fragment-3_router_memory"><span>7 Tage</span></a></li>
			</ul>
			<div id="fragment-1_router_memory">
				<img src="./tmp/router_{$router_data.router_id}_memory_12_hours.png">
			</div>
			<div id="fragment-2_router_memory">
				<img src="./tmp/router_{$router_data.router_id}_memory_1_day.png">
			</div>
			<div id="fragment-3_router_memory">
				<img src="./tmp/router_{$router_data.router_id}_memory_1_week.png">
			</div>
		</div>

		{literal}
			<script>
				$(document).ready(function() {
		{/literal}
					$("#tabs_clients").tabs();
		{literal}
				});
			</script>
		{/literal}

		<h3>Client Historie</h3>
		<div id="tabs_clients" style="width: 96%">
			<ul>
			        <li><a href="#fragment-1_clients"><span>12 Sunden</span></a></li>
			        <li><a href="#fragment-2_clients"><span>24 Stunden</span></a></li>
			        <li><a href="#fragment-3_clients"><span>7 Tage</span></a></li>
			</ul>
			<div id="fragment-1_clients">
				<img src="./tmp/router_{$router_data.router_id}_clients_12_hours.png">
			</div>
			<div id="fragment-2_clients">
				<img src="./tmp/router_{$router_data.router_id}_clients_1_day.png">
			</div>
			<div id="fragment-3_clients">
				<img src="./tmp/router_{$router_data.router_id}_clients_1_week.png">
			</div>
		</div>
	</div>
</div>

{literal}
	<script>
		$(document).ready(function() {
{/literal}
			$("#tabs_batman_adv").tabs();
{literal}
		});
	</script>
{/literal}
<div style="width: 100%; overflow: hidden;">
	<h2>B.A.T.M.A.N advanced Monitoring</h2>
	<div style="float:left; width: 45%; padding-right: 15px;">
		<h3>Interfaces and status</h3>
		{if !empty($crawl_batman_adv_interfaces)}
			<ul>
				{foreach $crawl_batman_adv_interfaces as $interface}
					<li>
						<b>{$interface.name}</b> {$interface.status} ({$interface.crawl_date|date_format:"%e.%m.%Y %H:%M"})
					</li>
				{/foreach}
			</ul>
		{else}
			<p>Keine Interfaces gefunden</p>
		{/if}
		
		<h3>Originators</h3>
		{if !empty($batman_adv_originators)}
			<table class="display" id="batman_adv_originator_list">
				<thead>
					<tr>
						<th>Originator</th>
						<th>Quality</th>
						<th>Last Seen</th>
					</tr>
				</thead>
				<tbody>
					{foreach $batman_adv_originators as $originators}
						<tr>
							<td><a href="search.php?search_range=mac_addr&search_string={$originators.originator}">{$originators.originator}</a></td>
							<td>{$originators.link_quality}</td>
							<td>{$originators.last_seen}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		{else}
			<p>Keine Originators gefunden</p>
		{/if}
	</div>
	<div style="float:left; width: 53%;">
		<h3>Anzahl der Nachbarn</h3>
		<div id="tabs_batman_adv" style="width: 96%">
			<ul>
			        <li><a href="#fragment-1_batman_adv"><span>12 Sunden</span></a></li>
			        <li><a href="#fragment-2_batman_adv"><span>24 Stunden</span></a></li>
			        <li><a href="#fragment-3_batman_adv"><span>7 Tage</span></a></li>
			</ul>
			<div id="fragment-1_batman_adv">
				<img src="./tmp/router_{$router_data.router_id}_originators_12_hours.png">
			</div>
			<div id="fragment-2_batman_adv">
				<img src="./tmp/router_{$router_data.router_id}_originators_1_day.png">
			</div>
			<div id="fragment-3_batman_adv">
				<img src="./tmp/router_{$router_data.router_id}_originators_1_week.png">
			</div>
		</div>
		
		<h3>Graphic Link Quality</h3>
		{literal}
			<script>
				$(document).ready(function() {
		{/literal}
					$("#tabs_batman_adv_link_quality_average").tabs();
		{literal}
				});
			</script>
		{/literal}
		
		<div id="tabs_batman_adv_link_quality_average" style="width: 96%">
			<ul>
	 		       <li><a href="#fragment-1_batman_adv_link_quality_average"><span>12 Sunden</span></a></li>
	   		     <li><a href="#fragment-2_batman_adv_link_quality_average"><span>24 Stunden</span></a></li>
	    		    <li><a href="#fragment-3_batman_adv_link_quality_average"><span>7 Tage</span></a></li>
			</ul>
			<div id="fragment-1_batman_adv_link_quality_average">
				<img id="batman_adv_link_quality_average_12_hours" style="display: block;" src="tmp/router_{$router_data.router_id}_batman_adv_link_quality_average_12_hours.png">
				{foreach $batman_adv_originators as $originators}
					<img id="batman_adv_link_quality_{$originators.originator_file_path}_12_hours" style="display: none;" src="tmp/router_{$router_data.router_id}_batman_adv_link_quality_{$originators.originator_file_path}_12_hours.png">
				{/foreach}
			</div>
			<div id="fragment-2_batman_adv_link_quality_average">
				<img id="batman_adv_link_quality_average_1_day" style="display: block;" src="tmp/router_{$router_data.router_id}_batman_adv_link_quality_average_1_day.png">
				{foreach $batman_adv_originators as $originators}
					<img id="batman_adv_link_quality_{$originators.originator_file_path}_1_day" style="display: none;" src="tmp/router_{$router_data.router_id}_batman_adv_link_quality_{$originators.originator_file_path}_1_day.png">
				{/foreach}
			</div>
			<div id="fragment-3_batman_adv_link_quality_average">
				<img id="batman_adv_link_quality_average_1_week" style="display: block;" src="tmp/router_{$router_data.router_id}_batman_adv_link_quality_average_1_week.png">
				{foreach $batman_adv_originators as $originators}
					<img id="batman_adv_link_quality_{$originators.originator_file_path}_1_week" style="display: none;" src="tmp/router_{$router_data.router_id}_batman_adv_link_quality_{$originators.originator_file_path}_1_week.png">
				{/foreach}
			</div>
		</div>
		
		<p>
			<select name="search_range" onChange="setBatmanAdvLinqQualityPictures(this.options[this.selectedIndex].value)">
				<option value="average" >Zeige Grafik für Average</option>
				{foreach $batman_adv_originators as $originators}
					<option value="{$originators.originator_file_path}" >Zeige Grafik für {$originators.originator}</option>
				{/foreach}
			</select>
		</p>
	</div>
</div>

{if !empty($router_olsr_interfaces)}
	<div style="width: 100%; overflow: hidden;">
		<div style="float:left; width: 40%;">
			<h2>Olsr Monitoring - Links</h2>
			<div id="ipitem" style="width: 370px; overflow: hidden;">
			<div nstyle="white-space: nowrap;">
			<div style="float:left; width: 150px;"><b>Link IP</b></div>
			<div style="float:left; width: 150px;"><b>Local interface IP</b></div>
			<div style="float:left; width: 70px;"><b>ETX</b></div>
		</div>
	</div>
	
	{foreach $olsrd_crawl_data.olsrd_links as $olsrd_links}
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

	<div style="float:left; width: 60%;">
		<p>
			<img src="./tmp/router_{$router_data.router_id}_olsrd_links.png">
		</p>
	</div>
{/if}

<h2>Interface Monitoring</h2>
{if !empty($interface_crawl_data)}
	{foreach $interface_crawl_data as $interface key=schluessel}
<script type="text/javascript">
	{literal}
	$(document).ready(function(){
		var selectedEffect = 'blind';
		var options = {direction: 'vertical'};
	{/literal}
		{if empty($interface.wlan_frequency) AND $interface.is_vpn!='true'}
			$('#traffic_{$schluessel}').hide(); // Hide div by default
			document.getElementById('traffic_title_arrow_up_{$schluessel}').style.display = 'inline';
			document.getElementById('traffic_title_arrow_down_{$schluessel}').style.display = 'none';
		{else}
			document.getElementById('traffic_title_arrow_up_{$schluessel}').style.display = 'none';
			document.getElementById('traffic_title_arrow_down_{$schluessel}').style.display = 'inline';
		{/if}
		$('#traffic_title_{$schluessel}').click(function(){literal} { {/literal}
			$('#traffic_{$schluessel}').toggle(selectedEffect,options,650); // First click should toggle to 'show'
			return false;
		{literal} } );
	});
	{/literal}
</script> 



		{literal}
			<script>
				$(document).ready(function() {
		{/literal}
					$("#tabs_{$schluessel}").tabs();
		{literal}
				});
			</script>
		{/literal}
<!--onClick="$('#traffic_{$schluessel}').slideToggle(800);"-->



		<div onclick="var el = document.getElementById('traffic_title_arrow_up_{$schluessel}'); var el2 = document.getElementById('traffic_title_arrow_down_{$schluessel}'); {literal} if (el.style.display == 'none') {el.style.display = 'inline'; el2.style.display = 'none';} else {el.style.display = 'none'; el2.style.display = 'inline';} {/literal}" id="traffic_title_{$schluessel}" style="width: 98%; background: #81b59e;" >
			<h3><span id="traffic_title_arrow_up_{$schluessel}">↑</span><span id="traffic_title_arrow_down_{$schluessel}">↓</span> | {$interface.name} {if !empty($interface.wlan_frequency)}(Wlan Interface){/if} {if $interface.is_vpn=='true'}(VPN Interface){/if}</h3>
		</div>
		<div id="traffic_{$schluessel}" style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 47%;">
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
					{if !empty($interface.mtu)}
						<li>
							<b>MTU:</b> {$interface.mtu}
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
					{if !empty($interface.ipv6_link_local_addr)}
						<li>
							<b>IPv6 Link Local Adresse:</b> {$interface.ipv6_link_local_addr}		
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
			<div style="float:left; width: 53%;">
				<div id="tabs_{$schluessel}" style="width: 96%">
					<ul>
						<li><a href="#fragment-1_{$schluessel}"><span>12 Sunden</span></a></li>
					        <li><a href="#fragment-2_{$schluessel}"><span>24 Stunden</span></a></li>
					        <li><a href="#fragment-3_{$schluessel}"><span>7 Tage</span></a></li>
					</ul>
					<div id="fragment-1_{$schluessel}">
						<img src="./tmp/router_{$router_data.router_id}_interface_{$interface.name}_traffic_rx_12_hours.png">
					</div>
					<div id="fragment-2_{$schluessel}">
						<img src="./tmp/router_{$router_data.router_id}_interface_{$interface.name}_traffic_rx_1_day.png">
					</div>
					<div id="fragment-3_{$schluessel}">
						<img src="./tmp/router_{$router_data.router_id}_interface_{$interface.name}_traffic_rx_1_week.png">
					</div>
				</div>
			</div>
		</div>

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