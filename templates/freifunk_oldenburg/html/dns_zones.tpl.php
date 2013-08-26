<script src="lib/extern/jquery/jquery.min.js"></script>
<script src="lib/extern/DataTables/jquery.dataTables.min.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#dns_zone_list').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "html" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "html" }
		],
		"aaSorting": [[ 0, "desc" ]]
	} );
} );
{/literal}
</script>

<h1>DNS-Zonen</h1>

<p>Hier können optional Netzinterne DNS-Zonen konfiguriert werden zu denen Benutzer dann eigene DNS-Records für
Services eintragen können.</p>

<h2>Eingetragene DNS-Zonen</h2>
{if !empty($dns_zone_list)}
	<table class="display" id="dns_zone_list" style="width: 100%;">
		<thead>
			<tr>
				<th>Name</th>
				<th>Primary DNS</th>
				<th>Secondary DNS</th>
				<th>Refresh</th>
				<th>Retry</th>
				<th>Expire</th>
				<th>TTL</th>
				<th>Benutzer</th>
				<th>Angelegt</th>
				<th>Aktionen</h2>
			</tr>
		</thead>
		<tbody>
			{foreach item=dns_zone from=$dns_zone_list}
				<tr>
					<td><a href="./dns_zone.php?dns_zone_id={$dns_zone->getDnsZoneId()}">{$dns_zone->getName()}</a></td>
					<td>{$dns_zone->getPriDns()}</td>
					<td>{$dns_zone->getSecDns()}</td>
					<td>{$dns_zone->getRefresh()}</td>
					<td>{$dns_zone->getRetry()}</td>
					<td>{$dns_zone->getExpire()}</td>
					<td>{$dns_zone->getTtl()}</td>
					<td><a href="./user.php?user_id={$dns_zone->getUser()->getUserId()}">{$dns_zone->getUser()->getNickname()}</a></td>
					<td>{$dns_zone->getCreateDate()|date_format:"%d.%m.%Y"}</td>
					<td><a href="./dns_zone.php?section=delete&dns_zone_id={$dns_zone->getDnsZoneId()}">Löschen</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>Keine DNS-Zonen eingetragen.</p>
{/if}

<h2>Neue DNS-Zone eintragen</h2>
<form action="./dns_zone.php?section=insert_add" method="POST">
	<p><b>Name:</b><br><input name="name" type="text" size="10" value=""></p>
	<p><b>Primary DNS:</b><br><input name="pri_dns" type="text" size="10" value=""></p>
	<p><b>Secondary DNS:</b><br><input name="sec_dns" type="text" size="10" value=""></p>
	<p><b>Refresh:</b><br><input name="refresh" type="text" size="10" value="604800"></p>
	<p><b>Retry:</b><br><input name="retry" type="text" size="10" value="86400"></p>
	<p><b>Expire:</b><br><input name="expire" type="text" size="10" value="2419200"></p>
	<p><b>Time to live:</b><br><input name="ttl" type="text" size="10" value="604800"></p>
	
	
	<p><input type="submit" value="Eintragen"></p>
</form>