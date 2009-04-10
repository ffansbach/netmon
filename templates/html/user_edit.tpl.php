<form action="./index.php?get=user&section=insert_edit&id={$user.id}" method="POST">

  <h1>Daten von {$user.nickname} ändern</h1>
  
  <h2>Passwort</h2>

  Passwort ändern? <input type="checkbox" name="changepassword" value="true">

  <p>Altes Passwort:<br><input name="oldpassword" type="password" size="30"></p>

  <p>Neues Passwort:<br><input name="newpassword" type="password" size="30"></p>
  <p>Neues Passwort wiederholen:<br><input name="newpasswordchk" type="password" size="30"></p>

  <hr>

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

  <p><input type="submit" value="Absenden"></p>
</form>

  <hr>

<form action="./index.php?get=user&section=delete&id={$user.id}" method="POST">
  <h2>Benutzer Löschen?</h2>
  Ja <input type="checkbox" name="delete" value="true">
  <p><input type="submit" value="Löschen!"></p>
</form>