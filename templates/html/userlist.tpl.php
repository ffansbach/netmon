<h1>Benutzerliste</h1>

<table border="1">
	<tr>
		<th>#</th>
		<th>Nickname</th>
		<th>Anzahl der Nodes</th>
		<th>Dabei seit</th>
	</tr>
{foreach key=count item=userlist from=$userlist}
	<tr>
		<td>{$count+1}</td>
		<td><a href="./user.php?id={$userlist.id}">{$userlist.nickname}</a></td>
		<td>{$userlist.nodecount}</td>
		<td>{$userlist.create_date}</td>
	</tr>
{/foreach}
</table>