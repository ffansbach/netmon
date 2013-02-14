<h1>Konfiguration des Mailversands</h1>
<form action="./config.php?section=insert_edit_email" method="POST">
	<h2>Abesender</h2>
	<p><b>Absenderadresse:</b><br><input name="mail_sender_adress" type="text" size="30" value="{$mail_sender_adress}"></p>
	<p>Absendername:<br><input name="mail_sender_name" type="text" size="30" value="{$mail_sender_name}"></p>

	<h2>Versandart</h2>
	<p>Versandart: <select name="mail_sending_type" onChange="if(this.options[this.selectedIndex].value=='smtp') { document.getElementById('smtp_config').style.display = 'block'; } else { document.getElementById('smtp_config').style.display = 'none';}">
					<option value="php_mail" {if $mail_sending_type == 'php_mail'}selected{/if}>php_mail</option>
					<option value="smtp" {if $mail_sending_type == 'smtp'}selected{/if}>smtp</option>
			      </select>
	</p>
	<p>SMTP Mail Ausgangsserver (z.B. mail.gmx.de)<br><input name="mail_smtp_server" type="text" size="30" value="{$mail_smtp_server}"></p>
	<p>SMTP Username (z.B. netmon@gmx.de):<br><input name="mail_smtp_username" type="text" size="30" value="{$mail_smtp_username}"></p>
	<p>SMTP Passwort:<br><input name="mail_smtp_password" type="password" size="30" value="{$mail_smtp_password}"></p>
	<p>SMTP-Login Methode:<br><input name="mail_smtp_login_auth" type="text" size="30" value="{$mail_smtp_login_auth}"></p>
	<p>SMTP-SSL Typ:<br><input name="mail_smtp_ssl" type="text" size="30" value="{$mail_smtp_ssl}"></p>

	<p><input type="submit" value="Absenden"></p>
</form>