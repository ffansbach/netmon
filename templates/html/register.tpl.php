<form action="./register.php" method="POST">
  <p>Benutzername:<br><input name="nickname" type="text" size="30" maxlength="30" value="{$smarty.post.nickname}"></p>
  <p>Passwort:<br><input name="password" type="password" size="30" value="{$smarty.post.password}"></p>
  <p>Passwort wiederholen:<br><input name="passwordchk" type="password" size="30" value="{$smarty.post.passwordchk}"></p>
  <p>Emailadresse:<br><input name="email" type="text" size="30" maxlength="60" value="{$smarty.post.email}"></p>
  <p><input type="checkbox" {if isset($smarty.post.agb)}checked="checked"{/if} name="agb" value="true"> Ich bin mit der <a href="{$networkpolicy}">Netzwerkpolicy</a> einverstanden und habe den den Abschnitt <a href="http://freifunk.nord-west.net/index.php/Wie_werde_ich_Freifunker#Rechte_und_Pflichten_der_Teilnehmer">Rechte und Plichten der Teilnehmer</a> im Wiki gelesen</p>
  <p><input type="submit" value="Absenden"></p>
</form>