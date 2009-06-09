<h1>Subetlist</h1>

{if !empty($subnetlist)}
<table>
	<tr>
		<th>#</th>
		<th>Subnet</th>
		<th>Titel</th>
		<th>Verantwortlicher</th>
		<th>Nodes im Netz</th>
	</tr>

{foreach key=count item=subnetlist from=$subnetlist}
	<tr>
		<td>{$count+1}</td>
		<td><a href="./index.php?get=subnet&id={$subnetlist.id}">{$net_prefix}.{$subnetlist.subnet_ip}</a></td>
		<td>{$subnetlist.title}</td>
		<td><a href="./index.php?get=user&id={$subnetlist.user_id}">{$subnetlist.nickname}</a></td>
		<td>{$subnetlist.nodes_in_net}</td>
	</tr>
{/foreach}
</table>
{else}
<p>Keine Subnetze vorhanden</p>
{/if}
