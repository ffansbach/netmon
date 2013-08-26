<script src="lib/extern/jquery/jquery.js"></script>
<script src="lib/extern/DataTables/jquery.dataTables.js"></script>

<link rel="stylesheet" type="text/css" href="templates/css/jquery_data_tables.css">

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#projectlist').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false
	} );
} );
{/literal}
</script>

<h1>Liste der Projekte</h1>
{if !empty($projectlist)}
	<div style="display: block; float: left;">
		<table class="display" id="projectlist">
			<thead>
				<tr>
					<th>Projektname</th>
					<th>Bat. adv.</th>
					<th>Olsr</th>
					<th>Wlan</th>
					<th>VPN</th>
					<th>IPv4</th>
					<th>IPv6</th>
					<th>Benutzer</th>
				</tr>
			</thead>
			<tbody>
				{foreach $projectlist as $project}
					<tr>
						<td><a href="./project.php?project_id={$project.project_id}">{$project.title}</a></td>
						<td>{if $project.is_batman_adv=='1'}Ja{else}Nein{/if}</td>
						<td>{if $project.is_olsr=='1'}Ja{else}Nein{/if}</td>
						<td>{if $project.is_wlan=='1'}Ja{else}Nein{/if}</td>
						<td>{if $project.is_vpn=='1'}Ja{else}Nein{/if}</td>
						<td>{if $project.is_ipv4=='1'}Ja{else}Nein{/if}</td>
						<td>{if $project.is_ipv6=='1'}Ja{else}Nein{/if}</td>
						<td><a href="./user.php?user_id={$project.user_id}">{$project.nickname}</a></td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
<p>Keine Router vorhanden</p>
{/if}