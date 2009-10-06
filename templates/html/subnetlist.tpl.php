<h1>Liste der vorhandenen Subnetze</h1>

{if !empty($subnetlist)}
<table>
	<tr>
		<th>Subnet</th>
		<th>Name</th>
		<th>Benutzer</th>
		<th>Ips im Netz</th>
	</tr>

{foreach key=count item=subnetlist from=$subnetlist}
	<tr>
		<td><a href="./subnet.php?id={$subnetlist.id}">{$net_prefix}.{$subnetlist.subnet_ip}.0/24</a></td>
		<td>{$subnetlist.title}</td>
		<td><a href="./user.php?id={$subnetlist.user_id}">{$subnetlist.nickname}</a></td>
		<td>{$subnetlist.ips_in_net}</td>
	</tr>
{/foreach}
</table>
{else}
<p>Keine Subnetze vorhanden</p>
{/if}
