<script src="lib/classes/extern/jquery/jquery.min.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.min.js"></script>

<script type="text/javascript">
{literal}
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "date-eu-pre": function ( date ) {
        var date = date.replace(" ", "");
          
        if (date.indexOf('.') > 0) {
            /*date a, format dd.mn.(yyyy) ; (year is optional)*/
            var eu_date = date.split('.');
        } else {
            /*date a, format dd/mn/(yyyy) ; (year is optional)*/
            var eu_date = date.split('/');
        }
          
        /*year (optional)*/
        if (eu_date[2]) {
            var year = eu_date[2];
        } else {
            var year = 0;
        }
          
        /*month*/
        var month = eu_date[1];
        if (month.length == 1) {
            month = 0+month;
        }
          
        /*day*/
        var day = eu_date[0];
        if (day.length == 1) {
            day = 0+day;
        }
          
        return (year + month + day) * 1;
    },
 
    "date-eu-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "date-eu-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );

$(document).ready(function() {
	$('#routerlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
			{ "sType": "html" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "date-eu" },
			{ "sType": "html" },
			{ "sType": "string" }, // zuverlässigkeit need own
			{ "sType": "numeric" },
			{ "sType": "numeric" },
			{ "sType": "numeric" }
		],
		"aaSorting": [[ 7, "desc" ]]
	} );
} );
{/literal}
</script>

<h1>Liste der Router</h1>
{if !empty($routerlist)}
	<table class="display" id="routerlist" style="width: 100%;">
		<thead>
			<tr>
				<th>Hostname</th>
				<th>O</th>
				<th>Technik</th>
				<th>Angelegt</th>
				<th>Benutzer</th>
				<th>Online</th>
				<th>Up (Std.)</th>
				<th>Clients</th>
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
					<td>{if !empty($router.hardware_name)}{$router.hardware_name}{else}{$router.short_chipset_name}{if $router.short_chipset_name!=$router.chipset_name}...{/if}{/if}</td>
					<td>{$router.router_create_date|date_format:"%d.%m.%Y"}</td>
					<td><a href="./user.php?user_id={$router.user_id}">{$router.nickname}</a></td>
					<td value="{math equation='round(x,1)' x=$router.router_reliability.online_percent}">{math equation="round(x,1)" x=$router.router_reliability.online_percent}%</td>
					<td>{math equation="round(x,1)" x=$router.actual_crawl_data.uptime/60/60}</td>
					<td>{$router.actual_crawl_data.client_count}</td>
					<td>{$router.traffic}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
<p>Keine Router vorhanden.</p>
{/if}