{if empty($projects)}
	<div class="error">Es muss mindestens ein Projekt angelegt sein, damit du ein Device hinzufügen kannst.</div>
{else}

{literal}
	<!--http://plugins.jquery.com/project/zendjsonrpc-->
	
	<script type="text/javascript" src="lib/classes/extern/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/json2.js"></script>
	<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/jquery.zend.jsonrpc.js"></script>
	<script type="text/javascript">
		function getIpRange(range) {
			free_ipv4_range = api_main.getFreeIpRangeByProjectId(document.getElementById('project_id').options[document.getElementById('project_id').selectedIndex].value, range, document.getElementById('ipv4_addr_input').value);
			document.getElementById('ipv4_dhcp_range_first_ip').innerHTML = free_ipv4_range.start;
			document.getElementById('ipv4_dhcp_range_last_ip').innerHTML = free_ipv4_range.end;
		}

		function getProjectInfo(project_id) {
			$(document).ready(function(){
				api_main = jQuery.Zend.jsonrpc({url: 'api_main.php'});
				project = api_main.project_info(project_id);
/*				if(project.ipv=='ipv4') {
					free_ipv4_ip = api_main.getAFreeIPv4IPByProjectId(project_id);
				}*/

				document.getElementById('project_title').innerHTML = project.title;
				document.getElementById('project_description').innerHTML = project.description;

				if(project.is_wlan=='1') {
					document.getElementById('section_wlan').style.display = 'block';

					document.getElementById('name').value = 'wlan1';
					document.getElementById('wlan_essid').innerHTML = project.wlan_essid;
					document.getElementById('wlan_bssid').innerHTML = project.wlan_bssid;
					document.getElementById('wlan_channel').innerHTML = project.wlan_channel;
				} else {
					document.getElementById('section_wlan').style.display = 'none';
				}

				if(project.is_vpn=='1') {
					document.getElementById('name').value = 'vpn1';
					document.getElementById('section_vpn').style.display = 'block';

					document.getElementById('vpn_server').innerHTML = project.vpn_server;
					document.getElementById('vpn_server_device').innerHTML = project.vpn_server_device;
					document.getElementById('vpn_server_proto').innerHTML = project.vpn_server_proto;
				} else {
					document.getElementById('section_vpn').style.display = 'none';
				}

				if(project.is_olsr=='1') {
					document.getElementById('section_olsr').style.display = 'block';
				} else {
					document.getElementById('section_olsr').style.display = 'none';
				}

				if(project.is_batman_adv=='1') {
					document.getElementById('section_batman_adv').style.display = 'block';
				} else {
					document.getElementById('section_batman_adv').style.display = 'none';
				}

				if(project.is_ipv4=='1') {
					document.getElementById('section_ipv4').style.display = 'block';
					document.getElementById('section_ipv6').style.display = 'none';
					document.getElementById('section_ip_no').style.display = 'none';

					free_ipv4_ip = api_main.getAFreeIPv4IPByProjectId(project_id);
					document.getElementById('ipv4_netmask').innerHTML = project.ipv4_netmask_dot;
					document.getElementById('ipv4_addr').innerHTML = free_ipv4_ip;
					document.getElementById('ipv4_addr_input').value = free_ipv4_ip;
				} else if(project.is_ipv6=='1') {
					document.getElementById('section_ipv6').style.display = 'block';
					document.getElementById('section_ipv4').style.display = 'none';
					document.getElementById('section_ip_no').style.display = 'none';
				} else if(project.ipv=='no') {
					document.getElementById('section_ip_no').style.display = 'block';
					document.getElementById('section_ipv6').style.display = 'none';
					document.getElementById('section_ipv4').style.display = 'none';
				} else {
					document.getElementById('section_ip_no').style.display = 'none';
					document.getElementById('section_ipv6').style.display = 'none';
					document.getElementById('section_ipv4').style.display = 'none';
				}

				if(project.ipv4_dhcp_kind=='range') {
					document.getElementById('section_ipv4_dhcp').style.display = 'block';
				}  else if(project.ipv4_dhcp_kind=='no') {
					document.getElementById('section_ipv4_dhcp').style.display = 'none';
				}

				if(project.is_geo_specific=='1') {
					document.getElementById("geo_polygon_map").innerHTML = "";
					new_interface_projectmap(project.project_id);
					document.getElementById('geo_polygon_map').style.display = 'block';
					document.getElementById('geo_polygon_hint').style.display = 'block';
				}  else {
					document.getElementById('geo_polygon_map').style.display = 'none';
					document.getElementById('geo_polygon_hint').style.display = 'none';
				}
				
			});
		}
	</script>
{/literal}

