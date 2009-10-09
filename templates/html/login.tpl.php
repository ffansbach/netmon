<h1>Login</h1>

<p>Bitte logge dich ein, um auf den Verwaltungsbereich zugreifen zu können.</p>

<form action="./login.php?section=login_send" method="POST">
  <p>Benutzername:<br><input name="nickname" type="text" size="30" maxlength="30"></p>
  <p>Passwort:<br><input name="password" type="password" size="30"></p>
  <p><a href="./send_new_password.php">Passort vergessen?</a> <a href="./resend_activation_mail.php">Aktivierungsmail erneut zusenden?</a></p>
  <p><input type="submit" value="Login"></p>
</form>