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

$(document).ready(function() {
	$('#servicelist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false
	} );
} );
{/literal}
</script>

{if $permitted}
<ul id="tabnav" style="text-align: right; padding-right: 0px; margin-right: 0px;">
	<li><a style="background-color: #b1dbff; font-style:italic; color: #000000; position: relative; top: 1px; padding-top: 4px; border-right: 0px;" href="./user_edit.php?section=edit&id={$smarty.get.user_id}">Benutzereinstellungen</a></li>
</ul>
{/if}

<h1>Benutzerseite von {$user.nickname}</h1>

<div style="width: 100%; overflow: hidden;">
	<div style="float:left; width: 47%;">
		<h2>Benutzerdaten</h2>
		<p>
			{if !empty($user.vorname) OR !empty($user.nachname)}<b>Name:</b> {$user.vorname} {$user.nachname}<br>{/if}
			
			{if !empty($user.strasse)}<b>Strasse: </b>{$user.strasse}<br>{/if}
			{if !empty($user.plz) OR !empty($user.ort)}</b>Wohnort:</b>  {$user.plz} {$user.ort}<br>{/if}
			{if !empty($user.telefon)}<b>Telefon:</b> {$user.telefon}<br>{/if}
			<b>Email:</b> <a href="mailto:{$user.email}">{$user.email}</a><br>
			
			{if !empty($user.jabber)}<b>Jabber-ID:</b> {$user.jabber}<br>{/if}
			{if !empty($user.icq)}<b>ICQ:</b> {$user.icq}<br>{/if}
			{if !empty($user.website)}<b>Website:</b> <a href="{$user.website}">{$user.website}</a><br>{/if}
			
			{if !empty($user.telefon)}<h2>Beschreibung:</h2><p> {$user.about}</p>{/if}
			
			<b>Anmeldedatum:</b> {$user.create_date|date_format:"%e.%m.%Y %H:%M"} Uhr
		</p>
	</div>
	<div style="float:left; width: 53%;">
		<h2>History</h2>
		{if !empty($user_history)}
			<ul>
				{foreach $user_history as $hist}
					<li>
						<b>{$hist.create_date|date_format:"%e.%m. %H:%M:%S"}:</b> <a href="./router_status.php?router_id={$hist.additional_data.router_id}">Router {$hist.additional_data.hostname}</a> 

						{if $hist.data.action == 'status' AND $hist.data.to == 'online'}
							geht <span style="color: #007B0F;">online</span>
						{/if}
						{if $hist.data.action == 'status' AND $hist.data.to == 'offline'}
							geht <span style="color: #CB0000;">offline</span>
						{/if}
						{if $hist.data.action == 'reboot'}
							wurde <span style="color: #000f9c;">Rebootet</span>
						{/if}
					</li>
				{/foreach}
			</ul>
		{else}
			<p>Keine Daten vorhanden</p>
		{/if}
	</div>
</div>

<h2>Router von {$user.nickname}</h2>
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
				{foreach $routerlist as $router}
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

<h2>Dienste von {$user.nickname}</h2>
{if !empty($servicelist)}
	<div style="display: block; float: left;">
		<table class="display" id="servicelist">
			<thead>
				<tr>
					<th>Title</th>
					<th>Router</th>
					<th>R-Status</th>
					<th>S-Status</th>
					<th>Benutzer</th>
					<th>Link</th>
				</tr>
			</thead>
			<tbody>
				{foreach $servicelist as $service}
					<tr>
						<td><a href="./router_config.php?router_id={$service.router_id}#service_{$service.service_id}">{$service.title}</a></td>
						<td><a href="./router_config.php?router_id={$service.router_id}">{$service.hostname}</a></td>
						<td>
							{if $service.router_status=="online"}
								<img src="./templates/img/ffmap/status_up_small.png" alt="online">
							{elseif $service.router_status=="offline"}
								<img src="./templates/img/ffmap/status_down_small.png" alt="offline">
							{/if}
						</td>
						<td>
							{if $service.service_status=="online"}
								<img src="./templates/img/ffmap/status_up_small.png" alt="online">
							{elseif $service.service_status=="offline"}
								<img src="./templates/img/ffmap/status_down_small.png" alt="offline">
							{/if}
						</td>
						<td><a href="./user.php?user_id={$service.user_id}">{$service.nickname}</a></td>
						<td><a href="{$service.combined_url_to_service}">{$service.combined_url_to_service}</a></td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
<p>Keine Services vorhanden</p>
{/if}

<br style="clear: both;">