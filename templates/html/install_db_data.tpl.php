<h1>Netmon installieren:</h1>

<h2>Installation der Datenbank</h2>
<form action="./install.php?section=check_connection" method="POST">
	<h3>Datenbank Informationen</h3>
	<p>Server:<br><input name="host" type="text" size="30" value="localhost"></p>
	<p>Datenbank:<br><input name="database" type="text" size="30"></p>
	<h3>Datenbank Benutzer</h3>
	<p>Benutzername:<br><input name="user" type="text" size="30"></p>
	<p>Passwort:<br><input name="password" type="password" size="30"></p>
  <p><input type="submit" value="Weiter"></p>
</form>