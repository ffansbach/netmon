{if empty($projects)}
	<div class="error">Es muss mindestens ein Projekt angelegt sein, damit du ein Device hinzufügen kannst.</div>
{else}

{literal}
	<!--http://plugins.jquery.com/project/zendjsonrpc-->
	
	<script type="text/javascript" src="lib/classes/extern/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/json2.js"></script>
	<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/jquery.zend.jsonrpc.js"></script>
	<script type="text/javascript">
		function getProjectInfo(project_id) {
			$(document).ready(function(){
				api_main = jQuery.Zend.jsonrpc({url: 'api_main.php'});
				project = api_main.project_info(project_id);
				if(project.ipv=='ipv4') {
					free_ipv4_ip = api_main.getAFreeIPv4IPByProjectId(project_id);
				}

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

				if(project.ipv=='ipv4') {
					document.getElementById('section_ipv4').style.display = 'block';
					document.getElementById('section_ipv6').style.display = 'none';
					document.getElementById('section_ip_no').style.display = 'none';

					document.getElementById('ipv4_netmask').innerHTML = project.ipv4_netmask_dot;
					document.getElementById('ipv4_addr').innerHTML = free_ipv4_ip;
					document.getElementById('ipv4_addr_input').value = free_ipv4_ip;
				} else if(project.ipv=='ipv6') {
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
			});
		}
	</script>
{/literal}

<h1>Device hinzufügen:</h1>
<form action="./interfaceeditor.php?section=insert_add&router_id={$smarty.get.router_id}" method="POST">
	<h2>Projekt</h2>
	<p>
		Device im Projekt:
		<select name="project_id" onChange="getProjectInfo(this.options[this.selectedIndex].value)">
			<option value="false" selected='selected'>Bitte wählen</option>
			{foreach item=project from=$projects}
				<option value="{$project.id}">{$project.title}</option>
			{/foreach}
		</select> anlegen.
	</p>
	
	<p><b>Projektbescheibung: </b><span id="project_description"></span></p>

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
				<b>IP-Adresse: </b> {$net_prefix}.<span id="ipv4_addr"></span>
				<input id="ipv4_addr_input" name="ipv4_addr" type="hidden">
			</li>
			<li>
				<b>Netzmaske: </b> <span id="ipv4_netmask"></span>
			</li>
		<ul>
	</div>

	<div id="section_ipv6" style="display: none;">
		<h2>IP Konfiguration</h2>
		<ul>
			<li>
				<b>IPv6: </b> Ja
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