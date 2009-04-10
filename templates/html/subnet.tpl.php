<h1>Subnet {$net_prefix}.{$subnet.subnet_ip}, {$subnet.title}</h1>

<div style="float:left; width: 40%;">

<h2>Daten:</h2>

VPN-Server: {if isset($subnet.vpn_server)}{$subnet.vpn_server} auf Port {$subnet.vpn_server_port}{else}Kein VPN-Server eingetragen{/if}<br>
Daten zu VPN: {if isset($subnet.vpn_server)} Device {$subnet.vpn_server_device}, Protokoll {$subnet.vpn_server_proto}{else}Kein VPN-Server eingetragen{/if}<br>
<br>
Verantwortlicher: <a href="./index.php?get=user&id={$subnet.created_by}">{$subnet.nickname}</a><br>
Eingetragen seit: {$subnet.create_date}<br>

<h2>Beschreibung:</h2>
<p>{$subnet.description}</p>
</div>

<div style="padding-left: 50%;">
	<h2>Radius des Netzes<h2>
	<!--<small style="font-size: 9pt;">Map Data from <a href="http://openstreetmap.org">OpenStreetMap</a></small>-->

	<script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
	<script type="text/javascript" src="http://openstreetmap.org/openlayers/OpenStreetMap.js"></script>
	<script type="text/javascript" src="http://map.freifunk-ol.de/pymap/Freifunk.js"></script>
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
</div>

<h2>IP-Verteilung</h2>

<p>Rot "n" = Node<br>
Orange "r" = Range<br>
Grün "f" = Frei</p>

<div style="width: 1000px;">

{foreach key=key item=status from=$ipstatus}
	<div style="float:left;">

			{if $status.typ=="free"}
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-style: solid;">{$status.ip}</div>
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #6cff84;">f</div>
			{elseif $status.typ=="node"}
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-style: solid;"><a href="./index.php?get=node&id={$status.node_id}">{$status.ip}</a></div>
		  		<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #ff0000;">n</div>
			{elseif $status.typ=="range"}
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-style: solid;"><a href="./index.php?get=service&service_id={$status.service_id}">{$status.ip}</a></div>
				<div style="width:35px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #ff9448;">r</div>
			{/if}

	</div>
{/foreach}

</div>
<br style="clear:both;">

</div>
