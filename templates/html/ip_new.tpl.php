{if empty($existing_subnets)}
	<div class="error">Es muss mindestens ein Subnetz angelegt sein, damit du eine IP erstellen kannst.</div>
{else}

{literal}
	<!--http://plugins.jquery.com/project/zendjsonrpc-->
	
	<script type="text/javascript" src="lib/classes/extern/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/json2.js"></script>
	<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/jquery.zend.jsonrpc.js"></script>
	<script type="text/javascript">
		function getSubnetInfo(subnet_id) {
			$(document).ready(function(){
				test = jQuery.Zend.jsonrpc({url: 'api_main.php'});
				subnet = test.subnet_info(subnet_id);
				if(subnet.dhcp_kind!='no') {
					document.getElementById('section_dhcp').style.display = 'block';
					
					if(subnet.dhcp_kind=='ips') {
						document.getElementById('dhcp_kind_ips').style.display = 'block';
						document.getElementById('dhcp_kind_subnet').style.display = 'none';
						document.getElementById('dhcp_kind_nat').style.display = 'none';
					} else if(subnet.dhcp_kind=='subnet') {
						document.getElementById('dhcp_kind_subnet').style.display = 'block';
						document.getElementById('dhcp_kind_ips').style.display = 'none';
						document.getElementById('dhcp_kind_nat').style.display = 'none';
					} else if(subnet.dhcp_kind=='nat') {
						document.getElementById('dhcp_kind_nat').style.display = 'block';
						document.getElementById('dhcp_kind_subnet').style.display = 'none';
						document.getElementById('dhcp_kind_ips').style.display = 'none';
					}
				} else {
					document.getElementById('section_dhcp').style.display = 'none';
				}
			});
		}
	</script>
{/literal}

