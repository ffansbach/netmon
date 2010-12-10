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
				{foreach item=hist from=$user_history}
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
<div id="ipitem" style="width: 760px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 120px;"><b>Hostname</b></div>
    <div style="float:left; width: 60px;"><b>Status</b></div>
    <div style="float:left; width: 80px;"><b>Stand</b></div>
<!--    <div style="float:left; width: 220px;"><b>Standort</b></div>-->
    <div style="float:left; width: 130px;"><b>Technik</b></div>
    <div style="float:left; width: 85px;"><b>Benutzer</b></div>
    <div style="float:left; width: 120px;"><b>Zuverlässigkeit</b></div>
    <div style="float:left; width: 85px;"><b>Uptime</b></div>
  </div>
</div>

{foreach key=count item=router from=$routerlist}
<div id="ipitem" style="width: 760px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 120px;"><a href="./router_status.php?router_id={$router.router_id}">{$router.hostname}</a></div>
    <div style="float:left; width: 60px;">
    {if $router.actual_crawl_data.status=="online"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_up_small.png" alt="online"></div>
    {elseif $router.actual_crawl_data.status=="offline"}
      <div style="float:left; width: 60px; align: center;"><img src="./templates/img/ffmap/status_down_small.png" alt="offline"></div>
    {/if}
    </div>
    <div style="float:left; width: 80px;">{$router.actual_crawl_data.crawl_date|date_format:"%H:%M"} Uhr</div>
<!--    <div style="float:left; width: 220px;">{$router.short_location}...</div>-->
    <div style="float:left; width: 130px;">{$router.chipset_name}</div>
    <div style="float:left; width: 85px;"><a href="./user.php?user_id={$router.user_id}">{$router.nickname}</a></div>
    <div style="float:left; width: 120px;">{$router.router_reliability.online_percent}% online</div>
    <div style="float:left; width: 85px;">{$router.actual_crawl_data.uptime/60/60|round:1} Stunden</div>

  </div>
</div>
{/foreach}
{else}
<p>Keine Router vorhanden</p>
{/if}

<h2>Liste der Dienste von {$user.nickname}</h2>
{if !empty($servicelist)}
<div id="ipitem" style="width: 760px; overflow: hidden;">
  <div nstyle="white-space: nowrap;">
    <div style="float:left; width: 240px;"><b>Title</b></div>
    <div style="float:left; width: 110px;"><b>Router</b></div>
    <div style="float:left; width: 80px;"><b>R-Status</b></div>
    <div style="float:left; width: 80px;"><b>S-Status</b></div>
    <div style="float:left; width: 85px;"><b>Benutzer</b></div>
    <div style="float:left; width: 100px;"><b>Link</b></div>
  </div>
</div>

{foreach key=count item=service from=$servicelist}
<div id="ipitem" style="width: 760px; overflow: hidden;">
  <div style="white-space: nowrap;">
    <div style="float:left; width: 240px;"><a href="./router_config.php?router_id={$service.router_id}#service_{$service.service_id}">{$service.title}</a></div>
    <div style="float:left; width: 110px;"><a href="./router_config.php?router_id={$service.router_id}">{$service.hostname}</a></div>
    <div style="float:left; width: 80px;">
		{if $service.router_status=="online"}
			<img src="./templates/img/ffmap/status_up_small.png" alt="online">
		{elseif $service.router_status=="offline"}
			<img src="./templates/img/ffmap/status_down_small.png" alt="offline">
		{/if}
    </div>
    <div style="float:left; width: 80px;">
		{if $service.service_status=="online"}
			<img src="./templates/img/ffmap/status_up_small.png" alt="online">
		{elseif $service.service_status=="offline"}
			<img src="./templates/img/ffmap/status_down_small.png" alt="offline">
		{/if}
</div>
    <div style="float:left; width: 85px;"><a href="./user.php?user_id={$service.user_id}">{$service.nickname}</a></div>
    <div style="float:left; width: 100px;"><a href="{$service.combined_url_to_service}">{$service.combined_url_to_service}</a></div>
  </div>
</div>
{/foreach}
{else}
<p>Keine Services vorhanden</p>
{/if}




<!--<h2>Ips von {$user.nickname}</h2>
{if empty($iplist)}
<p>Dieser Benutzer besitzt keine Ips.</p>
{else}
<table border="1">
	<tr>
		<th>#</th>
		<th>Ip-IP</th>
		<th>Aktionen</th>
	</tr>
{foreach key=count item=iplist from=$iplist}
	<tr>
		<td>{$count+1}</td>
		<td><a href="./ip.php?id={$iplist.id}">{$net_prefix}.{$iplist.ip}</a></td>
		<td>
		  {if $iplist.is_ip_owner}
		    <a href="./ipeditor.php?section=edit&id={$iplist.id}">Editieren</a>
		  {/if}
		</td>
	</tr>
{/foreach}
</table>
{/if}

<h2>Projekte die von {$user.nickname} verwaltet werden</h2>
{if empty($subnetlist)}
<p>Dieser Benutzer verwaltet keine Projekte</p>
{else}
<table border="1">
	<tr>
		<th>#</th>
		<th>Subnet</th>
		<th>Titel</th>
		<th>Ips</th>
		<th>Aktionen</th>
	</tr>
{foreach key=count item=subnetlist from=$subnetlist}
	<tr>
		<td>{$count+1}</td>
		<td><a href="./subnet.php?id={$subnetlist.id}">{$net_prefix}.{$subnetlist.host}/{$subnetlist.netmask}</a></td>
		<td>{$subnetlist.title}</td>
		<td>{$subnetlist.ips_in_net}</td>
		<td><a href="./subneteditor.php?section=edit&id={$subnetlist.id}">Editieren</a></td>
	</tr>
{/foreach}
</table>
{/if}-->

{if $permitted}
<p><a href="./user_edit.php?section=edit&id={$smarty.get.id}">Benutzer editieren</a></p>
{/if}