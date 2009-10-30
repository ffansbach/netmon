<h1>Subnet {$net_prefix}.{$subnet.host}/{$subnet.netmask}, {$subnet.title}</h1>
<div style="width: 100%; overflow: hidden;">
	<div style="float:left; width: 50%;">
		<h2>Daten:</h2>
		<p>
			<b>Subnet:</b> {$net_prefix}.{$subnet.host}/{$subnet.netmask}<br>
			<b>Erste IP:</b> {$subnet.first_ip}<br>
			<b>Letzte IP:</b> {$subnet.last_ip}<br>
			<b>IP´s belegt/möglich</b> ka/{$subnet.hosts_total}<br>
		</p>
		<p>
			<b>DHCP erlaubt:</b> {if $subnet.allows_dhcp}Ja{else}Nein{/if}
		<p>
			<b>VPN-Server:</b> {if !empty($subnet.vpn_server)}{$subnet.vpn_server}:{$subnet.vpn_server_port}{else}Kein VPN-Server eingetragen{/if}<br>
			<b>VPN-Device:</b> {if !empty($subnet.vpn_server)}{$subnet.vpn_server_device}{else}Kein VPN-Server eingetragen{/if}<br>
			<b>VPN-Protokoll:</b> {if !empty($subnet.vpn_server)}{$subnet.vpn_server_proto}{else}Kein VPN-Server eingetragen{/if}
		</p>
		<p>
			<b>Administrator:</b> <a href="./user.php?id={$subnet.user_id}">{$subnet.nickname}</a><br>
			<b>Angelegt am:</b> {$subnet.create_date}
		</p>

<h2>Beschreibung</h2>
<p>{$subnet.description}</p>
		</div>
		
		<div style="float:left; width: 50%;">
	<h2>Ort des Subnetzes<h2>
	<!--<small style="font-size: 9pt;">Map Data from <a href="http://openstreetmap.org">OpenStreetMap</a></small>-->

		<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
		<script type="text/javascript" src="./templates/js/OpenLayers.js"></script>
		<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
		<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
	<div id="map" style="height:300px; width:300px; border:solid 1px black;font-size:9pt;">
		<script type="text/javascript">
			var lon = {$subnet.longitude};
			var lat = {$subnet.latitude};
			var radius = {$subnet.radius}
			var zoom = 11;

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
				SubnetLayer("Subnet", lon, lat, radius);
			{/literal}
		</script>
	</div>

{if $isOwner}
	<h2>Aktionen</h2>
		<p>
			<a href="./subneteditor.php?section=edit&id={$subnet.id}">Subnetz editieren</a><br>
		</p>
{/if}

		</div>
	</div>

<h2>IP-Verteilung</h2>

<div style="width: 800px;">
{foreach key=key item=status from=$ipstatus}
	<div style="float:left;">

			{if $status.typ=="free"}
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-style: solid;">{$status.ip}</div>
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #6cff84;">f</div>
			{elseif $status.typ=="ip"}
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-style: solid;"><a href="./ip.php?id={$status.ip_id}">{$status.ip}</a></div>
		  		<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #ff0000;">n</div>
			{elseif $status.typ=="range"}
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-style: solid;"><a href="./ip.php?id={$status.belonging_ip_id}">{$status.range_ip}</a></div>
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #ff9448;">r</div>
			{/if}

	</div>
{/foreach}
</div>

<br style="clear:both;">

<h3>Legende</h3>

<span style="padding: 3px; text-align: center; background: #6cff84;">IP ist frei</span>
<span style="padding: 3px; text-align: center; background: #ff0000;">IP ist belegt</span>
<span style="padding: 3px; text-align: center; background: #ff9448;">IP wird per DHCP vergeben</span>

</div>
