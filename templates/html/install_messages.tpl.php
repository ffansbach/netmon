<h1>Netmon installieren:</h1>

<h2>Nachrichten</h2>
<form action="./install.php?section=messages_insert" method="POST">
	<h3>Jabber/XMTP</h3>
	<p>Server:<br><input name="jabber_server" type="text" size="30"></p>
	<p>Benutzername:<br><input name="jabber_username" type="text" size="30"></p>
	<p>Passwort:<br><input name="jabber_password" type="password" size="30"></p>


	<h3>Email</h3>
	<p>Mail Absender:<br><input name="mail_sender" type="text" size="50" value=""></p>

	{if !$mail}
	<h3>SMTP-Transport</h3>
<!--	<p>Datenbank:<br><input name="database" type="text" size="30"></p>

	<p>Benutzername:<br><input name="user" type="text" size="30"></p>
	<p>Passwort:<br><input name="password" type="password" size="30"></p>-->
	{/if}
  <p><input type="submit" value="Weiter"></p>
</form>