<h1>Interface zum Router {$router_data.hostname} hinzufügen:</h1>
<form action="./interfaceeditor.php?section=insert_add&router_id={$smarty.get.router_id}" method="POST">
	<h2>Projekt</h2>
	<div style="width: 100%; overflow: hidden;">
		<div style="float:left; width: 45%;">
			Device im Projekt:
			<select id="project_id" name="project_id" onChange="getProjectInfo(this.options[this.selectedIndex].value)">
				<option value="false" selected='selected'>Bitte wählen</option>
				{foreach item=project from=$projects}
					<option value="{$project.id}">{$project.title}</option>
				{/foreach}
			</select> anlegen.
			
			<p>
				<b>Projektbescheibung:</b><br>
				<span id="project_description"></span>
			</p>
			<p id="geo_polygon_hint" style="display: none;">
				<b>Hinweis:</b><br>
				Dieses Projekt ist lokal begrenzt. Bitte fügen sie Ihrem Router nur ein Interface dieses Projekts zu, wenn sich ihr Router im Bereich der Markierung auf der Karte befindet!
			</p>
		</div>
		<div style="float:left; width: 55%;">
			<script type="text/javascript" src='https://maps.googleapis.com/maps/api/js?key={$google_maps_api_key}&sensor=false'></script>
			<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
			<script type="text/javascript" src="./templates/{$template}/js/OpenStreetMap.js"></script>
			<script type="text/javascript" src="./templates/{$template}/js/OsmFreifunkMap.js"></script>
			
			<div id="geo_polygon_map" style="display: none; height:150px; width:350px; border:solid 1px black;font-size:9pt;">

			</div>
		</div>
	</div>

	<h2>Deviceeigenschaften</h2>
	<p>Wenn Sie das Device im Projekt <b><span id="project_title"></span></b> anlegen, erhält es folgende Eigenschaften:</p>	
	<div>
		<h2>Bezeichnung</h2>
		<ul>
			<li>
				<b>Name:</b> <input id="name" name="name" size="20" maxlength="20" >
			</li>
		</ul>
	</div>

	<div id="section_wlan" style="display: none;">
		<h2>Wlan</h2>
		<ul>
			<li>
				<b>Wlan: </b> Ja
			</li>
			<li>
				<b>ESSID: </b> <span id="wlan_essid"></span>
			</li>
			<li>
				<b>BSSID: </b> <span id="wlan_bssid"></span>
			</li>
			<li>
				<b>Channel: </b> <span id="wlan_channel"></span>
			</li>
		<ul>
	</div>

	<div id="section_vpn" style="display: none;">
		<h2>VPN</h2>
		<ul>
			<li>
				<b>VPN: </b> Ja
			</li>
			<li>
				<b>Server: </b> <span id="vpn_server"></span>
			</li>
			<li>
				<b>Device: </b> <span id="vpn_server_device"></span>
			</li>
			<li>
				<b>Protokoll: </b> <span id="vpn_server_proto"></span>
			</li>
		<ul>
	</div>

	<div id="section_olsr" style="display: none;">
		<h2>OLSR</h2>
		<ul>
			<li>
				<b>OLSR: </b> Ja
			</li>
		<ul>
	</div>

	<div id="section_batman_adv" style="display: none;">
		<h2>B.A.T.M.A.N advanced</h2>
		<ul>
			<li>
				<b>B.A.T.M.A.N advanced: </b> Ja
			</li>
		<ul>
	</div>

	<div id="section_ipv4" style="display: none;">
		<h2>IP Konfiguration</h2>
		<ul>
			<li>
				<b>IPv4: </b> Ja
			</li>
			<li>
				<b>IPv4-Adresse: </b> <span id="ipv4_addr"></span>
				<input id="ipv4_addr_input" name="ipv4_addr" type="hidden">
			</li>
			<li>
				<b>Netzmaske: </b> <span id="ipv4_netmask"></span>
			</li>
		<ul>
	</div>

	<div id="section_ipv4_dhcp" style="display: none;">
		<h2>IP DHCP Range Konfiguration</h2>
		<p>Geben Sie die Größe des Adressraumes ein den Sie zum verteilen reservieren möchten.</p>
		<ul>
			<li>
				<b>Anzahl der IP´s: </b> <input id="ipv4_dhcp_range" name="ipv4_dhcp_range" size="3" onChange="getIpRange(this.value)">
			</li>
		<ul>
		<div>
			<ul>
				<li>
					<b>Vorraussichtlich reservierter Adressraum: </b> <span id="ipv4_dhcp_range_first_ip"></span> - <span id="ipv4_dhcp_range_last_ip"></span>
				</li>
			<ul>
		</div>
	</div>

	<div id="section_ipv6" style="display: none;">
		<h2>IP Konfiguration</h2>
		<ul>
			<li>
				<b>IPv6: </b> Ja
			</li>
			<li>
				<b>IPv6-Adresse: </b> <input id="ipv6_addr_input" name="ipv6_addr">
			</li>
		<ul>
	</div>

	<div id="section_ip_no" style="display: none;">
		<h2>IP Konfiguration</h2>
		<ul>
			<li>
				<b>IP Konfiguration: </b> Das Device bekommt keine IP. Bei Devices die ausschließlich auf Layer 2 komunizieren ist keine IP notwendig.
			</li>
		<ul>
	</div>

	<p><input type="submit" value="Absenden"></p>
</form>

{/if}
