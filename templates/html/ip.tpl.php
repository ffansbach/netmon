<h1>Übersicht der IP <a href="http://{$net_prefix}.{$ip.ip}">{$net_prefix}.{$ip.ip}</a></h1>

<div style="width: 100%; overflow: hidden;">
    <div style="float:left; width: 50%;">
		<h2>Grunddaten</h2>
		<b>IP:</b> {$net_prefix}.{$ip.ip}<br>
		<b>Projekt:</b>  <a href="./subnet.php?id={$ip.subnet_id}">{$ip.title} ({$net_prefix}.{$ip.subnet_host}/{$ip.subnet_netmask})</a><br>
		 {if $subnet_data.dhcp_kind=='ips'}
			<b>DHCP:</b> {if $ip.zone_start==0 OR $ip.zone_end==0}
							Kein DHCP-Bereich reserviert
						{else}
							{$net_prefix}.{$ip.zone_start} bis {$net_prefix}.{$ip.zone_end}
						{/if}
		{elseif $subnet_data.dhcp_kind=='subnet'}
			<b>DHCP-Subnetz:</b> {$net_prefix}.{$ip.dhcp_host}/{$ip.dhcp_netmask}
		{elseif $subnet_data.dhcp_kind=='nat'}
			<b>DHCP-NAT:</b> 
			{if $ip.dhcp_host==0 OR $ip.dhcp_netmask==0}
				Kein NAT-Netzwerk angegeben
			{else}
				{$net_prefix}.{$ip.dhcp_host}/{$ip.dhcp_netmask}
			{/if}
		{elseif $subnet_data.dhcp_kind=='no'}		
			<b>DHCP:</b> Projekt verbietet DHCP
		{/if}
		<br>
		<b>Benutzer:</b> <a href="./user.php?id={$ip.user_id}">{$ip.nickname}</a><br>
		<b>Angelegt am:</b> {$ip.create_date|date_format:"%e.%m.%Y %H:%M"} Uhr<br>

		{if !empty($service_data.last_online_crawl.luciname)}
			<h2>Konfiguration</h2>
			<p>
				<b>Email:</b> {$service_data.last_online_crawl.email}<br>
				<b>Hostname:</b> {$service_data.last_online_crawl.hostname}<br>
				<b>Prefix:</b> {$service_data.last_online_crawl.prefix}<br>
				<b>SSID:</b> {$service_data.last_online_crawl.ssid}<br>
			</p>
		{/if}

		{if !empty($service_data.last_online_crawl.chipset)}
			<h2>Hardware</h2>
			<p>

				<b>Chipset:</b> {$service_data.last_online_crawl.chipset}<br>
				<b>Cpu:</b> {$service_data.last_online_crawl.cpu}<br>
				<b>Memory:</b> {$service_data.last_online_crawl.memory_total} Kb<br>
			</p>
		{/if}

		{if !empty($service_data.last_online_crawl.luciname)}
			<h2>Software</h2>
			<p>
				<b>Luciname:</b> {$service_data.last_online_crawl.luciname}<br>
				<b>Luciversion:</b> {$service_data.last_online_crawl.luciversion}<br>
				<b>Distname:</b> {$service_data.last_online_crawl.distname}<br>
				<b>Distversion:</b> {$service_data.last_online_crawl.distversion}<br>
			</p>
		{/if}

		{if !empty($service_data.last_online_crawl.memory_total)}		
			<h2>Status</h2>
			<p>
				<b>Stability:</b> {$service_data.stability.percent}% ({$service_data.stability.online}/{$service_data.stability.gesammt}) online<br>
				<b>Free memory:</b> {$service_data.current_crawl.memory_free}/{$service_data.last_online_crawl.memory_total} Kb<br>
				<b>Loadaverage:</b> {$service_data.service_data.current_crawl.loadavg}<br>
				<b>Processes:</b> {$service_data.current_crawl.processes}<br>
				<b>Uptime:</b> {$service_data.current_crawl.uptime/60/60|round:1} Stunden<br>
				<b>Idletime:</b> {$service_data.current_crawl.idletime/60/60|round:1} Stunden<br>
			</p>
		{/if}
    </div>

    <div style="float:left; width: 50%;">
		{if !empty($service_data.last_online_crawl.longitude) AND !empty($service_data.last_online_crawl.latitude)}
			<h2>Standort (Hellblau markiert)</h2>
			<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
			<script src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.2&mkt=en-us"></script>
			
			<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
			
			<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
			<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>

			<div id="map" style="height:300px; width:300px; border:solid 1px black;font-size:9pt;">
				<script type="text/javascript">
					var lon = {$ip.longitude};
					var lat = {$ip.latitude};
					var radius = 30
					var zoom = 16;

					/* Initialize Map */
					ipmap({$service_data.service_id});
				</script>
			</div>
			{if !empty($ip.location)}
				<p><b>Standortbeschreibung:</b> {$ip.location}<br></p>
			{/if}
		{/if}



	<h2>Grafische Historie</h2>
	<p>
			<img src="./tmp/ip_{$ip.ip_id}_ping.png" width="380">
	</p>
	<p>
			<img src="./tmp/ip_{$ip.ip_id}_loadavg.png" width="380">
	</p>



