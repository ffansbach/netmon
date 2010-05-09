<script type="text/javascript">
	document.body.id='tab2';
</script>
<ul id="tabnav">
	<li class="tab1"><a href="./router_status.php?router_id={$router_data.router_id}">Router Status</a></li>
	<li class="tab2"><a href="./router_config.php?router_id={$router_data.router_id}">Router Konfiguration</a></li>
</ul>

<h1>Netmon Konfiguration des Routers {$router_data.hostname}</h1>

<div style="width: 100%; overflow: hidden;">
    <div style="float:left; width: 50%;">
		<h2>Grunddaten</h2>
		<b>Benutzer:</b> <a href="./user.php?id={$router_data.user_id}">{$router_data.nickname}</a><br>
		<b>Angelegt am:</b> {$router_data.create_date|date_format:"%e.%m.%Y %H:%M"} Uhr<br>

		<h2>Router Konfiguration</h2>

		<h3><u>Netzwerk</u></h3>
		<b>Hostname:</b> {$router_data.hostname}<br>

		<h3><u>Hardware</u></h3>
		<p>
			<b>Chipset:</b> {$router_data.chipset_name}<br>
		</p>

		<h3><u>Statusdaten</u></h3>
		<b>Datenquelle:</b> {if $router_data.crawl_method=='router'}Router sendet Daten{elseif $router_data.crawl_method=='crawler'}Netmon Crawler{/if}<br>
    </div>

      <div style="float:left; width: 50%;">
		<h2>Standort</h2>
		<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
		<script src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.2&mkt=en-us"></script>
		
		<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
		
		<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
		<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>

		<div id="map" style="height:300px; width:300px; border:solid 1px black;font-size:9pt;">
			<script type="text/javascript">
				var lat = {$router_data.latitude};
				var lon = {$router_data.longitude};

				var radius = 30
				var zoom = 16;

				/* Initialize Map */
				ipmap({$service_data.service_id});
			</script>
		</div>
		<p><b>Standortbeschreibung:</b><br>
			Lat: {$router_data.latitude}
			Lon: {$router_data.longitude}

			<br><br>
			{$router_data.location}
		</p>
	</div>
</div>

<h2>Interfaces</h2>
{foreach item=interface from=$interfaces}
<h3>{$interface.name}</h3>
<div style="width: 100%; overflow: hidden;">
    <div style="float:left; width: 50%;">
	{if $interface.ipv=='ipv4'}
	<ul>
		<li>
			<b>IPv4 Adresse:</b> {$net_prefix}.{$interface.ipv4_addr}		
		</li>
		<li>
			<b>IPv4 Netmask:</b> {$interface.ipv4_netmask_dot}		
		</li>
		<li>
			<b>IPv4 Broadcast:</b> {$interface.ipv4_bcast}		
		</li>
	</ul>
	{/if}
	{if $interface.ipv=='ipv6'}
	<ul>

		<li>
			<b>IPv6 Adresse:</b> {$interface.ipv6_addr}		
		</li>
	</ul>
	{/if}
	{if $interface.ipv=='no'}
	<ul>

		<li>
			<b>IP Adresse:</b> Keine (Layer 2)
		</li>
	</ul>
	{/if}
	
	{if $interface.is_wlan=='1'}	
	<ul>
		<li>
			<b>Channel:</b> {$interface.wlan_channel}		
		</li>
		<li>
			<b>ESSID:</b> {$interface.wlan_essid}		
		</li>
		<li>
			<b>BSSID:</b> {$interface.wlan_bssid}		
		</li>
	</ul>
	{/if}
	{if $interface.is_vpn=='1'}
	<ul>
		<li>
			<b>Server: </b> {$interface.vpn_server}
		</li>
		<li>
			<b>Device: </b> {$interface.vpn_server_device}
		</li>
		<li>
			<b>Protokoll: </b> {$interface.vpn_server_proto}
		</li>
	</ul>
	{/if}
	{if $interface.is_olsr=='1'}
	<ul>
		<li>
			<b>Olsr: </b> Ja
		</li>
	</ul>
	{/if}
	{if $interface.is_batman_adv=='1'}
	<ul>
		<li>
			<b>B.A.T.M.A.N advanced: </b> Ja
		</li>
	</ul>
	{/if}
	</div>
	<div style="float:left; width: 50%;">
		{if $interface.is_vpn=='1'}
			<p>
				<a href="./vpn.php?section=new&interface_id={$interface.interface_id}">VPN-Zertifikate generieren</a><br>
				<a href="./vpn.php?section=download&interface_id={$interface.interface_id}">VPN-Zertifikate und Config-Datei downloaden</a><br>
			</p>
		{/if}
	</div>
</div>	  
<hr>
{/foreach}


<p>
  <a href="./interfaceeditor.php?section=add&router_id={$router_data.router_id}">Interface hinzufügen</a>
</p>




<h2>Dienste</h2>

<p>
  <a href="./deviceeditor.php?section=add&router_id={$router_data.router_id}">Dienst hinzufügen</a>
</p>


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