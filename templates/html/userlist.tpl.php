<h1>Nodelist</h1>

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
		<td><a href="./index.php?get=user&id={$userlist.id}">{$userlist.nickname}</a></td>
		<td>XX</td>
		<td>{$userlist.create_date}</td>
	</tr>
{/foreach}
</table>