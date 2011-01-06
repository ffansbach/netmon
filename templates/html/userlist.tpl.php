<script src="lib/classes/extern/jquery/jquery.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.js"></script>

<link rel="stylesheet" type="text/css" href="templates/css/jquery_data_tables.css">

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#routerlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false
	} );
} );
{/literal}
</script>

<h1>Benutzerliste</h1>
{if !empty($userlist)}
	<div style="display: block; float: left;">
		<table class="display" id="routerlist">
			<thead>
				<tr>
					<th>Benutzer</th>
					<th>Router</th>
					<th>Jabber-ID</th>
					<th>ICQ</th>
					<th>Email</th>
					<th>Dabei seit</th>
				</tr>
			</thead>
			<tbody>
				{foreach key=count item=userlist from=$userlist}
					<tr>
						<td><a href="./user.php?user_id={$userlist.id}">{$userlist.nickname}</a></td>
						<td>{$userlist.routercount}</td>
						<td>{$userlist.jabber}</td>
						<td>{$userlist.icq}</td>
						<td>{$userlist.email}</td>
						<td>{$userlist.create_date|date_format:"%d.%m.%Y"}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
<p>Keine Benutzer vorhanden</p>
{/if}