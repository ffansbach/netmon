<h1>Netmon installieren:</h1>

<h2>Informationen zur aktuellen Netmon Version</h2>
<p><b>Netmon Version:</b> {$netmon_version}<br>
<b>Codename</b> {$netmon_codename}</p>

<h2>Informationen im Web</h2>
<ul>
<li><a href="http://oldenburg.freifunk.net/">Freifunk Oldenburg</a></li>
<li><a href="http://wiki.freifunk-ol.de/index.php/Netmon">Netmon Wiki</a></li>
<li><a href="https://trac.freifunk-ol.de/">Netmon Development Repository</a></li>

</ul>


<h2>Systemcheck</h2>
<div style="width: 100%; display:inline-block">
    <div style="float:left; width: 33%;">
		<h3>PHP-Extensions</h3>
		PDO: {if $pdo_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		PDO MySQL: {if $pdo_mysql_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		Json: {if $json_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		Zip: {if $zip_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		Curl: {if $curl_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		GD: {if $gd_loaded}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		OpenSSL: {if $openssl}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		FTP: {if $ftp}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}
	</div>
	<div style="float:left; width: 33%;">
		<h3>PHP-Funktionen</h3>
		exec(): {if $exec}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		mail(): {if $mail}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_unknown_small.png" alt="nicht aktiviert">{/if}<br>
		<h3>Pear Klassen</h3>
		EZ-Components: {if $ezcomponents}<img src="./templates/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
	</div>
	<div style="float:left; width: 33%;">
		<h3>Ergebnis</h3>
		{if !$pdo_loaded OR !$ftp OR !$pdo_mysql_loaded OR !$openssl OR !$json_loaded OR !$zip_loaded OR !$curl_loaded OR !$gd_loaded OR !$ezcomponents OR !$exec}
			<div class="error" style="margin: 0px;">Einige Funktionen sind inaktiv, es kann zu Problemen bei Installation und Betrieb kommen!</div>
		{else}
			<div class="notice" style="margin: 0px;">Alle Funktionen sind aktiv, Installation und Betrieb sollten ohne Probleme ablaufen.</div>
			{if !$mail}
				<div class="unknown" style="margin: 0px; margin-top: 10px;">Da kein Mailserver verfügbar ist, wird ein SMTP-Mailtransport genutzt.</div>
			 {/if}
		{/if}
	</div>
</div>

<form action="./install.php?section=db" method="POST">
  <p><input type="submit" value="Weiter"></p>
</form>

<!--<form action="./install.php.php?section=db" method="POST">
	<h2>Datenbank</h2>
	<p>Server:<br><input name="server" type="text" size="30" value="localhost"></p>
	<p>Benutzername:<br><input name="nickname" type="text" size="30"></p>
	<p>Passwort:<br><input name="password" type="password" size="30"></p>
	<p>Datenbank:<br><input name="database" type="text" size="30"></p>

  <p><input type="submit" value="Absenden"></p>
</form>-->