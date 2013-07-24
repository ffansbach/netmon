<script src="lib/classes/extern/jquery/jquery.min.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.min.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#networklist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "html" }
		],
		"aaSorting": [[ 0, "desc" ]]
	} );
} );
{/literal}
</script>

<h1>Netzwerke</h1>

<p>Hier können Netzwerke eingetragen werden aus denen Benutzer dann IP Adressen für eigene Hardware reservieren können.</p>

<h2>Eingetragene Netzwerke</h2>
{if !empty($networklist)}
	<table class="display" id="networklist" style="width: 100%;">
		<thead>
			<tr>
				<th>Netzwerk</th>
				<th>Angelegt am</th>
				<th>Aktionen</h2>
			</tr>
		</thead>
		<tbody>
			{foreach item=network from=$networklist}
				<tr>
					<td>{$network->getIp()}/{$network->getNetmask()}</td>
					<td>{$network->getCreateDate()|date_format:"%d.%m.%Y %H:%M"} Uhr</td>
					<td><a href="./config.php?section=insert_delete_network&network_id={$network->getNetworkId()}">Löschen</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>Keine Netzwerk eingetragen.</p>
{/if}

<h2>Neues Netzwerk eintragen</h2>
<form action="./config.php?section=insert_edit_networks" method="POST">
	<p><b>Netzwerk:</b><br><input name="ipv4_address" type="text" size="15" value="">/<input name="netmask" type="text" size="2" value=""></p>
	
	<p><input type="submit" value="Eintragen"></p>
</form>