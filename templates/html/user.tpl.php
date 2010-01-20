<h1>Benutzer {$user.nickname}</h1>

{if !empty($user.vorname)}Vorname: {$user.vorname}, {/if}{if !empty($user.nachname)}Nachname: {$user.nachname}<br>{/if}

{if !empty($user.strasse)}Strasse: {$user.strasse}<br>{/if}
{if !empty($user.plz)}Plz:  {$user.plz}, {/if}{if !empty($user.ort)}Ort: {$user.ort}<br>{/if}
{if !empty($user.telefon)}Telefon: {$user.telefon}<br>{/if}
Email: <a href="mailto:{$user.email}">{$user.email}</a><br>

{if !empty($user.jabber)}Jabber-ID: {$user.jabber}<br>{/if}
{if !empty($user.icq)}ICQ: {$user.icq}<br>{/if}
{if !empty($user.website)}Website: <a href="{$user.website}">{$user.website}</a><br>{/if}

{if !empty($user.telefon)}<h2>Beschreibung:</h2><p> {$user.about}</p>{/if}

Ich bin seit dem {$user.create_date} dabei.<br>

<h2>Ips von {$user.nickname}</h2>
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
{/if}