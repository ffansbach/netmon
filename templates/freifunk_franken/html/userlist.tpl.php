<script src="lib/extern/jquery/jquery.js"></script>
<script src="lib/extern/DataTables/jquery.dataTables.js"></script>

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
				{foreach $userlist as $user}
					<tr>
						<td><a href="./user.php?user_id={$user.id}">{$user.nickname}</a></td>
						<td>{$user.routercount}</td>
						<td>{$user.jabber}</td>
						<td>{$user.icq}</td>
						<td>{$user.email}</td>
						<td>{$user.create_date|date_format:"%d.%m.%Y"}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	<br style="clear: both">

<p><a href="./userlist.php?section=export_vcard30">Emailadressen als vCcard 3.0 exportieren</a></p>
{else}
<p>Keine Benutzer vorhanden</p>
{/if}