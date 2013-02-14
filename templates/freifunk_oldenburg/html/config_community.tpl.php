<h1>Konfiguration der Community Daten</h1>
<form action="./config.php?section=insert_edit_community" method="POST">
	<p>Name der Community:<br><input name="community_name" type="text" size="30" value="{$community_name}"></p>
	<p>Netzwerkpolicy einschalten: <input name="enable_network_policy" type="checkbox" value="1" {if $enable_network_policy==1}checked{/if}>
	<p>Url zur Netzwerkpolicy:<br><input name="network_policy_url" type="text" size="30" value="{$network_policy_url}"></p>
	<p><input type="submit" value="Absenden"></p>
</form>