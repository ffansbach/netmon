<script src="lib/classes/extern/jquery/jquery.min.js"></script>
<script src="lib/classes/extern/DataTables/jquery.dataTables.min.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('#tld_list').dataTable( {
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false,
		"aoColumns": [ 
			{ "sType": "string" },
			{ "sType": "string" },
			{ "sType": "html" }
		],
		"aaSorting": [[ 0, "desc" ]]
	} );
} );
{/literal}
</script>

<h1>Topleveldomains</h1>

<p>Hier können optional Netzinterne Topleveldomains konfiguriert werden zu denen Benutzer dann eigene Domains für
Services eintragen können.</p>

<h2>Eingetragene Topleveldomains</h2>
{if !empty($tld_list)}
	<table class="display" id="tld_list" style="width: 100%;">
		<thead>
			<tr>
				<th>Topleveldomain</th>
				<th>Angelegt am</th>
				<th>Aktionen</h2>
			</tr>
		</thead>
		<tbody>
			{foreach item=tld from=$tld_list}
				<tr>
					<td>{$tld->getTld()}</td>
					<td>{$tld->getCreateDate()|date_format:"%d.%m.%Y %H:%M"} Uhr</td>
					<td><a href="./config.php?section=insert_delete_tld&tld_id={$tld->getTldId()}">Löschen</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p>Keine Topleveldomains eingetragen.</p>
{/if}

<h2>Neue Topleveldomain eintragen</h2>
<form action="./config.php?section=insert_edit_tlds" method="POST">
	<p><b>Topleveldomain:</b><br><input name="tld" type="text" size="10" value=""></p>
	
	<p><input type="submit" value="Eintragen"></p>
</form>