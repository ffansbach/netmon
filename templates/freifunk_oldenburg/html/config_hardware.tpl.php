<h1>Konfiguration der Hardwarenamen</h1>
<h2>Vorhandene Chipsets</h2>
{if !empty($chipsets_with_name)}
<table class="display" id="routerlist">
	<thead>
		<tr>
			<th>Chipset</th>
			<th>Name</th>
			<th>Erstellt</th>
			<th>Aktionen</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=count item=chipset from=$chipsets_with_name}
			<tr>
				<td>{$chipset.name}</td>
				<td>{$chipset.hardware_name}</td>
				<td>{$chipset.create_date|date_format:"%d.%m.%Y %H:%M"} Uhr</td>
				<td><a href="./config.php?section=edit_hardware_name&chipset_id={$chipset.id}">Editieren</a></td>
			</tr>
		{/foreach}
	</tbody>
</table>
{else}
	<p>Keine Chipsets vorhanden. Neue Chipsets werden wärend der Crawls automatisch hinzugefügt und können dann zugewiesen werden.</p>
{/if}


<h2>Nicht Zugewiesene Chipsets</h2>
{if !empty($chipsets_without_name)}
<table class="display" id="routerlist">
	<thead>
		<tr>
			<th>Chipset</th>
			<th>Erstellt</th>
			<th>Aktionen</th>
		</tr>
	</thead>
	<tbody>
		{foreach key=count item=chipset from=$chipsets_without_name}
			<tr>
				<td>{$chipset.name}</td>
				<td>{$chipset.create_date|date_format:"%d.%m.%Y %H:%M"} Uhr</td>
				<td><a href="./config.php?section=edit_hardware_name&chipset_id={$chipset.id}">Editieren</a></td>
			</tr>
		{/foreach}
	</tbody>
</table>
{else}
	<p>Keine nicht zugewiesenen Chipsets vorhanden.</p>
{/if}