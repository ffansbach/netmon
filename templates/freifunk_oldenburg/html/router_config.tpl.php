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
				<li>
					<b>IP-Adressen:</b>
					{foreach $interface->getIpList()->getIpList() as $ip}
						<ul>
							<li>
								<b>IPv{$ip->getIpv()} Adresse:</b> {$ip->getIp()}/{$ip->getNetmask()}<br>
							</li>
							<ul>
								<li>
									<b>Angelegt am:</b> {$ip->getCreateDate()|date_format:"%e.%m.%Y %H:%M"}
								</li>
							</ul>
						</ul>
					{/foreach}
				</li>
			</ul>
			
			</div>
			<div style="float:left; width: 50%;">
				<p>
					<a href="./ipeditor.php?section=add&router_id={$interface->getRouterId()}&interface_id={$interface->getNetworkinterfaceId()}">IP hinzufügen</a><br>
					<a href="./interface.php?section=delete&interface_id={$interface->getNetworkinterfaceId()}">Interface entfernen</a>
				</p>
			</div>
		</div>
		<hr>
	{/foreach}
{else}
	<p>Es sind keine Interfaces eingetragen</p>
{/if}

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