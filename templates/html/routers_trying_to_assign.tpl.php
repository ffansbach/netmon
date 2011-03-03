<script src="lib/classes/extern/jquery/jquery.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.js"></script>

<link rel="stylesheet" type="text/css" href="templates/css/jquery_data_tables.css">

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#routerlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false
	} );
} );
{/literal}
</script>

<script type="text/javascript">
	document.body.id='tab2';
</script>
<ul id="tabnav">
	<li class="tab1"><a href="./routerlist.php">Routerliste</a></li>
	<li class="tab2"><a href="./routers_trying_to_assign.php">Nicht angemeldete Router</a></li>
</ul>

<h1>Liste der Router die noch nicht zugewiesen sind</h1>
{if !empty($routerlist)}
	<div style="display: block; float: left;">
		<table class="display" id="routerlist">
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
						<td>{$router.update_date|date_format:"%H:%M"} Uhr</td>
						<td><a href="./routereditor.php?section=new&hostname={$router.hostname}&crawl_method=router&allow_router_auto_assign=1&router_auto_assign_login_string={$router.router_auto_assign_login_string}">Übernehmen</a></td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
<p>Keine Router vorhanden</p>
{/if}