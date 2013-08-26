<script src="lib/extern/jquery/jquery.min.js"></script>
<script src="lib/extern/DataTables/jquery.dataTables.min.js"></script>

<script type="text/javascript">
{literal}
jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
    var ukDatea = a.split('.');
    var ukDateb = b.split('.');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
    var ukDatea = a.split('.');
    var ukDateb = b.split('.');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};

$(document).ready(function() {
	$('#routerlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
			{ "sType": "html" },
			{ "sType": "numeric" },
			{ "sType": "string" },
			{ "sType": "html" },
			{ "sType": "string" },
			{ "sType": "uk_date" }
		],
		"aaSorting": [[ 0, "asc" ]]
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