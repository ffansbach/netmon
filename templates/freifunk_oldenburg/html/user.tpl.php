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
			{ "sType": "html" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "string" }, // zuverlässigkeit need own
			{ "sType": "numeric" },
			{ "sType": "numeric" },
			{ "sType": "numeric" }
		],
		"aaSorting": [[ 0, "asc" ]]
	} );
} );

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
			{ "sType": "html" },
			{ "sType": "html" }
		],
		"aaSorting": [[ 0, "asc" ]]
	} );
} );

$(document).ready(function() {
	$('#domainlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
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

<h1>Benutzerseite von {$user.nickname}</h1>

<div style="width: 100%; overflow: hidden;">
	<div style="float:left; width: 47%;">
		<h2>Daten</h2>
		<p>
			{if $is_logged_in}
				{if $smarty.session.user_id == $user.id}<b>API Key:</b> {$user.api_key}<br>{/if}
				{if !empty($user.vorname) OR !empty($user.nachname)}<b>Name:</b> {$user.vorname} {$user.nachname}<br>{/if}
				{if !empty($user.strasse)}<b>Strasse: </b>{$user.strasse}<br>{/if}
				{if !empty($user.plz) OR !empty($user.ort)}<b>Wohnort:</b>  {$user.plz} {$user.ort}<br>{/if}
				{if !empty($user.telefon)}<b>Telefon:</b> {$user.telefon}<br>{/if}
				{if !empty($user.email)}<b>Email:</b> <a href="mailto:{$user.email}">{$user.email}</a><br>{/if}
				{if !empty($user.jabber)}<b>Jabber-ID:</b> {$user.jabber}<br>{/if}
				{if !empty($user.icq)}<b>ICQ:</b> {$user.icq}<br>{/if}
				{if !empty($user.website)}<b>Website:</b> <a href="{$user.website}">{$user.website}</a><br>{/if}
			{/if}
			{if !empty($user.telefon)}<b>Beschreibung:</b> {$user.about}<br>{/if}
			<b>Anmeldedatum:</b> {$user.create_date|date_format:"%e.%m.%Y %H:%M"} Uhr<br>
			<b>Benutzerrollen:</b>	{assign var="first_role" value=true}
						{foreach item=role from=$user.roles}<!--
							-->{if $role.check}<!--
								-->{if !$first_role}, {/if}<!--
								-->{if $role.role == 3}Benutzer{/if}<!--
								-->{if $role.role == 4}Moderator{/if}<!--
								-->{if $role.role == 5}Administrator{/if}<!--
								-->{if $role.role == 6}Root{/if}<!--
								-->{assign var="first_role" value=false}<!--
							-->{/if}<!--
						-->{/foreach}
		</p>
	</div>
	<div style="float:left; width: 53%;">
		<h2>History</h2>
		{if !empty($user_history)}
			<ul>
				{foreach $user_history as $hist}
					<li>
						<b>{$hist.create_date|date_format:"%e.%m. %H:%M:%S"}:</b> <a href="./router_status.php?router_id={$hist.additional_data.router_id}">Router {$hist.additional_data.hostname}</a> 

						{if isset($hist.data.action) AND $hist.data.action == 'status' AND $hist.data.to == 'online'}
							Router geht <span style="color: #007B0F;">online</span>
						{/if}
						{if isset($hist.data.action) AND $hist.data.action == 'status' AND $hist.data.to == 'offline'}
							Router geht <span style="color: #CB0000;">offline</span>
						{/if}
						{if isset($hist.data.action) AND $hist.data.action == 'reboot'}
							Router wurde <span style="color: #000f9c;">Rebootet</span>
						{/if}
						{if isset($hist.action) AND $hist.action == 'watchdog_ath9k_bug'}
							<a href="./event.php?event_id={$hist.id}">ATH9K Bug registriert</a>
						{/if}
					</li>
				{/foreach}
			</ul>
		{else}
			<p>Keine Daten vorhanden</p>
		{/if}
	</div>
</div>

{if $smarty.get.user_id==$smarty.session.user_id AND empty($routerlist)}
	<h2>Neu bei Freifunk?</h2>
	<p>Du hast noch keine Router angelegt. Wenn dein Router schon angeschlossen ist, kannst du ihn aus dieser Liste übernehmen. Wenn dein Router trotzdem noch nicht in dieser Liste auftaucht, versuche die Seite in ein paar Minuten (5-10) noch einmal neu zu laden.</p>

	{if !empty($routersnotassigned_list)}
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
				{foreach key=count item=router from=$routersnotassigned_list}
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
	{else}
		<p>In der Liste der neuen Router ist derzeit kein Router eingetragen.</p>
	{/if}
{/if}

<h2>Router von {$user.nickname}</h2>
{if !empty($routerlist)}
	<table class="display" id="routerlist" style="width: 100%;">
		<thead>
			<tr>
				<th>Hostname</th>
				<th>O</th>
				<th>Stand</th>
				<th>Technik</th>
				<th>Online</th>
				<th>Uptime</th>
				<th>Clients</th>
				<th>Traffic</th>
			</tr>
		</thead>
		<tbody>
			{foreach $routerlist as $router}
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
					<td value="{math equation='round(x,1)' x=$router.router_reliability.online_percent}">{math equation="round(x,1)" x=$router.router_reliability.online_percent}%</td>
					<td>{math equation="round(x,1)" x=$router.actual_crawl_data.uptime/60/60} Std.</td>
					<td>{$router.client_count}</td>
					<td>{$router.traffic}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>Du hast noch keine Router angelegt.</p>
{/if}

<h2>Dienste von {$user.nickname}</h2>
{if !empty($servicelist)}
	<table class="display" id="servicelist" style="width: 100%;">
		<thead>
			<tr>
				<th>Title</th>
				<th>Router</th>
				<th>Server</th>
				<th>Dienst</th>
				<th>Benutzer</th>
				<th>Link</th>
			</tr>
		</thead>
		<tbody>
			{foreach $servicelist as $service}
				<tr>
					<td><a href="./router_status.php?router_id={$service.router_id}#service_{$service.service_id}">{$service.title}</a></td>
					<td><a href="./router_status.php?router_id={$service.router_id}">{$service.hostname}</a></td>
					<td>
						{if $service.router_status=="online"}
							<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="online">
						{elseif $service.router_status=="offline"}
							<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="offline">
						{/if}
					</td>
					<td>
						{if $service.service_status=="online"}
							<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="online">
						{elseif $service.service_status=="offline"}
							<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="offline">
						{/if}
					</td>
					<td><a href="./user.php?user_id={$service.user_id}">{$service.nickname}</a></td>
					<td><a href="{$service.combined_url_to_service}">{$service.combined_url_to_service}</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>Keine Services vorhanden.</p>
{/if}


<h2>Domains von {$user.nickname}</h2>
{if !empty($dns_hosts)}
	<table class="display" id="domainlist" style="width: 100%;">
		<thead>
			<tr>
				<th>Domain</th>
				<th>IPv4 Adresse</th>
				<th>IPv6 Adresse</th>
				<th>Aktionen</th>
			</tr>
		</thead>
		<tbody>
			{foreach $dns_hosts as $dns_host}
				<tr>
					<td>{$dns_host.host}.{$dns_tld}</td>
					<td>{if $dns_host.ipv4_id!=0}{$dns_host.ipv4_ip}{else}Keine IPv4 Adresse{/if}</td>
					<td>{if $dns_host.ipv6_id!=0}{$dns_host.ipv6_ip}{else}Keine IPv6 Adresse{/if}</td>
					<td><a href="./dnseditor.php?section=edit_host&host_id={$dns_host.id}">Editieren</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>Keine Domains vorhanden.</p>
{/if}