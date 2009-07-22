<h1>Neues VPN-Zertifikat generieren</h1>

<h2>Hinweis:</h2>
<p>Alte in der Datenbank gespeicherte Zertifikate werden überschrieben!</p>

<h2>Daten zur erstellung des Zertifikats:</h2>

<form action="./vpn.php?section=generate&node_id={$data.node_id}" method="POST">
  <p>Organisationsbereich (Nickname):<br><input name="organizationalunitname" type="text" size="30" maxlength="30"  value="{$data.nickname}"></p>
  <p>Common Name (Node-ID, muss eindeutig sein zwecks IP-Zuweisung!):<br><input name="commonname" type="text" size="30" maxlength="30"  value="{$data.node_id}"></p>
  <p>Email:<br><input name="emailaddress" type="text" size="30" maxlength="30"  value="{$data.email}"></p>
  <p>Passphrase (Passwort):<br><input name="privkeypass" type="password" size="30" maxlength="30"></p>
  <p>Passphrase wiederholen:<br><input name="privkeypass_chk" type="password" size="30" maxlength="30"></p>
  <p>Gültigk in Tagen:<br><input name="numberofdays" type="text" size="30" maxlength="30"  value="{$expiration}"></p>
  <p><input type="submit" value="Zertifikat generieren"></p>
</form>