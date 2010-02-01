{literal}
<script language="javascript">
	function check(){
		if(document.getElementById('use_real_network').checked == false){
			document.getElementById('real_net').style.display = 'none';
		} else {
			document.getElementById('real_net').style.display = 'block';
		}
	}

	function writeSame() {
			document.getElementById('real_host').value = document.getElementById('host').value;
	}
</script>
{/literal}

<form action="./subneteditor.php?section=insert_new" method="POST">

	<h1>Ein Projekt anlegen:</h1>
	<h2>Projekttyp</h2>
		<input type="radio" name="subnet_type" value="wlan" checked="checked" onChange="document.getElementById('vpn').style.display = 'none'; document.getElementById('wlan').style.display = 'block'; document.getElementById('dhcp_kind_ips').checked = true;">WLAN<br>
		<input type="radio" name="subnet_type" value="vpn" onChange="document.getElementById('vpn').style.display = 'block'; document.getElementById('wlan').style.display = 'none'; document.getElementById('dhcp_kind_no').checked = true;">VPN<br>

	<h2>Vergabe eines IP-Bereichs</h2>
	<p>
		<input type="radio" name="subnet_kind" value="simple" checked="checked" onchange="document.getElementById('simple').style.display = 'block'; document.getElementById('extend').style.display = 'none';">einfache Bereichvergabe<br>
		<input type="radio" name="subnet_kind" value="extend" onchange="document.getElementById('simple').style.display = 'none'; document.getElementById('extend').style.display = 'block';">erweiterte Bereichvergabe<br>
	</p>

	<div id="simple" style="display: block;">
		<h3>Einfache Bereichvergabe</h3>
		Mögliche IP´s im Bereich (/Netzmaske): <select name="only_netmask">
			<option value="30">2 (/30)</option>
			<option value="29">6 (/29)</option>
			<option value="28">14 (/28)</option>
			<option value="27">30 (/27)</option>
			<option value="26">62 (/26)</option>
			<option value="25">126 (/25)</option>
			<option value="24" selected>254 (/24)</option>
			<option value="23">510 (/23)</option>
			<option value="22">1022 (/22)</option>
			<option value="21">2046 (/21)</option>
			<option value="20">4094 (/20)</option>
			<option value="19">8190 (/19)</option>
			<option value="18">16382 (/18)</option>
			<option value="17">32766 (/17)</option>
			<option value="16">65534 (/16)</option>
		</select>
	</div>
	<div id="extend" style="display: none;">
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 50%;">
				<h3>neues Netz</h2>
				<input id="use_real_network" name="use_real_network" type="checkbox" onchange="check()" value="true"> Logisches Netz != Realnetz
				<div style="width: 100%; overflow: hidden;">
					<h4>Logisches Netz</h4>
					<div style="float:left;">
						Host:<br>
						10.18. <input id="host" name="host" type="text" size="7" maxlength="7" onchange="writeSame()"> /
					</div>
					<div style="margin-left:3px;">
						Netmask:<br>
						<input name="netmask" type="text" size="2" maxlength="2">
					</div>
					<div id="real_net" style="display: none;">
						<h4>Reales Netz</h4>
						<div style="float:left;">
							Host:<br>
							10.18. <input id="real_host" name="real_host" type="text" maxlength="7" size="7" readonly> /
						</div>
						<div style="margin-left:3px;">
							Netmask:<br>
							<input name="real_netmask" type="text" size="2" maxlength="2" value="16">
						</div>
					</div>
				</div>
			</div>
			<div style="float:left; width: 50%;">
				<h3>Bereits existierende Projekte</h3>
				{if !empty($existing_subnets)}
				<ul>
					{foreach item=existing_subnet from=$existing_subnets}
						<li>{$net_prefix}.{$existing_subnet.host}/{$existing_subnet.netmask}</li>
					{/foreach}
				</ul>
				{else}
					<p>Es existieren noch keine Projekte</p>
				{/if}
			</div>
		</div>
	</div>


	<h2>DHCP</h2>
	<p>
		<input id="dhcp_kind_ips" type="radio" name="dhcp_kind" value="ips" checked="checked">IP´s dürfen sich eine bestimmte Anzahl IP´s zum verteilen per DHCP reservieren.<br>
		<input id="dhcp_kind_subnet" type="radio" name="dhcp_kind" value="subnet">IP´s dürfen sich ein Subnetz zum verteilen von IP´s reservieren<br>
		<input id="dhcp_kind_nat" type="radio" name="dhcp_kind" value="nat">IP´s nutzen ein eigenes genattetes Subnetz zum verteilen von IP´s<br>
		<input id="dhcp_kind_no" type="radio" name="dhcp_kind" value="no">IP´s dürfen keine IP´s per DHCP verteilen<br>
	</p>
	
	<h2>Beschreibung</h2>
	<p>Projektname:<br>
		<input name="title" type="text" size="50">
	</p>
	<p>Projektbeschreibung:<br>
		<textarea name="description" cols="57" rows="3"></textarea>
	</p>

	<p>DNS Server (durch Leerzeichen getrennt):<br>
		<input name="dns_server" type="text" size="50">

	<div id="wlan" style="display:block">
	<p>Projekt SSID:<br>
		<input name="essid" type="text" size="20">
	<p>Projekt BSSID:<br>
		<input name="bssid" type="text" size="20">
	<p>Projekt Wlan-Kanal: <select name="channel">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6" selected>6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
			<option value="13">13</option>
		</select>
	</p>
	</div>
	<p>Externe Projektseite:<br>
		<input name="website" type="text" size="20">

	<h2>Lokale Begrenzung des Projekts</h2>
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
		Sie können das Projekt lokal eingenzen indem Sie auf die Karte Klicken und den Bereich des Projektes abstecken.<br>Ein Doppelklick beendet die Bearbeitung.
        </div>
	</div>
	
	<div id="vpn" style="display:none">
	<h2>VPN</h2>
	<p>
		<input type="radio" name="vpn_kind" value="own" onchange="document.getElementById('own').style.display = 'block'; document.getElementById('other').style.display = 'none';" checked>Eigener VPN-Server<br>
		<input type="radio" name="vpn_kind" value="other" onchange="document.getElementById('other').style.display = 'block'; document.getElementById('own').style.display = 'none';">VPN-Daten von anderem Projekt nutzen
	</p>

	<div id="other" style="display: none;">
		<h3>Übernehme VPN-Daten</h3>
		{if !empty($subnets_with_defined_vpnserver)}
			<p>VPN-Daten vom Projekt 
				<select name="vpnserver_from_project">
					{foreach item=subnet from=$subnets_with_defined_vpnserver}
						<option value="{$subnet.id}">{$net_prefix}.{$subnet.host}./{$subnet.netmask}</option>
					{/foreach}
				</select> übernehmen.
			</p>
		{else}
			<p>Keine Projekte vorhanden, von denen Daten übernommen werden könnten</p>
		{/if}
	</div>
	
	<div id="own" style="display: block;">
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
		<p>Server CA.CRT:<br><textarea name="vpn_server_ca" cols="50" rows="10"></textarea></p>
		<p>Server Cert:<br><textarea name="vpn_server_cert" cols="50" rows="10"></textarea></p>
		<p>Server Key:<br><textarea name="vpn_server_key" cols="50" rows="10"></textarea></p>
		
		<p>Passphrase:<br><input name="vpn_server_pass" type="password" size="30"></p>

		<h3>Synchronisation der CCD Daten per FTP zum VPN-Server</h3>
		<input type="radio" name="ftp_sync" value="0" checked="checked" onChange="document.getElementById('ftp_sync_data').style.display = 'none';">VPN-Server greift auf CCD-Verzeichnis von Netmon zu<br>
		<input type="radio" name="ftp_sync" value="1" onChange="document.getElementById('ftp_sync_data').style.display = 'block';">Netmon Synchronisiert CCD Ordner per FTP mit einem Ordner auf dem VPN-Server<br>
		<div id="ftp_sync_data" style="display:none">
			<p>Ordner:<br><input name="ftp_ccd_folder" type="text" size="30"></p>
			<p>Benutzername:<br><input name="ftp_ccd_username" type="text" size="20"></p>
			<p>Passwort:<br><input name="ftp_ccd_password" type="password" size="20"></p>
		</div>
	</div>
	</div>

	<p><input type="submit" value="Absenden"></p>
</form>