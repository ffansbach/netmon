<h1>Netmon installieren:</h1>

<form action="./install.php?section=messages_insert" method="POST">
	<h2>Netmon Website</h2>
	<p>URL zur Netmon Website:<br><input name="url_to_netmon" type="text" size="30" value="{$url_to_netmon}"></p>

	<h2>Benachrichtigungssystem<h2>
	<h3>Email</h3>
	<p>Versendeart: <select name="mail_sending_type" onChange="if(this.options[this.selectedIndex].value=='smtp') { document.getElementById('smtp_config').style.display = 'block'; } else { document.getElementById('smtp_config').style.display = 'none';}">
					<option value="php_mail" {if $mail_sending_type == 'php_mail'}selected{/if}>php_mail</option>
					<option value="smtp" {if $mail_sending_type == 'smtp'}selected{/if}>smtp</option>
			      </select>
	</p>

	<p>Absenderadresse:<br><input name="mail_sender_adress" type="text" size="30" value="{$mail_sender_adress}"></p>
	<p>Absendername:<br><input name="mail_sender_name" type="text" size="30" value="{$mail_sender_name}"></p>
	<span id="smtp_config" style="display: {if $mail_sending_type == 'smtp'}block{else}none{/if};">
		<p>SMTP-Server:<br><input name="mail_smtp_server" type="text" size="30" value="{$mail_smtp_server}"></p>
		<p>SMTP-Benutzername:<br><input name="mail_smtp_username" type="text" size="30" value="{$mail_smtp_username}"></p>
		<p>SMTP-Passwort:<br><input name="mail_smtp_password" type="password" size="30" value="{$mail_smtp_password}"></p>
		<p>SMTP-Login Methode:<br><input name="mail_smtp_login_auth" type="text" size="30" value="{$mail_smtp_login_auth}"></p>
		<p>SMTP-SSL Typ: <br><input name="mail_smtp_ssl" type="text" size="30" value="{$mail_smtp_ssl}"></p>
	</span>

	<h3>Jabber</h3>
	<p>Server:<br><input name="jabber_server" type="text" size="30" value="{$jabber_server}"></p>
	<p>Benutzername:<br><input name="jabber_username" type="text" size="30" value="{$jabber_username}"></p>
	<p>Passwort:<br><input name="jabber_password" type="password" size="30" value="{$jabber_password}"></p>
	
	<h2>Community</h2>
	<p>IPv4 Netzwerkpräfix:<br><input name="net_prefix" type="text" size="30" value="{$net_prefix}"></p>
	<p>Community Name:<br><input name="community_name" type="text" size="30" value="{$community_name}"></p>
	<p>Community Webseite:<br><input name="community_website" type="text" size="30" value="{$community_website}"></p>
	<p>Seite für Netzwerkvereinbarung einschalten: <input id="enable_network_policy" name="enable_network_policy" type="checkbox" value="true" {if $enable_network_policy}checked{/if} onChange="if(document.getElementById('enable_network_policy').checked) { document.getElementById('network_policy_config').style.display = 'block'; } else { document.getElementById('network_policy_config').style.display = 'none';}"></p>
	<span id="network_policy_config" style="display: {if $enable_network_policy == 'true'}block{else}none{/if};">
		<p>URL zur Netzwerkvereinbarung:<br><input name="networkPolicy" type="text" size="30" value="{$networkPolicy}"></p>
	</span>

	<h3>Netmon</h3>
	<p>Speicherdauer für Crawl-Daten in Tagen:<br><input name="days_to_keep_mysql_crawl_data" type="text" size="30" value="{$days_to_keep_mysql_crawl_data}"></p>

	<h3>Google Maps</h3>
	<p>Google Maps API-Key:<br><input name="google_maps_api_key" type="text" size="30" value="{$google_maps_api_key}"></p>

	<h3>Crawler</h3>
	<p>Dauer eines Crawl Cycles in Minuten:<br><input name="crawl_cycle" type="text" size="30" value="{$crawl_cycle}"></p>
	
	<p><input type="submit" value="Weiter"></p>
</form>