<!--		{if !empty($service_data.current_crawl.olsrd_neighbors)}
			<h2>Benachbarte IP´s</h2>
			{assign var="tmp" value="2 Hop Neighbors"}
			{assign var="tmp2" value="IP address"}

			{foreach key=count item=olsrd_neighbors from=$service_data.current_crawl.olsrd_neighbors}
<!--		{if $olsrd_neighbors.$tmp eq '0'}<span style="background-color: yellow;">{/if}{if $olsrd_neighbors.netmon_ip_id}<a href="./ip.php?id={$olsrd_neighbors.netmon_ip_id}">{/if}{$olsrd_neighbors.$tmp2}{if $olsrd_neighbors.netmon_ip_id}</a>{/if} {if $olsrd_neighbors.$tmp eq '0'}(Direkter Client)</span>{/if}<br>-->
<!--		{if $olsrd_neighbors.netmon_is_client==true}<span style="background-color: yellow;">{/if}{if $olsrd_neighbors.netmon_ip_id}<a href="./ip.php?id={$olsrd_neighbors.netmon_ip_id}">{/if}{$olsrd_neighbors.$tmp2}{if $olsrd_neighbors.netmon_ip_id}</a>{/if} {if $olsrd_neighbors.netmon_is_client==true}(Direkter Client)</span>{/if}<br>
			{/foreach}
		{/if}-->



		{if !empty($service_data.olsrd_links)}
			<h2>Benachbarte IP´s</h2>

<div id="ipitem" style="width: 370px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 150px;"><b>Neighbour IP</b></div>
    <div style="float:left; width: 150px;"><b>Local interface IP</b></div>
    <div style="float:left; width: 70px;"><b>ETX</b></div>
  </div>
</div>

			{foreach item=olsrd_links from=$service_data.olsrd_links}
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
		{/if}


	</div>
</div>

<h2>Dienste auf dieser IP:</h2>
{foreach item=service from=$services}
<h3><a href="./service.php?service_id={$service.service_id}">{$service.services_title}</a> 

{if is_numeric($service.crawler)}<a target="_blank" href="{if $service.crawler=='80'}http://{elseif $service.crawler=='21'}ftp://{elseif $service.crawler=='8888'}http://{/if}{$net_prefix}.{$service.ip}:{if $service.crawler=='80'}80{elseif $service.crawler=='21'}21{elseif $service.crawler=='8888'}8888{/if}">{/if}
({$service.typ}:{$service.crawler})
{if is_numeric($service.crawler)}</a>{/if}


 (<a href="./serviceeditor.php?section=edit&service_id={$service.service_id}">Editieren</a>)</h3>
<ul>
	<li>
		<b>Beschreibung:</b> {$service.description}
	</li>
	<li>
		<b>Letzter Status{if !empty($service.current_crawl.crawl_time)} ({$service.current_crawl.crawl_time|date_format:"%H:%M"} Uhr){/if}:</b> 
    {if $service.current_crawl.status=="online"}
      <img src="./templates/img/ffmap/status_up_small.png" alt="online">
    {elseif $service.current_crawl.status=="offline"}
      <img src="./templates/img/ffmap/status_down_small.png" alt="offline">
    {elseif $service.current_crawl.status=="unbekannt"}
      <img src="./templates/img/ffmap/status_pending_small.png" alt="unbekannt">
    {/if}

	</li>
	<li>
<div style="width: 100%; overflow: hidden;">
    <div style="float:left; padding-right: 3px;">
		<b>Status Historie: </b> 
    </div>
	{if !empty($service.crawl_history)}
    <div style="float:left; width: 50%;">
<div style="width: 800px; overflow: hidden;">
{foreach key=count item=history from=$service.crawl_history}
    {if $history.status=="online"}
		<div style="float:left; width: 10px; height: 10pt; background-color: green; border-width: 1px; border-style:solid;">&nbsp;</div>
    {elseif $history.status=="offline"}
		<div style="float:left; width: 10px; height: 10pt; background-color: red; border-width: 1px; border-style:solid;">&nbsp;</div>
    {/if}
{/foreach}
</div>
</div>
	{else}
      <img src="./templates/img/ffmap/status_pending_small.png" alt="offline">
	{/if}
	</li>
	<li>
		<b>Angelegt am:</b> {$service.create_date|date_format:"%e.%m.%Y %H:%M"} Uhr
	</li>
</ul>
{/foreach}




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