<h1>IP anlegen:</h1>
<form action="./ipeditor.php?section=insert" method="POST">
	<h2>Projekt</h2>
	<p>
		IP im Projekt:
		<select name="subnet_id" onChange="getSubnetInfo(this.options[this.selectedIndex].value)">
			<option value="false" selected>Bitte wählen</option>
			{foreach item=subnet from=$existing_subnets}
				<option value="{$subnet.id}">{$net_prefix}.{$subnet.host}/{$subnet.netmask} ({$subnet.title})</option>
			{/foreach}
		</select> anlegen.
	</p>
	
	<h2>IP</h2>
	<p>
		<input type="radio" name="ip_kind" value="simple" checked="checked" onchange="document.getElementById('ip_extend').style.display = 'none';">IP vom System vergeben lassen<br>
		<input type="radio" name="ip_kind" value="extend" onchange="document.getElementById('ip_extend').style.display = 'block';">IP aussuchen<br>
	</p>
	<div id="ip_extend" style="display: none;">
		<b>IP:</b> {$net_prefix}.<input name="ip" type="text" size="7">
	</div>

	<div id="section_dhcp" style="display: none;">
		<h2>DHCP</h2>
		<div id="dhcp_kind_ips">
			<p>
				<input type="radio" name="dhcp_ips_kind" value="simple" checked="checked" onchange="document.getElementById('dhcp_ips_extend').style.display = 'none'; document.getElementById('dhcp_ips_simple').style.display = 'block';">Zahl der benötigten IP´s angeben<br>
				<input type="radio" name="dhcp_ips_kind" value="extend" onchange="document.getElementById('dhcp_ips_simple').style.display = 'none'; document.getElementById('dhcp_ips_extend').style.display = 'block';">DHCP-Bereich selbst angeben<br>
			</p>
			<div id="dhcp_ips_simple" style="display: block;">
				<p>Für Clients zu reservierende IP's: <input name="ips" type="text" size="1" maxlength="3" value="5"></p>
			</div>
			<div id="dhcp_ips_extend" style="display: none;">
				<b>IP-Bereich:</b> {$net_prefix}.<input name="dhcp_first" type="text" size="7"> bis {$net_prefix}.<input name="dhcp_last" type="text" size="7">
			</div>
		</div>
		<div id="dhcp_kind_subnet">
			<p>
				<input type="radio" name="dhcp_kind_subnet" value="simple" checked="checked" onchange="document.getElementById('dhcp_subnet_extend').style.display = 'none'; document.getElementById('dhcp_subnet_simple').style.display = 'block';">Zahl der benötigten IP´s angeben<br>
				<input type="radio" name="dhcp_kind_subnet" value="extend" checked onchange="document.getElementById('dhcp_subnet_simple').style.display = 'none'; document.getElementById('dhcp_subnet_extend').style.display = 'block';">Subnetz selbst angeben<br>
			</p>
			<div id="dhcp_subnet_simple" style="display: none;">
				benötigte DHCP-IP´s: <select name="dhcp_subnet_ips">
					<option value="2">2 (/30)</option>
					<option value="6">6 (/29)</option>
					<option value="14">14 (/28)</option>
					<option value="30">30 (/27)</option>
					<option value="62">62 (/26)</option>
					<option value="126">126 (/25)</option>
					<option value="254">254 (/24)</option>
				</select>
			</div>
			<div id="dhcp_subnet_extend" style="display: block;">
					<div style="float:left;">
						Host:<br>
						10.18. <input id="dhcp_subnet_host" name="dhcp_subnet_host" type="text" size="7" maxlength="7"> /
					</div>
					<div style="margin-left:3px;">
						Netmask:<br>
						<input name="dhcp_subnet_netmask" type="text" size="2" maxlength="2">
					</div>
			</div>
		</div>
		<div id="dhcp_kind_nat">
			<p>Vergeben Sie IP´s aus einem selbst gewählten, genatteten Subnet.<br>
			   Die per DHCP angebundenen Clients werden bei dieser Methode nicht direkt in das Freifunk Netz eingebunden.
			</p>

			<p>Sie sollten das Subnetz, das Sie vergeben zu informativen Zwecken hier eintragen:
			<div style="float:left;">
				Host:<br>
				<input id="dhcp_nat_host" name="dhcp_nat_host" type="text" size="15" maxlength="15"> /
			</div>
			<div style="margin-left:3px;">
				Netmask:<br>
				<input name="dhcp_nat_netmask" type="text" size="2" maxlength="2">
			</div>
		</div>
	</div>

	<div>
		<h2>Standort</h2>
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 55%;">
				<script type="text/javascript" src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
				<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
				<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
				<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
				
				<div id="map" style="height:200px; width:400px; border:solid 1px black;font-size:9pt;">
					{literal}
						<script type="text/javascript">
							new_ip_map();
						</script>
					{/literal}
				</div>
			</div>
		
			<div style="float:left; width: 45%;">
				<p>
					Länge: <input id="longitude" name="longitude" size="15" maxlength="15" ><br>
					Breite: <input id="latitude" name="latitude" size="15" maxlength="15" >
				</p>
			</div>
		</div>
	</div>
	
	<p>Kurze Beschreibung des Standorts:<br><input name="location" type="text" size="60" maxlength="60" value=""></p>

	<h2>Routertyp</h2>
	<p>Chipset: <select name="chipset">
			{foreach item=imggen_supported_chipset from=$imggen_supported_chipsets}
			<option value="{$imggen_supported_chipset}">{$imggen_supported_chipset}</option>
			{/foreach}
		</select>
	</p>

	<!--	<h2>Reichweite</h2>
	<p>
		Radius (optional): <input name="radius" type="text" size="5" maxlength="10" value="80"><br>
		 Sinnvoll wenn Typ "ip" ist und man die ungefähre Reichweite seines W-Lan-Netzes in metern weiß.</p>
	</p>-->
	
	<h1>Dienst anlegen</h1>
	<script type="text/javascript" src="./templates/js/servicesAuswahl.js"></script>
	<script type="text/javascript" src="./templates/js/LinkedSelection.js"></script>
	<script type="text/javascript">
		{literal}
			window.onload = function()
			{
				var vk = new LinkedSelection( [ 'typ', 'crawl'], serviceAuswahl );
			}
		{/literal}
	</script>
	
	<p>
		<label id="typLabel" for="typ">Dienst:</label>
		<select id="typ" name="typ">
			<option value="false">Bitte wählen:</option>
			<option value="node">Freifunk Knoten</option>
			<option value="vpn">Vpn-Client</option>
			<option value="client">Wlan-Client</option>
			<option value="service">Server</option>
		</select>
		
		<label id="crawlLabel" for="crawl">Crawlart:</label>
		<select id="crawl" name="crawler">
			<option value="false">erst Dienst auswählen</option>
		</select>
		
		<span id="portInput" style="visibility:hidden; margin-left: 5px;">
			Portnummer: <input name="port" type="text" size="5" maxlength="10" value="">
		</span> 
	</p>

	<div id="discription" style="display:none">
		<h2>Beschreibung</h2>
		<p>
			Titel:<br><input name="title" type="text" size="40" maxlength="40" value="">
  		</p>
		
		<p>
			Beschreibung: <br><textarea name="description" cols="50" rows="10"></textarea>
		</p>
	</div>

	<h2>Privatsphäre:</h2>
	<p>
		Dienst für alle sichtbar:  
		<select name="visible" size="1">
			<option value="1" selected>Ja</option>
			<option value="0">Nein</option>
		</select>
	</p>

	<h2>Benachrichtigungen:</h2>
	<p>
		Benachrichtige mich, wenn dieser Dienst länger als <input name="notification_wait" type="text" size="2" maxlength="2" value="6"> Crawldurchgänge nicht erreichbar ist
		<select name="notify" size="1">
			<option value="1" selected>Ja</option>
		      <option value="0">Nein</option>
		    </select>
	</p>

	<p><input type="submit" value="Absenden"></p>
</form>

{/if}