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
	document.body.id='tab1';
</script>
<ul id="tabnav">
	<li class="tab1"><a href="./routerlist.php">Routerliste</a></li>
	<li class="tab2"><a href="./routers_trying_to_assign.php">Nicht angemeldete Router</a></li>
</ul>

<h1>Liste der Router</h1>
{if !empty($routerlist)}
	<div style="display: block; float: left;">
		<table class="display" id="routerlist">
			<thead>
				<tr>
					<th>Hostname</th>
					<th>Status</th>
					<th>Stand</th>
					<th>Technik</th>
					<th>Benutzer</th>
					<th>Zuverlässigkeit</th>
					<th>Uptime</th>
				</tr>
			</thead>
			<tbody>
				{foreach key=count item=router from=$routerlist}
					<tr>
						<td><a href="./router_status.php?router_id={$router.router_id}">{$router.hostname}</a></td>
						<td>
							{if $router.actual_crawl_data.status=="online"}
								<img src="./templates/img/ffmap/status_up_small.png" title="online" alt="online">
							{elseif $router.actual_crawl_data.status=="offline"}
								<img src="./templates/img/ffmap/status_down_small.png" title="offline" alt="offline">
							{/if}
						</td>
						<td>{$router.actual_crawl_data.crawl_date|date_format:"%H:%M"} Uhr</td>
						<td>{$router.chipset_name}</td>
						<td><a href="./user.php?user_id={$router.user_id}">{$router.nickname}</a></td>
						<td>{$router.router_reliability.online_percent}% online</td>
						<td>{math equation="round(x,1)" x=$router.actual_crawl_data.uptime/60/60} Stunden</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
<p>Keine Router vorhanden</p>
{/if}