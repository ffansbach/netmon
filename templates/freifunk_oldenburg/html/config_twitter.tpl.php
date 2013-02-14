<h1>Konfiguration der Twitter-Anbindung</h1>
<h2>Anmelden</h2>
<p><a href="https://twitter.com/apps/new">Netmon installation bei Twitter anmelden</a><p>

<h2>Twitter Anwendungsdaten</h2>
<form action="./config.php?section=insert_edit_twitter_application_data" method="POST">
	<p>Consumer key:<br><input name="twitter_consumer_key" type="text" size="30" value="{$twitter_consumer_key}"></p>
	<p>Consumer secret:<br><input name="twitter_consumer_secret" type="text" size="30" value="{$twitter_consumer_secret}"></p>
	<p><input type="submit" value="Absenden"></p>
</form>

<h2>Twitter Account</h2>
<form action="./config.php?section=insert_edit_twitter_username" method="POST">
	<p>Twitter Nickname:<br><input name="twitter_username" type="text" size="30" value="{$twitter_username}"></p>
	<p><input type="submit" value="Absenden"></p>
</form>

<h2>Twitter Token holen</h2>
<form action="./config.php?section=get_twitter_token" method="POST">
	<p><input type="submit" value="Token holen"></p>
</form>