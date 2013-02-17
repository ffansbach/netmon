<h1>Konfiguration der Twitter-Anbindung</h1>
{if empty($twitter_token)}
	<form action="./config.php?section=get_twitter_token" method="POST">
		<p><input type="submit" value="Netmon an Twitter anbinden"></p>
	</form>
{else}
	<form action="./config.php?section=delete_twitter_token" method="POST">
		<p><input type="submit" value="Twitteranbindung entfernen"></p>
	</form>
{/if}