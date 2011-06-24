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

<form action="./projecteditor.php?section=insert_new" method="POST">

	<h1>Ein neues Projekt anlegen:</h1>
	<h2>Beschreibung</h2>
	<p>Projektname:<br>
		<input name="title" type="text" size="50">
	</p>
	<p>Projektbeschreibung:<br>
		<textarea name="description" cols="57" rows="3"></textarea>
	</p>

	<h2>Projekt Einstellungen</h2>
	<h3>Infrastruktur</h3>
{literal}
	<input type="checkbox" id="is_wlan" name="is_wlan" value="1" onChange="if(document.getElementById('is_wlan').checked) {document.getElementById('wlan').style.display = 'block';} else {document.getElementById('wlan').style.display = 'none';}">Diese Projekt ist ein Wlan Projekt<br>

	<div id="wlan" style="display:none;">
	<h4>Wlan Einstellungen</h4>
	<p>WLAN ESSID:<br>
		<input name="wlan_essid" type="text" size="20">
	<p>Projekt BSSID:<br>
		<input name="wlan_bssid" type="text" size="20">
	<p>Projekt Wlan-Kanal: <select name="wlan_channel">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
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

	<input type="checkbox" id="is_vpn" name="is_vpn" value="1" onChange="if(document.getElementById('is_vpn').checked) {document.getElementById('vpn').style.display = 'block';} else {document.getElementById('vpn').style.display = 'none';}">Diese Projekt ist ein VPN Projekt<br>

	<div id="vpn" style="display:none">
	<h4>VPN Einstellungen</h4>
		<h5>Daten zu eigenem VPN Server</h5>
		
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
		
		<h5>Daten zu eigenen VPN-Zertifikaten</h5>
		<p>Server CA.CRT:<br><textarea name="vpn_server_ca_crt" cols="50" rows="10"></textarea></p>
		<p>Server CA.KEY:<br><textarea name="vpn_server_ca_key" cols="50" rows="10"></textarea></p>
		
		<p>Passphrase:<br><input name="vpn_server_pass" type="password" size="30"></p>

		<h5>Synchronisation der CCD Daten per FTP zum VPN-Server</h5>
		<input type="radio" name="is_ccd_ftp_sync" value="0" checked="checked" onChange="document.getElementById('ftp_sync_data').style.display = 'none';">VPN-Server greift auf CCD-Verzeichnis von Netmon zu<br>
		<input type="radio" name="is_ccd_ftp_sync" value="1" onChange="document.getElementById('ftp_sync_data').style.display = 'block';">Netmon Synchronisiert CCD Ordner per FTP mit einem Ordner auf dem VPN-Server<br>
		<div id="ftp_sync_data" style="display:none">
			<p>Ordner:<br><input name="ccd_ftp_folder" type="text" size="30"></p>
			<p>Benutzername:<br><input name="ccd_ftp_username" type="text" size="20"></p>
			<p>Passwort:<br><input name="ccd_ftp_password" type="password" size="20"></p>
		</div>
	</div>

	<h3>Routing</h3>
	<input type="checkbox" id="is_batman_adv" name="is_batman_adv" value="1"> Diese Projekt ist ein B.A.T.M.A.N advanced Projekt<br>

	<input type="checkbox" id="is_olsr" name="is_olsr" value="1"> Diese Projekt ist ein Olsr Projekt<br>

	<h3>IP Einstellungen</h3>
{/literal}
{literal}
	<input type="checkbox" id="is_ipv4" name="is_ipv4" value="1" onChange="if(document.getElementById('is_ipv4').checked) {document.getElementById('ipv4').style.display = 'block';} else {document.getElementById('ipv4').style.display = 'none';}">Diese Projekt ist ein IPv4 Projekt<br>

	<div id="ipv4" style="display:none">
	<h4>IPv4 Einstellungen</h4>
	<p>Subnet Host:<br> <input name="ipv4_host" type="text" size="15"></p>
	<p>Subnet Netmask <select name="ipv4_netmask">
				<option value="28">28</option>
				<option value="27">27</option>
				<option value="26">26</option>
				<option value="25">25</option>
				<option value="24" selected="selected">24</option>
				<option value="23">23</option>
				<option value="22">22</option>
				<option value="21">21</option>
				<option value="20">20</option>
				<option value="19">19</option>
				<option value="18">18</option>
				<option value="17">17</option>
				<option value="16">16</option>
			    </select></p>

	<p>Subnet DHCP <select name="ipv4_dhcp_kind">
				<option value="no">Kein DHCP</option>
				<option value="range">DHCP Bereich</option>
				<option value="nat">Genattetes Subnet</option>
			    </select></p>
	</div>

	<input type="checkbox" id="is_ipv6" name="is_ipv6" value="1" onChange="if(document.getElementById('is_ipv6').checked) {document.getElementById('ipv6').style.display = 'block';} else {document.getElementById('ipv6').style.display = 'none';}">Diese Projekt ist ein IPv6 Projekt<br>



	<h3>Sonstiges</h3>
	<input type="checkbox" id="is_geo_specific" name="is_geo_specific" value="1" onChange="if(document.getElementById('is_geo_specific').checked) {document.getElementById('geo_specific').style.display = 'block';} else {document.getElementById('geo_specific').style.display = 'none';}">Diese Projekt ist lokal begrenzt<br>
	<div id="geo_specific" style="display:none">
	<h4>Lokale Begrenzung des Projekts</h4>
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 55%;">
				<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
				<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
				<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
				<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
				
				<div id="map" style="height:200px; width:400px; border:solid 1px black;font-size:9pt;">
					<script type="text/javascript">
						newproject_map();
					</script>
				</div>
			</div>
			<div style="float:left; width: 45%;">
				<input  id="polygon_location" name="geo_polygons" type="hidden">
				Sie können das Projekt lokal eingenzen indem Sie auf die Karte Klicken und den Bereich des Projektes abstecken.<br>Ein Doppelklick beendet die Bearbeitung.
		        </div>
		</div>
	</div>
{/literal}
	<p>DNS Server (durch Leerzeichen getrennt):<br>
		<input name="dns_server" type="text" size="50">

	<p>Externe Projektseite:<br>
		<input name="website" type="text" size="20">

	<p><input type="submit" value="Absenden"></p>
</form>