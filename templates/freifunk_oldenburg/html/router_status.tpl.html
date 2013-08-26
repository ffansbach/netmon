<script src="lib/extern/jquery/jquery.min.js"></script>
<script src="lib/extern/DataTables/jquery.dataTables.min.js"></script>

<!-- Javascript for the graphs -->
<script type="text/javascript" src="lib/extern/javascriptrrd/binaryXHR.js"></script>
<script type="text/javascript" src="lib/extern/javascriptrrd/rrdFile.js"></script>
<!-- rrdFlot class needs the following four include files !-->
<script type="text/javascript" src="lib/extern/javascriptrrd/rrdFlotSupport.js"></script>
<script type="text/javascript" src="lib/extern/javascriptrrd/rrdFlot.js"></script>
<script type="text/javascript" src="lib/extern/flot/jquery.flot.js"></script>
<script type="text/javascript" src="lib/extern/flot/jquery.flot.selection.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#batman_adv_originator_list').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"sScrollY": "300px",
		"bPaginate": false,
		"aaSorting": [[ 2, "desc" ]]
	} );
} );
{/literal}
</script>

{literal}
	<script>
		function setBatmanAdvLinqQualityPictures(originator) {
			$(document).ready(function(){
				document.getElementById('batman_adv_link_quality_average_graphic').style.display = 'none';
{/literal}
	{foreach $batman_adv_originators as $originators}
				document.getElementById('batman_adv_link_quality_{$originators.originator_file_path}_graphic').style.display = 'none';
	{/foreach}
{literal}
				document.getElementById('batman_adv_link_quality_'+originator+'_graphic').style.display = 'block';
				document.getElementById('batman_adv_link_quality_title').innerText = originator;
			});
		}
	</script>
{/literal}

{literal}
    <script type="text/javascript">
      // This function updates the Web Page with the data from the RRD archive header
      // when a new file is selected
      function update_fname(html_graph_id) {
        var graph_opts={legend: {position:"ne", noColumns:2} };
        var ds_graph_opts={	'traffic_rx':{checked:true, color: "#3118c3", lines: { show: true, fill: true, fillColor:""} },
							'traffic_tx':{checked:true, color: "#ef2700", lines: { show: true, fill: true, fillColor:""} },
							'memory_caching':{checked:true, color: "#e30f0f", lines: { show: true, fill: true, fillColor:""} },
							'memory_buffering':{checked:true, color: "#56f5e4", lines: { show: true, fill: true, fillColor:""} },
							'memory_free':{checked:true, color: "#0009c3", lines: { show: true, fill: true, fillColor:""} },
							'clients':{checked:true, color: "#006c7b", lines: { show: true, fill: false} },
							'originators':{checked:true, color: "#46ddd4", lines: { show: true, fill: false} },
							'total':{checked:true, color: "#d97500", lines: { show: true, fill: false} },
							'runnable':{checked:true, color: "#d90000", lines: { show: true, fill: false} },
							'quality':{checked:true, color: "#3118c3", lines: { show: true, fill: true, fillColor:""} } };

        // the rrdFlot object creates and handles the graph
        var f=new rrdFlot(html_graph_id,rrd_data,graph_opts,ds_graph_opts);
      }

      // This is the callback function that,
      // given a binary file object,
      // verifies that it is a valid RRD archive
      // and performs the update of the Web page
      function update_fname_handler(bf, html_graph_id) {
          var i_rrd_data=undefined;
          try {
            var i_rrd_data=new RRDFile(bf);            
          } catch(err) {
            alert("File "+fname+" is not a valid RRD archive!");
          }
          if (i_rrd_data!=undefined) {
            rrd_data=i_rrd_data;
            update_fname(html_graph_id)
          }
      }
</script>
{/literal}

