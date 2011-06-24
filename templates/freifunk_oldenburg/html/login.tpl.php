<h1>Login</h1>

<p>Bitte logge dich ein, um auf den Verwaltungsbereich zugreifen zu können.</p>

<div id="traditionall_login" style="display:block;">
<form action="./login.php?section=login_send" method="POST">
	<p>Benutzername:<br><input name="nickname" type="text" size="30" maxlength="30"> <img src="./templates/img/openid-logo-small.png" onclick="document.getElementById('openid_login').style.display = 'block'; document.getElementById('traditionall_login').style.display = 'none';"></p>
	<p>Passwort:<br><input name="password" type="password" size="30"></p>
	<p><input type="checkbox" name="remember" value="true" checked> Login merken</p>
	
	<p><a href="./send_new_password.php">Passort vergessen?</a> <a href="./resend_activation_mail.php">Aktivierungsmail erneut zusenden?</a></p>
	<p><input type="submit" value="Login"></p>
</form>
</div>

<div id="openid_login" style="display:none;">
<form action="./login.php?section=openid_login_send" method="POST">
	<input type="hidden" name="openid_action" value="login">
	<p>Open-ID:<br><input name="openid_url" type="text" size="30" maxlength="200"> <img src="./templates/img/arrow_undo.png" onclick="document.getElementById('openid_login').style.display = 'none'; document.getElementById('traditionall_login').style.display = 'block';"></p>
	<p><input type="checkbox" name="remember" value="true" checked> Login merken</p>

	<p><input type="submit" value="Login"></p>
</form>
</div>