<script src="lib/classes/extern/jquery/jquery.min.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.min.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#routerlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "html" }
		],
		"aaSorting": [[ 0, "asc" ]]
	} );
} );
{/literal}
</script>

<h1>Liste der neuen Router</h1>
{if !empty($routerlist)}
	<table class="display" id="routerlist" style="width: 100%;">
		<thead>
			<tr>
				<th>Hostname</th>
				<th>Mac Adresse</th>
				<th>Erstellt</th>
				<th>Update</th>
				<th>Aktionen</th>
			</tr>
		</thead>
		<tbody>
			{foreach key=count item=router from=$routerlist}
				<tr>
					<td>{$router.hostname}</td>
					<td>{$router.router_auto_assign_login_string}</td>
					<td>{$router.create_date|date_format:"%d.%m.%Y %H:%M"} Uhr</td>
					<td>{$router.update_date|date_format:"%d.%m.%Y %H:%M"} Uhr</td>
					<td><a href="./routereditor.php?section=new&crawl_method=netmon&allow_router_auto_assign=1&router_auto_assign_login_string={$router.router_auto_assign_login_string}">Übernehmen</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>In der Liste der neuen Router ist derzeit kein Router eingetragen.</p>
{/if}