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

<form action="./subneteditor.php?section=insert_edit&id={$subnet_data.id}" method="POST">

	<h1>Ein Projekt anlegen:</h1>
	<h2>Projekttyp</h2>
		<input type="radio" name="subnet_type" value="wlan" checked="checked" onChange="document.getElementById('vpn').style.display = 'none'; document.getElementById('wlan').style.display = 'block'; document.getElementById('dhcp_kind_ips').checked = true;" {if $subnet_data.subnet_type=='wlan'}checked{/if}>WLAN<br>
		<input type="radio" name="subnet_type" value="vpn" onChange="document.getElementById('vpn').style.display = 'block'; document.getElementById('wlan').style.display = 'none'; document.getElementById('dhcp_kind_no').checked = true;" {if $subnet_data.subnet_type=='vpn'}checked{/if}>VPN<br>


	<h2>Vergabe eines IP-Bereichs</h2>
	<p>
		<input type="radio" name="subnet_kind" value="simple" onchange="document.getElementById('simple').style.display = 'block'; document.getElementById('extend').style.display = 'none';" disabled>einfache Bereichvergabe<br>
		<input type="radio" name="subnet_kind" value="extend" checked="checked" onchange="document.getElementById('simple').style.display = 'none'; document.getElementById('extend').style.display = 'block';">erweiterte Bereichvergabe<br>
	</p>

	<div id="simple" style="display: none;">
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
	<div id="extend" style="display: block;">
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 50%;">
				<h3>neues Netz</h2>
				<input id="use_real_network" name="use_real_network" type="checkbox" onchange="check()" value="true"> Logisches Netz != Realnetz
				<div style="width: 100%; overflow: hidden;">
					<h4>Logisches Netz</h4>
					<div style="float:left;">
						Host:<br>
						10.18. <input id="host" name="host" type="text" size="7" maxlength="7" onchange="writeSame()" value="{$subnet_data.host}"> /
					</div>
					<div style="margin-left:3px;">
						Netmask:<br>
						<input name="netmask" type="text" size="2" maxlength="2" value="{$subnet_data.netmask}">
					</div>
					<div id="real_net" style="display: none;">
						<h4>Reales Netz</h4>
						<div style="float:left;">
							Host:<br>
							10.18. <input id="real_host" name="real_host" type="text" maxlength="7" size="7" value="{$subnet_data.real_host}"> /
						</div>
						<div style="margin-left:3px;">
							Netmask:<br>
							<input name="real_netmask" type="text" size="2" maxlength="2" value="{$subnet_data.real_host}">
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
		<input id="dhcp_kind_ips" type="radio" name="dhcp_kind" value="ips" {if $subnet_data.dhcp_kind=='ips'}checked="checked"{/if}>IP´s dürfen sich eine bestimmte Anzahl IP´s zum verteilen per DHCP reservieren.<br>
		<input id="dhcp_kind_subnet" type="radio" name="dhcp_kind" value="subnet" {if $subnet_data.dhcp_kind=='subnet'}checked="checked"{/if}>IP´s dürfen sich ein Subnetz zum verteilen von IP´s reservieren<br>
		<input id="dhcp_kind_nat" type="radio" name="dhcp_kind" value="nat" {if $subnet_data.dhcp_kind=='nat'}checked="checked"{/if}>IP´s nutzen ein eigenes genattetes Subnetz zum verteilen von IP´s<br>
		<input id="dhcp_kind_no" type="radio" name="dhcp_kind" value="no" {if $subnet_data.dhcp_kind=='no'}checked="checked"{/if}>IP´s dürfen keine IP´s per DHCP verteilen<br>
	</p>
	
	<h2>Beschreibung</h2>
	<p>Projektname:<br>
		<input name="title" type="text" size="50" value="{$subnet_data.title}">
	</p>
	<p>Projektbeschreibung:<br>
		<textarea name="description" cols="57" rows="3">{$subnet_data.description}</textarea>
	</p>

	<p>DNS Server (durch Leerzeichen getrennt):<br>
		<input name="dns_server" type="text" size="50" value="{$subnet_data.dns_server}">

	<div id="wlan" style="{if $subnet_data.subnet_type=='wlan'}display:block{else}display:none{/if}">
	<p>Projekt SSID:<br>
		<input name="essid" type="text" size="20" value="{$subnet_data.essid}">
	<p>Projekt BSSID:<br>
		<input name="bssid" type="text" size="20" value="{$subnet_data.bssid}">
	<p>Projekt Wlan-Kanal: <select name="channel">
			<option value="1" {if $subnet_data.channel=='1'}selected{/if}>1</option>
			<option value="2" {if $subnet_data.channel=='2'}selected{/if}>2</option>
			<option value="3" {if $subnet_data.channel=='3'}selected{/if}>3</option>
			<option value="4" {if $subnet_data.channel=='4'}selected{/if}>4</option>
			<option value="5" {if $subnet_data.channel=='5'}selected{/if}>5</option>
			<option value="6" {if $subnet_data.channel=='6'}selected{/if}>6</option>
			<option value="7" {if $subnet_data.channel=='7'}selected{/if}>7</option>
			<option value="8" {if $subnet_data.channel=='8'}selected{/if}>8</option>
			<option value="9" {if $subnet_data.channel=='9'}selected{/if}>9</option>
			<option value="10" {if $subnet_data.channel=='10'}selected{/if}>10</option>
			<option value="11" {if $subnet_data.channel=='11'}selected{/if}>11</option>
			<option value="12" {if $subnet_data.channel=='12'}selected{/if}>12</option>
			<option value="13" {if $subnet_data.channel=='13'}selected{/if}>13</option>
		</select>
	</p>
	</div>
	<p>Externe Projektseite:<br>
		<input name="website" type="text" size="20" value='{$subnet_data.website}'>

	<h2>Lokale Begrenzung des Projekts</h2>
	<div style="width: 100%; overflow: hidden;">
		<div style="float:left; width: 55%;">
			<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
			<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
			<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
			<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
			
			<div id="map" style="height:200px; width:400px; border:solid 1px black;font-size:9pt;">
				<script type="text/javascript">
					newsubnet_map();
					AddKmlLayer("Netzwerklocation", "./api.php?class=apiMap&section=getSubnetPolygons&subnet_id={$subnet_data.id}");
				</script>
			</div>
		</div>
		
		<div style="float:left; width: 45%;">
		<input  id="polygon_location" name="polygons" type="hidden" size="30" value='{$subnet_data.polygons}'>
		Sie können das Projekt lokal eingenzen indem Sie auf die Karte Klicken und den Bereich des Projektes abstecken.<br>Ein Doppelklick beendet die Bearbeitung.
        </div>
	</div>
	
	<div id="vpn" style="{if $subnet_data.subnet_type=='vpn'}display:block{else}display:none{/if}">
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
					<input name="vpn_server" type="text" size="35" value="{$subnet_data.vpn_server}">
			</div>
			
			<div style="float:left; width: 60%;">
				Port:<br>
					<input name="vpn_server_port" type="text" size="5" value="{$subnet_data.vpn_server_port}">
			</div>
		</div>
		
		
		<p>Protokoll:<br><input name="vpn_server_proto" type="text" size="5" value="{$subnet_data.vpn_server_proto}"></p>
		<p>Device:<br><input name="vpn_server_device" type="text" size="5" value="{$subnet_data.vpn_server_device}"></p>
		
		<h3>Daten zu eigenen VPN-Zertifikaten</h3>
		<p>Server CA.CRT:<br><textarea name="vpn_server_ca" cols="50" rows="10">{$subnet_data.vpn_server_ca}</textarea></p>
		<p>Server Cert:<br><textarea name="vpn_server_cert" cols="50" rows="10">{$subnet_data.vpn_server_key}</textarea></p>
		<p>Server Key:<br><textarea name="vpn_server_key" cols="50" rows="10">{$subnet_data.vpn_server_ca}</textarea></p>
		
		<p>Passphrase:<br><input name="vpn_server_pass" type="password" size="30"></p>

		<h3>Synchronisation der CCD Daten per FTP zum VPN-Server</h3>
		<input type="radio" name="ftp_sync" value="0" onChange="document.getElementById('ftp_sync_data').style.display = 'none';" {if $subnet_data.ftp_sync=='0'}checked="checked"{/if}>VPN-Server greift auf CCD-Verzeichnis von Netmon zu<br>
		<input type="radio" name="ftp_sync" value="1" onChange="document.getElementById('ftp_sync_data').style.display = 'block';" {if $subnet_data.ftp_sync=='1'}checked="checked"{/if}>Netmon Synchronisiert CCD Ordner per FTP mit einem Ordner auf dem VPN-Server<br>
		<div id="ftp_sync_data" style="{if $subnet_data.ftp_sync=='1'}display:block{else}display:none{/if}">
			<p>Ordner:<br><input name="ftp_ccd_folder" type="text" size="30" value="{$subnet_data.ftp_ccd_folder}"></p>
			<p>Benutzername:<br><input name="ftp_ccd_username" type="text" size="20" value="{$subnet_data.ftp_ccd_username}"></p>
			<p>Passwort:<br><input name="ftp_ccd_password" type="password" size="20" value="{$subnet_data.ftp_ccd_password}"></p>
		</div>
	</div>
	</div>

	<p><input type="submit" value="Absenden"></p>
</form>

<form action="./subneteditor.php?section=delete&subnet_id={$subnet_data.id}" method="POST">
  <h2>Subnet Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <p><input type="submit" value="Löschen!"></p>
</form>