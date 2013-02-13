<h1>Registrieren</h1>
<p>Wenn du einen Freifunk Knoten betreiben möchtest, musst du dich auf unserem Verwaltungsportal registrieren. Nach Absenden deiner Daten bekommst du eine Bestätigungsmail mit deinem Benutzernamen, deinem Passwort und einem Bestätigungslink über den du deine Registration bestätigen musst.</p>

<h2>Daten</h2>
<form action="./register.php{if isset($smarty.get.openid)}?openid={$smarty.get.openid}{/if}" method="POST">
  {if !empty($smarty.get.openid)}<p>Open-ID:<br><input name="openid" type="text" size="30" maxlength="30" value="{if isset($smarty.get.openid)}{$smarty.get.openid}{/if}"> <a href="./register.php"><img src="./templates/{$template}/img/arrow_undo.png" style="border-style: none;"></a></p>{/if}
  <p>Benutzername:<br><input name="nickname" type="text" size="30" maxlength="30" value="{if isset($smarty.post.nickname)}{$smarty.post.nickname}{/if}"> {if empty($smarty.get.openid)}<a href="./register.php?openid=openid.example.com"><img src="./templates/{$template}/img/openid-logo-small.png" style="border-style: none;"></a>{/if}</p>
  {if empty($smarty.get.openid)}<p>Passwort:<br><input name="password" type="password" size="30" value="{if isset($smarty.post.password)}{$smarty.post.password}{/if}"></p>
  <p>Passwort wiederholen:<br><input name="passwordchk" type="password" size="30" value="{if isset($smarty.post.passwordchk)}{$smarty.post.passwordchk}{/if}"></p>{/if}
  <p>Emailadresse:<br><input name="email" type="text" size="30" maxlength="60" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}"></p>
  {if $enable_network_policy == 'true'}
  <p><input type="checkbox" {if isset($smarty.post.agb)}checked="checked"{/if} name="agb" value="true"> Ich habe die <a href="{$networkpolicy}">Netzwerkpolicy</a> gelesen und erkäre mich bereit den dort beschriebenen Verpflichtungen nachzukommen.</p>
  {/if}
  <p><input type="submit" value="Registrieren"></p>
</form>