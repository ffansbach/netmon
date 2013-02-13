<h1>Installation der Datenbank</h1>
<form action="./install.php?section=check_connection" method="POST">
	<h2>Datenbank Informationen</h2>
	<p>Server:<br><input name="mysql_host" type="text" size="30" value="localhost"></p>
	<p>Datenbank:<br><input name="mysql_db" type="text" size="30"></p>
	<h2>Datenbank Benutzer</h2>
	<p>Benutzername:<br><input name="mysql_user" type="text" size="30"></p>
	<p>Passwort:<br><input name="mysql_password" type="password" size="30"></p>
  <p><input type="submit" value="Weiter"></p>
</form>