<h1>Netmon installieren:</h1>

<h2>Nachrichten</h2>
<form action="./install.php?section=messages_insert" method="POST">
	<h3>Jabber/XMTP</h3>
	<p>Server:<br><input name="jabber_server" type="text" size="30"></p>
	<p>Benutzername:<br><input name="jabber_username" type="text" size="30"></p>
	<p>Passwort:<br><input name="jabber_password" type="password" size="30"></p>


	<h3>Email</h3>
	<p>Absenderadresse:<br><input name="mail_sender_adress" type="text" size="30"></p>
	<p>Absendername:<br><input name="mail_sender_name" type="text" size="30"></p>

	{if !$mail}
	<h3>SMTP-Transport</h3>
	<p><input type="checkbox" name="mail_sending_type" value="true" checked> Benutze SMTP-Transport zum Senden von Emails</p>
	<p>SMTP-Server:<br><input name="mail_smtp_server" type="text" size="30"></p>
	<p>SMTP-Benutzername:<br><input name="mail_smtp_username" type="text" size="30"></p>
	<p>SMTP-Passwort:<br><input name="mail_smtp_password" type="password" size="30"></p>
	<p>SMTP-Login Methode: (Leer lassen wenn nicht benötigt)<br><input name="mail_smtp_login_auth" type="text" size="30"></p>
	<p>SMTP-SSL Typ: (Leer lassen wenn nicht benötigt)<br><input name="mail_smtp_ssl" type="text" size="30"></p>
	{/if}
  <p><input type="submit" value="Weiter"></p>
</form>