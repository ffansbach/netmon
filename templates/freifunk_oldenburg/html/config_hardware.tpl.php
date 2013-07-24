<script src="lib/classes/extern/jquery/jquery.min.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.min.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#chipsetlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "html" }
		],
		"aaSorting": [[ 0, "desc" ]]
	} );
} );

$(document).ready(function() {
	$('#chipsetlist_unnamed').dataTable( {
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

<h1>Konfiguration der Hardwarenamen</h1>
<p>Hier können den Chipbezeichnungen der Hersteller Routernamen für normalsterbliche zugewiesen werden.</p>
<h2>Vorhandene Chipsets</h2>
{if !empty($chipsets_with_name)}
<table class="display" id="chipsetlist">
	<thead>
		<tr>
			<th>Chipset</th>
			<th>Name</th>
			<th>Erstellt</th>
			<th>Aktionen</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=count item=chipset from=$chipsets_with_name}
			<tr>
				<td>{$chipset.name}</td>
				<td>{$chipset.hardware_name}</td>
				<td>{$chipset.create_date|date_format:"%d.%m.%Y %H:%M"} Uhr</td>
				<td><a href="./config.php?section=edit_hardware_name&chipset_id={$chipset.id}">Editieren</a></td>
			</tr>
		{/foreach}
	</tbody>
</table>
{else}
	<p>Keine Chipsets vorhanden. Neue Chipsets werden wärend der Crawls automatisch hinzugefügt und können dann zugewiesen werden.</p>
{/if}


<h2>Nicht Zugewiesene Chipsets</h2>
{if !empty($chipsets_without_name)}
<table class="display" id="chipsetlist_unnamed">
	<thead>
		<tr>
			<th>Chipset</th>
			<th>Erstellt</th>
			<th>Aktionen</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=count item=chipset from=$chipsets_without_name}
			<tr>
				<td>{$chipset.name}</td>
				<td>{$chipset.create_date|date_format:"%d.%m.%Y %H:%M"} Uhr</td>
				<td><a href="./config.php?section=edit_hardware_name&chipset_id={$chipset.id}">Editieren</a></td>
			</tr>
		{/foreach}
	</tbody>
</table>
{else}
	<p>Keine nicht zugewiesenen Chipsets vorhanden.</p>
{/if}