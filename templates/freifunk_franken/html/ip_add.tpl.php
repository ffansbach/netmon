<script type="text/javascript" src="lib/classes/extern/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/json2.js"></script>
<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/jquery.zend.jsonrpc.js"></script>
{literal}
<script type="text/javascript">
	function getIps(project_id) {
		api_main = jQuery.Zend.jsonrpc({url: 'api_main.php'});

		project = api_main.project_info(project_id);

		if (project.is_ipv4==1) {
			document.getElementById('section_ipv4').style.display = 'block';

			free_ipv4_ip = api_main.getAFreeIPv4IPByProjectId(project_id);
			document.getElementById('ipv4_netmask').innerHTML = project.ipv4_netmask_dot;
			document.getElementById('ipv4_addr').innerHTML = free_ipv4_ip;
			document.getElementById('ipv4_addr_input').value = free_ipv4_ip;
		}

		if (project.is_ipv6==1) {
			document.getElementById('section_ipv6').style.display = 'block';
		}
//		document.getElementById('section_ip_no').style.display = 'none';
	}
</script>
{/literal}

<form action="./ipeditor.php?section=insert&router_id={$smarty.get.router_id}" method="POST">
<h1>IP hinzufügen</h1>
<h2>Interface wählen</h2>
IP zu folgendem Interface hinzufügen:
<select id="interface_id" name="interface_id">
	<option value="false" selected='selected'>Bitte wählen</option>
	{foreach item=interface from=$router_interfaces}
		<option value="{$interface.interface_id}" {if $smarty.get.interface_id==$interface.interface_id}selected{/if}>{$interface.name}</option>
	{/foreach}
</select>

<h2>Projekt wählen</h2>
Device im Projekt:
<select id="project_id" name="project_id" onChange="getIps(this.options[this.selectedIndex].value)">
	<option value="false" selected='selected'>Bitte wählen</option>
	{foreach item=project from=$projects}
		<option value="{$project.id}">{$project.title}</option>
	{/foreach}
</select> anlegen.

<div id="section_ipv4" style="display: none;">
	<h2>IPv4 Konfiguration</h2>
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

<div id="section_ipv6" style="display: none;">
	<h2>IPv6 Konfiguration</h2>
	<ul>
		<li>
			<b>IPv6: </b> Ja
		</li>
		<li>
			<b>IPv6-Adresse: </b> <input id="ipv6_addr_input" name="ipv6_addr">
		</li>
	<ul>
</div>

<p><input type="submit" value="Absenden"></p>

</form>