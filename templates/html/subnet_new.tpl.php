<form action="./subneteditor.php?section=insert" method="POST">

	<h1>Ein neues Subnetz erstellen:</h1>
	<h2>Daten zum Netz</h2>
	<p>Neues Subnetz wählen:
		<select name="subnet_ip">
			{foreach item=subnet from=$avalailable_subnets}
				<option value="{$subnet}">{$net_prefix}.{$subnet}.0/24</option>
			{/foreach}
		</select>
	</p>
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
		<div style="float:left; width: 45%;">
			<p>Länge:<br><input name="longitude" type="text" size="30"></p>
			<p>Breite:<br><input name="latitude" type="text" size="30"></p>
			<p>Radius (in Metern):<br><input name="radius" type="text" size="30"></p>
		</div>
		
		<div style="float:left; width: 55%;">
			<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
			<script type="text/javascript" src="./templates/js/OpenLayers.js"></script>
			<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
			<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
			
			<div id="map" style="height:200px; width:400px; border:solid 1px black;font-size:9pt;">
				<script type="text/javascript">
					new_subnet();
				</script>
			</div>
		</div>
	</div>
	
	
	<h2>VPN</h2>
	<p>
		<input type="radio" name="vpn_kind" value="no" checked="checked">Kein VPN-Server.<br>
		<input type="radio" name="vpn_kind" value="other" onchange="document.getElementById('other').style.display = 'block'; document.getElementById('own').style.display = 'none';">VPN-Server von anderem Subnetz nutzen.<br>
		<input type="radio" name="vpn_kind" value="own" onchange="document.getElementById('own').style.display = 'block'; document.getElementById('other').style.display = 'none';">Eigenen VPN-Server definieren.
	</p>

	<div id="other" style="display: none;">
		<h3>Übernehme VPN-Daten</h3>
		<p>VPN-Daten von Subnetz 
			<select name="vpnserver_from_project">
				{foreach item=subnet from=$subnets_with_defined_vpnserver}
					<option value="{$subnet.id}">{$net_prefix}.{$subnet.subnet_ip}.0/24</option>
				{/foreach}
			</select> übernehmen.
		</p>
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