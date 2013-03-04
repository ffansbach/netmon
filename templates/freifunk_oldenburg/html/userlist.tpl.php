<script src="lib/classes/extern/jquery/jquery.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.js"></script>

<link rel="stylesheet" type="text/css" href="templates/css/jquery_data_tables.css">

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#routerlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"bAutoWidth": false
	} );
} );
{/literal}
</script>

<h1>Benutzerliste</h1>
{if !empty($userlist)}
	<table class="display" id="routerlist" style="width: 100%">
		<thead>
			<tr>
				<th>Benutzer</th>
				<th>Router</th>
				<th>Jabber-ID</th>
				<th>Email</th>
				<th>Benutzerrollen</th>
				<th>Dabei seit</th>
			</tr>
		</thead>
		<tbody>
			{foreach $userlist as $user}
				<tr>
					<td><a href="./user.php?user_id={$user.id}">{$user.nickname}</a></td>
					<td>{$user.routercount}</td>
					<td>{$user.jabber}</td>
					<td><a href="mailto:{$user.email}">{$user.email}</a></td>
					<td>
						{assign var="first_role" value=true}
						{foreach item=role from=$user.roles}<!--
							-->{if $role.check}<!--
								-->{if !$first_role}, {/if}<!--
								-->{if $role.role == 3}Benutzer{/if}<!--
								-->{if $role.role == 4}Moderator{/if}<!--
								-->{if $role.role == 5}Administrator{/if}<!--
								-->{if $role.role == 6}Root{/if}<!--
								-->{assign var="first_role" value=false}<!--
							-->{/if}<!--
						-->{/foreach}
					</td>
					<td>{$user.create_date|date_format:"%d.%m.%Y"}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<h2>Export</h2>
	<p><a href="./userlist.php?section=export_vcard30">Benutzerliste als vCcard 3.0 exportieren</a></p>
{else}
<p>Keine Benutzer vorhanden</p>
{/if}