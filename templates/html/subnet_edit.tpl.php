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
	<h1>Subnetz editieren:</h1>
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
				<input id="use_real_network" name="use_real_network" type="checkbox" onchange="check()" value="true" {if !empty($subnet_data.real_host)}checked{/if}> Logisches Netz != Realnetz
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
					<div id="real_net" style="{if empty($subnet_data.real_host)}display: none;{else}display: block;{/if}">
						<h4>Reales Netz</h4>
						<div style="float:left;">
							Host:<br>
							10.18. <input id="real_host" name="real_host" type="text" maxlength="7" size="7" value="{if empty($subnet_data.real_host)}{$subnet_data.host}{else}{$subnet_data.real_host}{/if}" readonly> /
						</div>
						<div style="margin-left:3px;">
							Netmask:<br>
							<input name="real_netmask" type="text" size="2" maxlength="2" value="{if empty($subnet_data.real_host)}16{else}{$subnet_data.real_netmask}{/if}">
						</div>
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
	<p>
		<input type="radio" name="dhcp_kind" value="ips" {if $subnet_data.dhcp_kind=='ips'}checked{/if}>IP´s dürfen sich eine bestimmte Anzahl IP´s zum verteilen per DHCP reservieren.<br>
		<input type="radio" name="dhcp_kind" value="subnet" {if $subnet_data.dhcp_kind=='subnet'}checked{/if}>IP´s dürfen sich ein Subnetz zum verteilen von IP´s reservieren<br>
		<input type="radio" name="dhcp_kind" value="nat" {if $subnet_data.dhcp_kind=='nat'}checked{/if}>IP´s nutzen ein eigenes genattetes Subnetz zum verteilen von IP´s<br>
		<input type="radio" name="dhcp_kind" value="no" {if $subnet_data.dhcp_kind=='no'}checked{/if}>IP´s dürfen keine IP´s per DHCP verteilen<br>
	</p>
	
	<h2>Beschreibung</h2>
	<p>Titel:<br>
		<input name="title" type="text" size="50" value="{$subnet_data.title}">
	</p>
	<p>Beschreibung:<br>
		<textarea name="description" cols="50" rows="10">{$subnet_data.description}</textarea>
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
					AddKmlLayer("Bisherige Netzwerklocation", "./api.php?class=apiMap&section=getSubnetPolygons&subnet_id={$subnet_data.id}");
				</script>
			</div>
		</div>
		
		<div style="float:left; width: 45%;">
		<input  id="polygon_location" name="polygons" type="hidden" value='{$subnet_data.polygons}'>
		Klicken sie auf die Karte und markieren sie ein Polygon über dem ort des Subnetzes.<br>Ein Doppelklick beendet die Bearbeitung.
        </div>
	</div>
	
	
	<h2>VPN</h2>
	<p>
		<input type="radio" name="vpn_kind" value="no" {if empty($subnet_data.vpn_server)}checked="checked"{/if} onchange="document.getElementById('other').style.display = 'none'; document.getElementById('own').style.display = 'none';">Kein VPN-Server.<br>
		<input type="radio" name="vpn_kind" value="other" onchange="document.getElementById('other').style.display = 'block'; document.getElementById('own').style.display = 'none';">VPN-Server von anderem Subnetz nutzen.<br>
		<input type="radio" name="vpn_kind" value="own" {if !empty($subnet_data.vpn_server)}checked="checked"{/if} onchange="document.getElementById('own').style.display = 'block'; document.getElementById('other').style.display = 'none';">Eigenen VPN-Server definieren.
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
	
	<div id="own" style="{if !empty($subnet_data.vpn_server)}display: block;{else}display: none;{/if}}">
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
		<p>Server Cert:<br><textarea name="vpn_server_cert" cols="50" rows="10">{$subnet_data.vpn_server_cert}</textarea></p>
		<p>Server Key<br><textarea name="vpn_server_key" cols="50" rows="10">{$subnet_data.vpn_server_key}</textarea></p>
		
		<p>Passphrase:<br><input name="vpn_server_pass" type="password" size="30" value="{$subnet_data.vpn_server_pass}"></p>
	</div>

	<p><input type="submit" value="Absenden"></p>
</form>

<form action="./subneteditor.php?section=delete&subnet_id={$subnet_data.id}" method="POST">
  <h2>Subnet Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <p><input type="submit" value="Löschen!"></p>
</form>