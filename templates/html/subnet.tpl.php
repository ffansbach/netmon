<h1>Projekt {$subnet.title}</h1>
<div style="width: 100%; overflow: hidden;">
	<div style="float:left; width: 50%;">
		<h2>Daten:</h2>
		<p>
			<b>Subnet:</b> {$net_prefix}.{$subnet.host}/{$subnet.netmask}<br>
			{if !empty($subnet.real_host)}<b>Real Subnet:</b> {$net_prefix}.{$subnet.real_host}/{$subnet.real_netmask}<br>{/if}
			<b>Erste IP:</b> {$subnet.first_ip}<br>
			<b>Letzte IP:</b> {$subnet.last_ip}<br>
			<b>Broadcast IP:</b> {$subnet.broadcast}<br>
			<b>IP´s belegt/möglich</b> ka/{$subnet.hosts_total}<br>
		</p>
		<p>
			<b>DHCP Methode:</b> {if $subnet.dhcp_kind=='ips'}Ip´s verteilen IP´s
								 {elseif $subnet.dhcp_kind=='subnet'} IP´s verteilen IP´s per Subnet
								 {elseif $subnet.dhcp_kind=='nat'} Genattetes Subnet
								 {elseif $subnet.dhcp_kind=='no'} Kein DHCP erlaubt
								 {/if}
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
		<h2>Lokale Begrenzung des Projekts<h2>
		<!--<small style="font-size: 9pt;">Map Data from <a href="http://openstreetmap.org">OpenStreetMap</a></small>-->

		<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
		<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
		<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
		<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
		<div id="map" style="height:300px; width:300px; border:solid 1px black;font-size:9pt;">
		<script type="text/javascript">
			subnetmap();
			AddKmlLayer("Netzwerklocation", "./api.php?class=apiMap&section=getSubnetPolygons&subnet_id={$subnet.id}");
			AddKmlLayer("Verbindungen", "./api.php?class=apiMap&section=conn");
			AddKmlLayer("online and offline Nodes", "./api.php?class=apiMap&section=getOnlineAndOfflineServiceKML&highlighted_subnet={$subnet.id}");
		</script>
	</div>

{if $isOwner}
	<h2>Aktionen</h2>
		<p>
			<a href="./subneteditor.php?section=edit&id={$subnet.id}">Projekt editieren</a><br>
		</p>
{/if}

		</div>
	</div>

<h2>IP-Verteilung</h2>

<div style="width: 800px;">
{foreach item=status from=$ipstatus}
	<div style="float:left; overflow: hidden;">

			{if $status.type=="free"}
				<div style="width:90px; text-align: center; border: 0px; border-right: 1px; border-style: solid;">{$status.ip}</div>
				<div style="width:90px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #6cff84;">f</div>
			{elseif $status.type=="ip"}
				<div style="width:90px; text-align: center; border: 0px; border-right: 1px; border-style: solid;"><a href="./ip.php?id={$status.ip_id}">{$status.ip}</a></div>
		  		<div style="width:90px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #ff0000;">n</div>
			{elseif $status.type=="range"}
				<div style="width:90px; text-align: center; border: 0px; border-right: 1px; border-style: solid;"><a href="./ip.php?id={$status.ip_id}">{$status.range_ip}</a></div>
				<div style="width:90px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: #ff9448;">r</div>
			{elseif $status.type=="subnet_ip"}
				<div style="width:90px; text-align: center; border: 0px; border-right: 1px; border-style: solid;"><a href="./ip.php?id={$status.ip_id}">{$status.subnet_ip}</a></div>
				<div style="width:90px; text-align: center; border: 0px; border-right: 1px; border-bottom: 1px; border-style: solid; background: yellow;">r</div>
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
