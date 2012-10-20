<h1>Projektliste</h1>

{if !empty($subnetlist)}
<table>
	<tr>
		<th>Subnet</th>
		<th>Projekt</th>
		<th>Benutzer</th>
		<th>Ips im Netz</th>
	</tr>

{foreach key=count item=subnetlist from=$subnetlist}
	<tr>
		<td><a href="./subnet.php?id={$subnetlist.id}">{$net_prefix}.{$subnetlist.host}/{$subnetlist.netmask}</a></td>
		<td>{$subnetlist.title}</td>
		<td><a href="./user.php?id={$subnetlist.user_id}">{$subnetlist.nickname}</a></td>
		<td>{$subnetlist.ips_in_net}</td>
	</tr>
{/foreach}
</table>
{else}
<p>Keine Projekte vorhanden</p>
{/if}
