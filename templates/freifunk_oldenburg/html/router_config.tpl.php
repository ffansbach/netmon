<script type="text/javascript">
	document.body.id='tab2';
</script>
<ul id="tabnav" style="float: left;">
	<li class="tab1"><a href="./router_status.php?router_id={$router_data.router_id}">Router Status</a></li>
	<li class="tab2"><a href="./router_config.php?router_id={$router_data.router_id}">Router Konfiguration</a></li>
</ul>

<ul id="tabnav" style="text-align: right; padding-right: 0px; margin-right: 0px;">
	<li><a style="background-color: #b1dbff; font-style:italic; color: #000000; position: relative; top: 1px; padding-top: 4px;" href="./routereditor.php?section=edit&router_id={$router_data.router_id}">Router editieren</a></li>
	<li><a style="background-color: #b1dbff; font-style:italic; color: #000000; position: relative; top: 1px; padding-top: 4px;" href="./interfaceeditor.php?section=add&router_id={$router_data.router_id}">Interface hinzufügen</a></li>
	{if $show_add_link}<li><a style="background-color: #b1dbff; font-style:italic; color: #000000; position: relative; top: 1px;" href="./addeditor.php?router_id={$smarty.get.router_id}">Werbung</a></li>{/if}
	<li><a style="background-color: #b1dbff; font-style:italic; color: #000000; position: relative; top: 1px; padding-top: 4px; border-right: 0px;" href="./serviceeditor.php?section=add&router_id={$router_data.router_id}">Dienst hinzufügen</a></li>
</ul>

<h1>Konfiguration des Routers {$router_data.hostname}</h1>

<div style="width: 100%; overflow: hidden;">
    <div style="float:left; width: 50%;">
		<h2>Grunddaten</h2>
		<b>Benutzer:</b> <a href="./user.php?user_id={$router_data.user_id}">{$router_data.nickname}</a><br>
		<b>Angelegt am:</b> {$router_data.create_date|date_format:"%e.%m.%Y %H:%M"} Uhr<br>
		{if !empty($router_data.description)}<b>Beschreibung:</b> {$router_data.description}<br>{/if}

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
		{if (!empty($router_data.latitude) AND !empty($router_data.longitude)) OR (!empty($router_last_crawl.latitude) AND !empty($router_last_crawl.longitude))}
		<script type="text/javascript" src='https://maps.googleapis.com/maps/api/js?key={$google_maps_api_key}&sensor=false'></script>
		<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
		<script type="text/javascript" src="./templates/{$template}/js/OpenStreetMap.js"></script>
		<script type="text/javascript" src="./templates/{$template}/js/OsmFreifunkMap.js"></script>

		<div id="map" style="height:300px; width:400px; border:solid 1px black;font-size:9pt;">
			<script type="text/javascript">
				var lat = {$router_data.latitude};
				var lon = {$router_data.longitude};

				var radius = 30
				var zoom = 16;

				/* Initialize Map */
				router_map({$router_data.router_id});
			</script>
		</div>
		<p><b>Standortbeschreibung:</b><br>
			Lat: {$router_data.latitude}
			Lon: {$router_data.longitude}

			<br><br>
			{$router_data.location}
		</p>
		{else}
			<p>Keine Standortdaten verfügbar</p>
		{/if}
	</div>
</div>

<h2>Interfaces</h2>
{if !empty($networkinterfacelist)}
	{foreach key=count item=interface from=$networkinterfacelist}
		<h3>{$interface->getName()}</h3>
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 50%;">
			<ul>
				<li>
					<b>Angelegt am:</b> {$interface->getCreateDate()|date_format:"%e.%m.%Y %H:%M"}
				</li>
			</ul>
			
				{foreach $interface->getIpList()->getIpList() as $iplist}
					<ul>
						<li>
							<b>IPv{$iplist->getIpv()} Adresse:</b> {$iplist->getIp()}<br>
						</li>
						<li>
							<b>Netmask:</b> {$iplist->getNetmask()}
						</li>
					</ul>
				{/foreach}
			
			</div>
			<div style="float:left; width: 50%;">
				<p>
					<a href="./ipeditor.php?section=add&router_id={$interface->getRouterId()}&interface_id={$interface->getNetworkinterfaceId()}">IP hinzufügen</a><br>
					<a href="./interfaceeditor.php?section=delete&interface_id={$interface->getNetworkinterfaceId()}">Interface entfernen</a>
				</p>
			</div>
		</div>
		<hr>
	{/foreach}
{else}
	<p>Es sind keine Interfaces eingetragen</p>
{/if}


