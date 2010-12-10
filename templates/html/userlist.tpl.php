<h1>Benutzerliste</h1>

<table border="1">
	<tr>
		<th>#</th>
		<th>Benutzer</th>
		<th>Ips</th>
		<th>Jabber-ID</th>
		<th>ICQ</th>
		<th>Email</th>
		<th>Dabei seit</th>
	</tr>
{foreach key=count item=userlist from=$userlist}
	<tr>
		<td>{$count+1}</td>
		<td><a href="./user.php?user_id={$userlist.id}">{$userlist.nickname}</a></td>
		<td>{$userlist.ipcount}</td>
		<td>{$userlist.jabber}</td>
		<td>{$userlist.icq}</td>
		<td>{$userlist.email}</td>
		<td>{$userlist.create_date} Uhr</td>
	</tr>
{/foreach}
</table>