<h1>Statusdaten des Routers {$router_data.hostname}</h1>

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
	<div style="float:left; width: 50%;">
		<h2>Grunddaten</h2>
		<b>Benutzer:</b> <a href="./user.php?user_id={$router_data.user_id}">{$router_data.nickname}</a><br>
		<b>Angelegt am:</b> {$router_data.create_date|date_format:"%d.%m.%Y %H:%M"} Uhr<br>

		<h2>System Monitoring</h2>
		<h3><u>Allgemein</u></h3>
		<b>Status:</b> 
		{if $router_last_crawl.status=="online"}
			<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="online">
		{elseif $router_last_crawl.status=="offline"}
			<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="offline">
		{elseif $router_last_crawl.status=="unknown"}
			<img src="./templates/{$template}/img/ffmap/status_unknown_small.png" title="unknown" alt="unknown">
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
			{if !empty($router_last_crawl.openwrt_core_revision)}
				<b>OpenWrt Core Revision:</b> {$router_last_crawl.openwrt_core_revision}<br>
			{/if}
			{if !empty($router_last_crawl.openwrt_feeds_packages_revision)}
				<b>OpenWrt Package Feed revision:</b> {$router_last_crawl.openwrt_feeds_packages_revision}<br>
			{/if}
			{if !empty($router_last_crawl.kernel_version)}
				<b>Kernelversion:</b> {$router_last_crawl.kernel_version}<br>
			{/if}
			{if !empty($router_last_crawl.batman_advanced_version)}
				<b>Batman advanced Version:</b> {$router_last_crawl.batman_advanced_version}<br>
			{/if}
			{if !empty($router_last_crawl.nodewatcher_version)}
				<b>Nodewatcher Version:</b> {$router_last_crawl.nodewatcher_version}<br>
			{/if}
			{if !empty($router_last_crawl.firmware_version)}
				<b>Firmware Version:</b> {$router_last_crawl.firmware_version}<br>
			{/if}
			{if !empty($router_last_crawl.firmware_revision)}
				<b>Firmware Revision:</b> {$router_last_crawl.firmware_revision}
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
				<b>Idletime:</b> {math equation="round(x,1)" x=$router_last_crawl.idletime/60/60} Stunden<br>
			{/if}
			{if !empty($router_last_crawl.local_time)}
				<b>Zeit auf dem Router:</b> {$router_last_crawl.local_time|date_format:"%d.%m.%Y %H:%M"} Uhr
			{/if}
		</p>
		<h3><u>Clients</u></h3>
		<p><b>Verbundene Clients:</b> {$router_last_crawl.client_count}</p>
	</div>
	<div style="float:left; width: 50%;">
		<h2>Standort ({if !empty($router_last_crawl.latitude)}crawl{else}Netmon{/if})</h2>
		{if (!empty($router_data.latitude) AND !empty($router_data.longitude)) OR (!empty($router_last_crawl.latitude) AND !empty($router_last_crawl.longitude))}
			<script type="text/javascript" src='https://maps.googleapis.com/maps/api/js?key={$google_maps_api_key}&sensor=false'></script>
			<script type="text/javascript" src="./lib/extern/openlayers/OpenLayers.js"></script>
			<script type="text/javascript" src="./templates/{$template}/js/OpenStreetMap.js"></script>
			<script type="text/javascript" src="./templates/{$template}/js/OsmFreifunkMap.js"></script>
			<div id="map" style="height:300px; width:400px; border:solid 1px black;font-size:9pt;">
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
		{else}
			<p>Keine Standortdaten verfügbar</p>
		{/if}

		<h2>Events</h2>
		{if !empty($eventlist)}
			<ul>
				{foreach key=count item=event from=$eventlist}
					<li>
						<b><a href="event.php?event_id={$event->getEventId()}">{$event->getCreateDate()|date_format:"%e.%m.%Y %H:%M"}</a>:</b> 
						{if $event->getObject() == 'router'}
							{assign var="data" value=$event->getData()}
							
							{if $event->getAction() == 'status' AND $data.to == 'online'}
								geht <span style="color: #007B0F;">online</span>
							{/if}
							{if $event->getAction() == 'status' AND $data.to == 'offline'}
								geht <span style="color: #CB0000;">offline</span>
							{/if}
							{if $event->getAction() == 'reboot'}
								wurde <span style="color: #000f9c;">Rebootet</span>
							{/if}
							{if $event->getAction() == 'new'}
								wurde Netmon hinzugefügt
							{/if}
							{if $event->getAction() == 'batman_advanced_version'}
								änderte Batman adv. Version von {$data.from} zu {$data.to}</span>
							{/if}
							{if $event->getAction() == 'firmware_version'}
								änderte Firmware Version von {$data.from} zu {$data.to}</span>
							{/if}
							{if $event->getAction() == 'nodewatcher_version'}
								änderte Nodewatcher Version von {$data.from} zu {$data.to}</span>
							{/if}
							{if $event->getAction() == 'distversion'}
								änderte Distversion Version von {$data.from} zu {$data.to}</span>
							{/if}
							{if $event->getAction() == 'distname'}
								änderte Distname Version von {$data.from} zu {$data.to}</span>
							{/if}
							{if $event->getAction() == 'hostname'}
								änderte Hostname von {$data.from} zu {$data.to}</span>
							{/if}
							{if $event->getAction() == 'chipset'}
								änderte Chipset von {$data.from} zu {$data.to}</span>
							{/if}
							{if $event->getAction() == 'watchdog_ath9k_bug'}
								<a href="./event.php?event_id={$event->getEventId()}">ATH9K Bug registriert</a>
							{/if}
						{/if}
						{if $event->getObject() == 'not_assigned_router'}
							{assign var="data" value=$event->getData()}
							
							{$data.router_auto_assign_login_string}
							{if $event->getAction() == 'new'}
								 erscheint in der <a href="./routers_trying_to_assign.php">Liste der neuen, nicht zugewiesenen Router</a>
							{/if}
						{/if}
					</li>
				{/foreach}
			</ul>
		{else}
			<p>Es sind keine Events in der Datenbank gespeichert.</p>
		{/if}

		<h2>Grafische Historie</h2>
		<h3>Memory</h3>
		{if $memory_rrd_file_exists}
		<script type="text/javascript">
			fname='./rrdtool/databases/router_{$router_data.router_id}_memory.rrd';
			html_graph_id="memory_graph"
			try {
				FetchBinaryURLAsync(fname,update_fname_handler, html_graph_id);
			} catch (err) {
				alert("Failed loading "+fname+"\n"+err);
			}
		</script>
		<div id="memory_graph" style="float:left; width: 100%;"></div>
		{/if}

		<h3>Prozesse</h3>
		{if $processes_rrd_file_exists}
		<script type="text/javascript">
			fname='./rrdtool/databases/router_{$router_data.router_id}_processes.rrd';
			html_graph_id="processes_graph"
			try {
				FetchBinaryURLAsync(fname,update_fname_handler, html_graph_id);
			} catch (err) {
				alert("Failed loading "+fname+"\n"+err);
			}
		</script>
		<div id="processes_graph" style="float:left; width: 100%;"></div>
		{/if}
		
		<h3>Client Historie</h3>
		{if $clients_rrd_file_exists}
		<script type="text/javascript">
			fname='./rrdtool/databases/router_{$router_data.router_id}_clients.rrd';
			html_graph_id="client_history_graph"
			try {
				FetchBinaryURLAsync(fname,update_fname_handler, html_graph_id);
			} catch (err) {
				alert("Failed loading "+fname+"\n"+err);
			}
		</script> 
		<div id="client_history_graph" style="float:left; width: 100%;"></div>
		{/if}
	</div>
