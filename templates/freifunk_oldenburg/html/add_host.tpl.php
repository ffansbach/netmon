<h1>Eine neue Domain anlegen</h1>

<form action="./dnseditor.php?section=insert_add&user_id={$user_id}" method="POST">
	<h2>Angaben zum Betrieb</h2>
	<p>
		Hostname: <input name="host" type="text" size="20" maxlength="100" value="">.ffol
	</p>

	<p>IPv4 Adresesse
		<select name="ipv4_id" size="1">
			<option value="" selected>Keine</option>
		</select>
	</p>

	<p>IPv6 Adresesse
		<select name="ipv6_id" size="1">
			<option value="" selected>Keine</option>
		</select>
	</p>



	<h2>Port und URL</h2>
	<p>
		Portnummer: <input name="port" type="text" size="5" maxlength="10" value="">
	</p>
	
	<p><input type="submit" value="Absenden"></p>
</form>