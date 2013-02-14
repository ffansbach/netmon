<script src="lib/classes/extern/jquery/jquery.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#routerlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aaSorting": [[ 0, "asc" ]]
	} );
} );
{/literal}
</script>

<h1>Liste der Router</h1>
{if !empty($routerlist)}
	<div style="display: block; float: left;">
		<table class="display" id="routerlist">
			<thead>
				<tr>
					<th>Hostname</th>
					<th>O</th>
					<th>Stand</th>
					<th>Technik</th>
					<th>Benutzer</th>
					<th>Online</th>
					<th>Uptime</th>
					<th>Clients</th>
<!--					<th>Origs</th>-->
					<th>Traffic</th>
				</tr>
			</thead>
			<tbody>
				{foreach key=count item=router from=$routerlist}
					<tr>
						<td><a href="./router_status.php?router_id={$router.router_id}">{$router.hostname}</a></td>
						<td>
							{if $router.actual_crawl_data.status=="online"}
								<img src="./templates/{$template}/img/ffmap/status_up_small.png" title="online" alt="online">
							{elseif $router.actual_crawl_data.status=="offline"}
								<img src="./templates/{$template}/img/ffmap/status_down_small.png" title="offline" alt="offline">
							{elseif $router.actual_crawl_data.status=="unknown"}
								<img src="./templates/{$template}/img/ffmap/status_unknown_small.png" title="unknown" alt="unknown">
							{/if}
						</td>
						<td>{$router.actual_crawl_data.crawl_date|date_format:"%H:%M"} Uhr</td>
						<td>{if !empty($router.hardware_name)}{$router.hardware_name}{else}{$router.short_chipset_name}{if $router.short_chipset_name!=$router.chipset_name}...{/if}{/if}</td>
						<td><a href="./user.php?user_id={$router.user_id}">{$router.nickname}</a></td>
						<td value="{math equation='round(x,1)' x=$router.router_reliability.online_percent}">{math equation="round(x,1)" x=$router.router_reliability.online_percent}%</td>
						<td>{math equation="round(x,1)" x=$router.actual_crawl_data.uptime/60/60} Std.</td>
						<td>{$router.client_count}</td>
<!--						<td>{$router.originators_count}</td>-->
						<td>{$router.traffic}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
<p>Keine Router vorhanden</p>
{/if}

<br style="clear: both;">