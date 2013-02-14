<h1>Konfiguration der Datenbankanbindung</h1>
<form action="./config.php?section=insert_edit" method="POST">
	<p>Host:<br><input name="mysql_host" type="text" size="30" value="{$mysql_host}"></p>
	<p>Datenbank:<br><input name="mysql_db" type="text" size="30" value="{$mysql_db}"></p>
	<p>Benutzer:<br><input name="mysql_user" type="text" size="30" value="{$mysql_user}"></p>
	<p>Passwort:<br><input name="mysql_password" type="text" size="30" value="{$mysql_password}"></p>

	<p><input type="submit" value="Absenden"></p>
</form>