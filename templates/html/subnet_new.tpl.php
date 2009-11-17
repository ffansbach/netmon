<form action="./subneteditor.php?section=insert_new" method="POST">

	<h1>Ein neues Subnetz erstellen:</h1>
	<h2>Subnetz Bereich</h2>
	<p>
		<input type="radio" name="subnet_kind" value="extend" checked="checked" onchange="document.getElementById('simple').style.display = 'none'; document.getElementById('extend').style.display = 'block';">Extend<br>
		<input type="radio" name="subnet_kind" value="simple" onchange="document.getElementById('simple').style.display = 'block'; document.getElementById('extend').style.display = 'none';">Simple<br>
	</p>

	<div id="simple" style="display: none;">
		<h3>Einfache Bereichvergabe</h3>
		IP´s im Subnetz: <select name="ip_count">
			<option value="2">2 (/30)</option>
			<option value="6">6 (/29)</option>
			<option value="14">14 (/28)</option>
			<option value="30">30 (/27)</option>
			<option value="62">62 (/26)</option>
			<option value="126">126 (/25)</option>
			<option value="254">254 (/24)</option>
			<option value="510">510 (/23)</option>
			<option value="1022">1022 (/22)</option>
			<option value="2046">2046 (/21)</option>
			<option value="4094">4094 (/20)</option>
			<option value="8190">8190 (/19)</option>
			<option value="16382">16382 (/18)</option>
			<option value="32766">32766 (/17)</option>
			<option value="65534">65534 (/16)</option>
		</select>

	</div>
	<div id="extend" style="display: block;">
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 50%;">
				<h3>neues Netz</h2>
				<div style="width: 100%; overflow: hidden;">
					<div style="float:left;">
						Host:<br>
						<input name="host" type="text" size="15"> /
					</div>
					<div style="float:left; margin-left:3px;">
						Netmask:<br>
						<input name="netmask" type="text" size="5">
					</div>
				</div>
			</div>
			<div style="float:left; width: 50%;">
				<h3>Bereits existierende Subnetze</h3>
				<ul>
					{foreach item=existing_subnet from=$existing_subnets}
						<li>{$net_prefix}.{$existing_subnet.host}/{$existing_subnet.netmask}</li>
					{/foreach}
				</ul>
			</div>
		</div>
	</div>


	<h2>DHCP</h2>
	<p><input type="checkbox" name="allows_dhcp" value="true" checked="checked"> IP´s dürfen einen bestimmten IP-Bereich zum verteilen per DHCP reservieren.</p>
	
	<h2>Beschreibung</h2>
	<p>Titel:<br>
		<input name="title" type="text" size="50">
	</p>
	<p>Beschreibung:<br>
		<textarea name="description" cols="50" rows="10"></textarea>
	</p>

	<h2>Ort (ungefähre Lage des Subnetzes)</h2>
	<div style="width: 100%; overflow: hidden;">
		<div style="float:left; width: 55%;">
			<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
			<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
			<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
			<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
			
			<div id="map" style="height:200px; width:400px; border:solid 1px black;font-size:9pt;">
				<script type="text/javascript">
					newsubnet_map();
				</script>
			</div>
		</div>
		
		<div style="float:left; width: 45%;">
		<input  id="polygon_location" name="polygons" type="hidden" size="30">
		Klicken sie auf die Karte und markieren sie ein Polygon über dem ort des Subnetzes.<br>Ein Doppelklick beendet die Bearbeitung.
        </div>
	</div>
	
	
	<h2>VPN</h2>
	<p>
		<input type="radio" name="vpn_kind" value="no" checked="checked" onchange="document.getElementById('other').style.display = 'none'; document.getElementById('own').style.display = 'none';">Kein VPN-Server.<br>
		<input type="radio" name="vpn_kind" value="other" onchange="document.getElementById('other').style.display = 'block'; document.getElementById('own').style.display = 'none';">VPN-Server von anderem Subnetz nutzen.<br>
		<input type="radio" name="vpn_kind" value="own" onchange="document.getElementById('own').style.display = 'block'; document.getElementById('other').style.display = 'none';">Eigenen VPN-Server definieren.
	</p>

	<div id="other" style="display: none;">
		<h3>Übernehme VPN-Daten</h3>
		{if !empty($subnets_with_defined_vpnserver)}
			<p>VPN-Daten von Subnetz 
				<select name="vpnserver_from_project">
					{foreach item=subnet from=$subnets_with_defined_vpnserver}
						<option value="{$subnet.id}">{$net_prefix}.{$subnet.host}./{$subnet.netmask}</option>
					{/foreach}
				</select> übernehmen.
			</p>
		{else}
			<p>Keine Subnetze vorhanden, von denen Daten übernommen werden könnten</p>
		{/if}
	</div>
	
	<div id="own" style="display: none;">
		<h3>Daten zu eigenem VPN Server</h3>
		
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 40%;">
				Adresse/Ip:<br>
					<input name="vpn_server" type="text" size="35">
			</div>
			
			<div style="float:left; width: 60%;">
				Port:<br>
					<input name="vpn_server_port" type="text" size="5">
			</div>
		</div>
		
		
		<p>Protokoll:<br><input name="vpn_server_proto" type="text" size="5"></p>
		<p>Device:<br><input name="vpn_server_device" type="text" size="5"></p>
		
		<h3>Daten zu eigenen VPN-Zertifikaten</h3>
		<p>Server CA.CRT:<br><textarea name="vpn_server_ca" cols="50" rows="10">{$subnet_data.vpn_cacrt}</textarea></p>
		<p>Server Cert:<br><textarea name="vpn_server_cert" cols="50" rows="10">{$subnet_data.vpn_cacrt}</textarea></p>
		<p>Server Key<br><textarea name="vpn_server_key" cols="50" rows="10">{$subnet_data.vpn_cacrt}</textarea></p>
		
		<p>Passphrase:<br><input name="vpn_server_pass" type="password" size="30"></p>
	</div>

	<p><input type="submit" value="Absenden"></p>
</form>