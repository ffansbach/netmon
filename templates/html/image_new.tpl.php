<h1>Neues Image generieren</h1>

<h2>Hinweis:</h2>
<p>Die Felder unten sind in der Regel vorausgefüllt und müssen nicht geändert werden!</p>

<h2>Daten zur erstellung des Images:</h2>


<form action="./imagemaker.php?section=generate&ip_id={$ip_data.ip_id}" method="POST">
  <p>Routertyp:<br><input name="routertyp" type="text" size="30" maxlength="30"  value="{$routertyp}"></p>
  <p>IP :<br><input name="ip" type="text" size="30" maxlength="30"  value="{$net_prefix}.{$ip_data.ip}"></p>
  <p>Dhcp-start :<br><input name="dhcp_start" type="text" size="30" maxlength="30"  value="{$dhcp_start}"></p>
  <p>Dhcp-limit :<br><input name="dhcp_limit" type="text" size="30" maxlength="30"  value="{$dhcp_limit}"></p>
  <p>Nickname :<br><input name="nickname" type="text" size="30" maxlength="30"  value="{$user_data.nickname}"></p>
  <p>Real Name :<br><input name="realname" type="text" size="30" maxlength="30"  value="{$user_data.vorname} {$user_data.nachname}"></p>
  <p>Email :<br><input name="email" type="text" size="30" maxlength="30"  value="{$user_data.email}"></p>

<p>Build Kommando:<br>
{$build_command}
</p>

  <p><input type="submit" value="Image generieren"></p>
</form>