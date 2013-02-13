<h1>Host {$host_data.host} editieren:</h1>

<h2>Host löschen</h2>
<form action="./dnseditor.php?section=delete_host&host_id={$host_data.id}" method="POST">
	<input name="really_delete" type="checkbox" value="1"> Host {$host_data.host} wirklich löschen?
	<p><input type="submit" value="Löschen"></p>
</form>