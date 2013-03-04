<form action="./user_edit.php?section=insert_edit&user_id={$user.id}" method="POST">

  <h1>Daten von {$user.nickname} ändern</h1>
  
  <h2>Passwort</h2>

  Passwort ändern? <input type="checkbox" name="changepassword" value="true">

  <p>Altes Passwort:<br><input name="oldpassword" type="password" size="30"></p>

  <p>Neues Passwort:<br><input name="newpassword" type="password" size="30"></p>
  <p>Neues Passwort wiederholen:<br><input name="newpasswordchk" type="password" size="30"></p>

  <hr>

	{if $is_root}
		<h2>Administrative Einstellungen</h2>
		<h3>Rechtevergabe</h3>
		<p>
			{foreach item=permission from=$permissions}
				<input type="checkbox" name="permission[]" value="{$permission.dual}" {if $permission.check}checked{/if}> {if $permission.role == 3}Benutzer{/if}{if $permission.role == 4}Moderator{/if}{if $permission.role == 5}Administrator{/if}{if $permission.role == 6}Root{/if}<br>
			{/foreach}
		</p>
		<hr>
	{/if}

  <h2>Open-ID</h2>
  <p>Open-ID (Login über Open-ID):<br><input name="openid" type="text" size="30" value="{$user.openid}"></p>

  <h2>Grunddaten</h2>

  <p>Email:<br><input name="email" type="text" size="30" value="{$user.email}"></p>

  <p>Name:<br><input name="nachname" type="text" size="30" value="{$user.nachname}"></p>
  <p>Vorname:<br><input name="vorname" type="text" size="30" value="{$user.vorname}"></p>
  <p>Straße:<br><input name="strasse" type="text" size="30" value="{$user.strasse}"></p>
  <p>Plz:<br><input name="plz" type="text" size="30" value="{$user.plz}"></p>
  <p>Ort:<br><input name="ort" type="text" size="30" value="{$user.ort}"></p>
  <p>Telefon:<br><input name="telefon" type="text" size="30" value="{$user.telefon}"></p>

  <p>Jabber:<br><input name="jabber" type="text" size="30" value="{$user.jabber}"></p>
  <p>ICQ:<br><input name="icq" type="text" size="30" value="{$user.icq}"></p>
  <p>Website:<br><input name="website" type="text" size="30" value="{$user.website}"></p>

  <p>About:<br><textarea name="about" cols="50" rows="10">{$user.about}</textarea></p>

	<h2>Benachrichtigungen</h2>
	<p>Benachrichtigungen sollen mir per 
    <select name="notification_method" size="1">
      <option value="email" {if $user.notification_method=='email'}selected{/if}>Email</option>
      <option value="jabber" {if $user.notification_method=='jabber'}selected{/if}>Jabber</option>
    </select> gesendet werden.
	</p>

  <p><input type="submit" value="Absenden"></p>
</form>

  <hr>

<form action="./user_edit.php?section=delete&user_id={$user.id}" method="POST">
  <h2>Benutzer Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <input type="hidden" name="user_id" value="{$smarty.get.user_id}">
  <p><input type="submit" value="Löschen!"></p>
</form>