<!--	<pre>
		{print_r($interface)}
	</pre>-->
<!--		<h3>{$interface->getName()}</h3>
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 50%;">
			<ul>
				<li>
					<b>Angelegt am:</b> {$interface.create_date|date_format:"%e.%m.%Y %H:%M"}
				</li>
			</ul>
				{foreach $interface.ip_addresses as $ip_address}
					{if $ip_address.ipv=='4'}
						<ul>
							<li>
								<b>IPv4 Adresse:</b> {$ip_address.ip} {if $network_connection_ipv6=='true'}(<a href="./ping_ip.php?ip_id={$ip_address.ip_id}">Ping</a>, <a href="./show_crawl_data.php?ip_id={$ip_address.ip_id}">Crawl Daten</a>){/if}
							</li>
							<li>
								<b>IPv4 Netmask:</b> {$interface.ipv4_netmask_dot}		
							</li>
							<li>
								<b>IPv4 Broadcast:</b> {$interface.ipv4_bcast}		
							</li>
							<li>
								<b>IPv4 DHCP:</b> {$interface.ipv4_dhcp_kind}		
							</li>
							{if $interface.ipv4_dhcp_kind=='range'}
								<li>
									<b>DHCP-Range:</b> {$interface.ipv4_dhcp_range_start} - {$interface.ipv4_dhcp_range_end}
								</li>
							{/if}
						</ul>
					{/if}
					{if $ip_address.ipv=='6'}
						<ul>
							<li>
								<b>IPv6 Adresse:</b> {$ip_address.ip} {if $network_connection_ipv4=='true'}(<a href="./ping_ip.php?ip_id={$ip_address.ip_id}">Ping</a>, <a href="./show_crawl_data.php?ip_id={$ip_address.ip_id}">Crawl Daten</a>){/if}
							</li>
						</ul>
					{/if}
					{if $ip_address.ipv=='no' OR empty($interface.ip_addresses)}
						<ul>
							<li>
								<b>IP Adresse:</b> Keine (Layer 2)
							</li>
						</ul>
					{/if}
					<li>
						<a href="./ipeditor.php?section=delete&ip_id={$ip_address.ip_id}&router_id={$router_data.router_id}">IP-Adresse entfernen</a>
					</li>
				{/foreach}
			
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
				<p>
					<a href="./ipeditor.php?section=add&router_id={$router_data.router_id}&interface_id={$interface.interface_id}">IP hinzufügen</a><br>
					<a href="./interfaceeditor.php?section=delete&interface_id={$interface.interface_id}">Interface entfernen</a>
				</p>
			</div>
		</div>-->

<h2>Dienste</h2>
{if !empty($services)}
	{foreach $services as $service}
		<a name="service_{$service.service_id}"></a>
		{if !empty($interfaces) AND !empty($service.url_prefix) AND !empty($service.port)}
			<a href="{$service.url_prefix}{$interfaces.0.ipv4_addr}:{$service.port}">
		{/if}
		<h3>{$service.title}</h3>
		{if !empty($interfaces) AND !empty($service.url_prefix) AND !empty($service.port)}
			</a>
		{/if}
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 50%;">
				<ul>
					<li>
						<b>Port: </b> {$service.port}
					</li>
					<li>
						<b>URL-Prefix: </b> {if empty($service.url_prefix)}Kein URL-Prefix{else}{$service.url_prefix}{/if}
					</li>
					<li>
						<b>Beschreibung: </b> {$service.description}
					</li>
				</ul>
			</div>
		<div style="float:left; width: 50%;">
		
		</div>
		<div style="float:left; width: 50%;">
			<p>
				<a href="./serviceeditor.php?section=edit&service_id={$service.service_id}">Dienst editieren</a><br>
				<a href="./serviceeditor.php?section=delete&service_id={$service.service_id}">Dienst entfernen</a>
			</p>
		</div>
	</div>
	<hr>
	{/foreach}
{else}
	<p>Es sind keine Dienste eingetragen</p>
{/if}