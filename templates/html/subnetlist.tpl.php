<h1>Subetlist</h1>

{if !empty($subnetlist)}
<table>
	<tr>
		<th>#</th>
		<th>Subnet</th>
		<th>Titel</th>
		<th>Verantwortlicher</th>
		<th>Online</th>
		<th>Offline</th>
		<th>Gesamt</th>
		<th>problem</th>
	</tr>

{foreach key=count item=subnetlist from=$subnetlist}
	<tr>
		<td>{$count+1}</td>
		<td><a href="./index.php?get=subnet&id={$subnetlist.id}">{$net_prefix}.{$subnetlist.subnet_ip}</a></td>
		<td>{$subnetlist.title}</td>
		<td><a href="./index.php?get=user&id={$subnetlist.user_id}">{$subnetlist.nickname}</a></td>
		<td style="background: green;">XX</td>
		<td style="background: red;">YY</td>
		<td>{$subnetlist.nodes_in_net}</td>
		<td>Kein Problem</td>
	</tr>
{/foreach}
</table>
{else}
<p>Keine Subnetze vorhanden</p>
{/if}
