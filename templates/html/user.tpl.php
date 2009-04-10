<h1>Benutzer {$user.nickname}</h1>

{if !empty($user.vorname)}Vorname: {$user.vorname}, {/if}{if !empty($user.nachname)}nachname: {$user.nachname}<br>{/if}

{if !empty($user.strasse)}Strasse: {$user.strasse}<br>{/if}
{if !empty($user.plz)}Plz:  {$user.plz}, {/if}{if !empty($user.ort)}ort: {$user.ort}</br>{/if}
{if !empty($user.telefon)}Telefon: {$user.telefon}<br>{/if}
Email: <a href="mailto:{$user.email}">{$user.email}</a><br>

{if !empty($user.jabber)}Jabber-ID: {$user.jabber}<br>{/if}
{if !empty($user.icq)}ICQ: {$user.icq}<br>{/if}
{if !empty($user.website)}Website: <a href="{$user.website}">{$user.website}</a><br>{/if}

{if !empty($user.telefon)}<h2>Beschreibung:</h2><p> {$user.about}</p>{/if}

Ich bin seit dem {$user.create_date} dabei.<br>

<h2>Nodes von {$user.nickname}</h2>
{if empty($nodelist)}
<p>Dieser Benutzer besitzt keine Nodes.<br>
Oder ein neu angelegter Node wurde noch icht gecrawlt (Lösung: {$timeBetweenCrawls} Minuten warten!)</p>
{else}
<table border="1">
	<tr>
		<th>#</th>
		<th>Node-IP</th>
		<th>Aktionen</th>
	</tr>
{foreach key=count item=nodelist from=$nodelist}
	<tr>
		<td>{$count+1}</td>
		<td><a href="./index.php?get=node&id={$nodelist.id}">{$net_prefix}.{$nodelist.subnet_ip}.{$nodelist.node_ip}</a></td>
		<td>
		  {if $nodelist.is_node_owner}
		    <a href="./index.php?get=nodeeditor&section=edit&id={$nodelist.id}">Editieren</a>
		  {/if}
		</td>
	</tr>
{/foreach}
</table>
{/if}

<h2>Subnetze die von {$user.nickname} verwaltet werden</h2>
{if empty($subnetlist)}
<p>Dieser Benutzer verwaltet keine Subnetze</p>
{else}
<table border="1">
	<tr>
		<th>#</th>
		<th>Subnet</th>
		<th>Titel</th>
		<th>Nodes</th>
		<th>Aktionen</th>
	</tr>
{foreach key=count item=subnetlist from=$subnetlist}
	<tr>
		<td>{$count+1}</td>
		<td><a href="./index.php?get=subnet&id={$subnetlist.id}">{$net_prefix}.{$subnetlist.subnet}.0/24</a></td>
		<td>{$subnetlist.title}</td>
		<td>{$subnetlist.nodes_in_net}</td>
		<td><a href="./index.php?get=subneteditor&section=edit&id={$subnetlist.id}">Editieren</a></td>
	</tr>
{/foreach}
</table>
{/if}