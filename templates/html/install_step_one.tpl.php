<h1>Netmon installieren:</h1>

<h2>Informationen zur aktuellen Netmon Version</h2>
<p><b>Netmon Version:</b> {$netmon_version}<br>
<b>Codename</b> {$netmon_codename}</p>

<h2>Systemcheck</h2>

<p>
PDO: {if $pdo_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
PDO MySQL: {if $pdo_mysql_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
Json: {if $json_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
Curl: {if $curl_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
GD: {if $dg_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}
</p>

{if !$pdo_loaded OR !$pdo_mysql_loaded OR !$json_loaded OR !$curl_loaded OR !$dg_loaded}
<div class="error">Einige benötigte Funktionen sind inaktiv, es kann zu Problemen bei Installation und Betrieb kommen!</div>
{/if}

<form action="./install.php.php?section=db" method="POST">
	<h2>Datenbank</h2>
	<p>Server:<br><input name="server" type="text" size="30" value="localhost"></p>
	<p>Benutzername:<br><input name="nickname" type="text" size="30"></p>
	<p>Passwort:<br><input name="password" type="password" size="30"></p>
	<p>Datenbank:<br><input name="database" type="text" size="30"></p>

  <p><input type="submit" value="Absenden"></p>
</form>