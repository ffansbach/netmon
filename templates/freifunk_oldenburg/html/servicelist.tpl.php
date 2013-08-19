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

<h1>Liste der Dienste</h1>
{if !empty($servicelist)}
	<table class="display" id="servicelist" style="width: 100%;">
		<thead>
			<tr>
				<th>Title</th>
				<th>Ips</th>
				<th>Ressource-Records</th>
				<th>Port</th>
				<th>Benutzer</th>
			</tr>
		</thead>
		<tbody>
			{foreach $servicelist as $service}
				<tr>
					<td><a href="./service.php?service_id={$service->getServiceId()}">{$service->getTitle()}</a></td>
					<td>
						{foreach key=i item=ip from=$service->getIplist()->getIplist()}{if $i>0},<br>{/if}{$ip->getIp()}{/foreach}
					</td>
					<td>
						{foreach key=i item=dns_ressource_record from=$service->getDnsRessourceRecordList()->getDnsRessourceRecordList()}{if $i>0},<br>{/if}{$dns_ressource_record->getHost()}.{$dns_ressource_record->getDnsZone()->getName()}{/foreach}
					</td>
					<td>{$service->getPort()}</td>
					<td><a href="./user.php?user_id={$service->getUserId()}">{$service->getUser()->getNickname()}</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>Keine Dienste vorhanden.</p>
{/if}