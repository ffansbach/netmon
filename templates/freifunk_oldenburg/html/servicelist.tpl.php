<script src="lib/classes/extern/jquery/jquery.min.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.min.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#servicelist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
			{ "sType": "html" },
			{ "sType": "html" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" }
		],
		"aaSorting": [[ 0, "asc" ]]
	} );
} );

{/literal}
</script>

<h1>Liste der Dienste</h1>
{if !empty($servicelist)}
	<table class="display" id="servicelist" style="width: 100%;">
		<thead>
			<tr>
				<th>Title</th>
				<th>Router</th>
				<th>Server</th>
				<th>Dienst</th>
				<th>Stand</th>
				<th>Link</th>
			</tr>
		</thead>
		<tbody>
			{foreach key=count item=service from=$servicelist}
				<tr>
					<td><a href="./router_status.php?router_id={$service.router_id}#service_{$service.service_id}">{$service.title}</a></td>
					<td><a href="./router_status.php?router_id={$service.router_id}">{$service.hostname}</a></td>
					<td>
						{if $service.router_status=="online"}
							<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="online">
						{elseif $service.router_status=="offline"}
							<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="offline">
						{elseif $service.router_status=="unknown"}
							<img src="./templates/{$template}/img/ffmap/status_unknown_small.png" title="unknown" alt="unknown">
						{/if}
					</td>
					<td>
						{if $service.service_status=="online"}
							<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="online">
						{elseif $service.service_status=="offline"}
							<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="offline">
						{elseif $service.service_status=="unknown"}
							<img src="./templates/{$template}/img/ffmap/status_unknown_small.png" title="unknown" alt="unknown">
						{/if}
					</td>
					<td>{$service.service_status_crawl_date|date_format:"%H:%M"} Uhr</td>
					<td><a href="{$service.combined_url_to_service}">{$service.combined_url_to_service}</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>Keine Dienste vorhanden.</p>
{/if}