</div>

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
			<table class="display" id="batman_adv_originator_list" style="font-size: 8pt;">
				<thead>
					<tr>
						<th>Originator</th>
						<th>Last Seen</th>
						<th>Qual.</th>
						<th>Nexthop</th>
						<th>Out. Iface</th>
					</tr>
				</thead>
				<tbody>
					{foreach $batman_adv_originators as $originators}
						<tr style="{if $originators.originator== $originators.nexthop}font-weight: bold;{/if} background-color: {if $originators.link_quality >= 0 AND $originators.link_quality < 105}#ff1e1e{else if $originators.link_quality >= 105 AND $originators.link_quality < 130}#ff4949{else if $originators.link_quality >= 130 AND $originators.link_quality < 155}#ff6a6a{else if $originators.link_quality >= 155 AND $originators.link_quality < 180}#ffac53{else if $originators.link_quality >= 180 AND $originators.link_quality < 205}#ffeb79{else if $originators.link_quality >= 205 AND $originators.link_quality < 230}#79ff7c{else if $originators.link_quality >= 230}#04ff0a{/if};">
							<td><a href="search.php?search_range=mac_addr&search_string={$originators.originator}">{$originators.originator}</a></td>
							<td>{$originators.last_seen}</td>
							<td>{$originators.link_quality}</td>
							<td>{$originators.nexthop}</td>
							<td>{$originators.outgoing_interface}</td>
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
		{if $rrd_originators_db_exists}
			<script type="text/javascript">
				fname='./rrdtool/databases/router_{$router_data.router_id}_originators.rrd';
				html_graph_id="batman_adv_originator_graphic"
				try {
					FetchBinaryURLAsync(fname,update_fname_handler, html_graph_id);
				} catch (err) {
					alert("Failed loading "+fname+"\n"+err);
				}
			</script> 
			<div id="batman_adv_originator_graphic" style="float:left; width: 100%;"></div>
		{else}
			<p>Keine Grafik zum anzeigen vorhanden</p>
		{/if}

		<h3>Graphic Link Quality für <span id="batman_adv_link_quality_title">average</span></h3>
		{if $rrd_link_quality_db_exists}
			<script type="text/javascript">
				fname='./rrdtool/databases/router_{$router_data.router_id}_batman_adv_link_quality_average.rrd';
				html_graph_id="batman_adv_link_quality_average_graphic"
				try {
					FetchBinaryURLAsync(fname,update_fname_handler, html_graph_id);
				} catch (err) {
					alert("Failed loading "+fname+"\n"+err);
				}
			</script> 
			<div id="batman_adv_link_quality_average_graphic" style="float:left; width: 100%;"></div>
			
			{foreach $batman_adv_originators as $originators}
				<script type="text/javascript">
					fname='./rrdtool/databases/router_{$router_data.router_id}_batman_adv_link_quality_{$originators.originator_file_path}.rrd';
					html_graph_id="batman_adv_link_quality_{$originators.originator_file_path}_graphic"
					try {
						FetchBinaryURLAsync(fname,update_fname_handler, html_graph_id);
					} catch (err) {
						alert("Failed loading "+fname+"\n"+err);
					}
				</script> 
				<div id="batman_adv_link_quality_{$originators.originator_file_path}_graphic" style="float:left; width: 100%; display: none;"></div>
			{/foreach}
			
			<p>
				<select name="search_range" onChange="setBatmanAdvLinqQualityPictures(this.options[this.selectedIndex].value)">
					<option value="average" >Zeige Grafik für Average</option>
					{foreach $batman_adv_originators as $originators}
						<option value="{$originators.originator_file_path}" >Zeige Grafik für {$originators.originator}</option>
					{/foreach}
				</select>
			</p>
		{else}
			<p>Keine Grafik zum anzeigen vorhanden</p>
		{/if}
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
		</div>
		<div style="float:left; width: 60%;">
			<p>
				<img src="./tmp/router_{$router_data.router_id}_olsrd_links.png">
			</p>
		</div>
	</div>
{/if}

