<h1>Netmon installieren</h1>
<p>Netmon ist ein Monitoring-Portal für Freifunknetzwerke. Du benutzt Version <i>{$netmon_version} ({$netmon_codename})</i>. Dieser Assistent wird dich durch die Installation führen und die wichtigsten Einstellungen vornehmen. Alle weiteren Einstellungen und Anpassungen an deine lokale Community kannst du später als eingeloggter Benutzer im Menü unter <i>Konfiguration</i> vornehmen.<br><br>Viel Spaß!</p>

<h2>Informationen im Web</h2>
<ul>
	<li><a href="http://oldenburg.freifunk.net/">Freifunk Oldenburg</a></li>
	<li><a href="http://wiki.freifunk-ol.de/index.php?title=Netmon">Netmon Wiki</a></li>
	<li><a href="http://ticket.freifunk-ol.de/projects/netmon">Netmon Entwicklerportal</a></li>
</ul>

<h2>Systemcheck</h2>
<p>Der Systemcheck überprüft ob alle wichtigen Funktionen deines Servers zum Betrieb von Netmon eingeschaltet sind.</p>
<div style="width: 100%; display:inline-block">
    <div style="float:left; width: 33%;">
		<h3>PHP-Extensions</h3>
		PDO: {if $pdo_loaded}<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		PDO MySQL: {if $pdo_mysql_loaded}<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		Json: {if $json_loaded}<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		Curl: {if $curl_loaded}<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		GD: {if $gd_loaded}<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
	</div>
	<div style="float:left; width: 33%;">
		<h3>PHP-Version</h3>php_version
		PHP Version 5.2+: {if $php_version}<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}
		<h3>PHP-Funktionen</h3> 
		exec(): {if $exec}<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/{$template}/img/ffmap/status_down_small.png" alt="nicht aktiviert">{/if}<br>
		mail(): {if $mail}<img src="./templates/{$template}/img/ffmap/status_up_small.png" alt="aktiviert">{else}<img src="./templates/{$template}/img/ffmap/status_unknown_small.png" alt="nicht aktiviert">{/if}<br>
	</div>
	<div style="float:left; width: 33%;">
		<h3>Ergebnis</h3>
		{if !$pdo_loaded OR !$pdo_mysql_loaded OR !$json_loaded OR !$curl_loaded OR !$gd_loaded OR !$exec OR !$php_version}
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