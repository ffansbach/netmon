<h1>Registrieren</h1>

<p>Wenn du bei {$project_name} einen Freifunk Knoten betreiben willst, musst du dich auf unserem Verwaltungsportal registrieren.<br>
Nach Absenden deiner Daten bekommst du eine Bestätigungsmail mit deinem Benutzernamen, deinem Passwort und einem Bestätigungslink über den du deine Registration bestätigen musst.
</p>

<h2>Daten</h2>

<form action="./register.php" method="POST">
  <p>Benutzername:<br><input name="nickname" type="text" size="30" maxlength="30" value="{$smarty.post.nickname}"></p>
  <p>Passwort:<br><input name="password" type="password" size="30" value="{$smarty.post.password}"></p>
  <p>Passwort wiederholen:<br><input name="passwordchk" type="password" size="30" value="{$smarty.post.passwordchk}"></p>
  <p>Emailadresse:<br><input name="email" type="text" size="30" maxlength="60" value="{$smarty.post.email}"></p>
  <p><input type="checkbox" {if isset($smarty.post.agb)}checked="checked"{/if} name="agb" value="true"> Ich habe die <a href="{$networkpolicy}">Netzwerkpolicy</a> gelesen und erkäre mich bereit den dort beschriebenen Verpflichtungen nachzukommen.</p>
  <p><input type="submit" value="Absenden"></p>
</form>