<h2>Interface Monitoring</h2>
{if !empty($interface_crawl_data)}
	{foreach $interface_crawl_data as $interface key=schluessel}
		<script type="text/javascript">
			fname='./rrdtool/databases/router_{$router_data.router_id}_interface_{$interface.name}_traffic_rx.rrd';
			html_graph_id="jrrd_{$schluessel}"
			try {
				FetchBinaryURLAsync(fname,update_fname_handler, html_graph_id);
			} catch (err) {
				alert("Failed loading "+fname+"\n"+err);
			}
		</script> 
		
		<script type="text/javascript">
			{literal}
				$(document).ready(function(){
					var selectedEffect = 'blind';
					var options = {direction: 'vertical'};
			{/literal}
			{if empty($interface.wlan_frequency) AND $interface.is_vpn!='true' AND $interface.name!='br-mesh'}
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
					<li>
						<b>Average traffic received:</b> {$interface.traffic_info.traffic_rx_per_second_kilobyte} Kb/sec
					</li>
					<li>
						<b>Average traffic transmitted:</b> {$interface.traffic_info.traffic_tx_per_second_kilobyte} Kb/sec
					</li>
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
								<b>Channel:</b> {$interface.wlan_channel} ({$interface.wlan_frequency})
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
			<div id="jrrd_{$schluessel}" style="float:left; width: 53%;"></div>
		</div>
	{/foreach}
{else}
	<p>Keine Daten zu Interfaces vorhanden</p>
{/if}

<br style="clear: both">