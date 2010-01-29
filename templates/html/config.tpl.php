<h1>Netmon Konfiguration</h1>
<form action="./config.php?section=insert_edit" method="POST">


	<h3>//INSTALLATION-LOCK</h3>
	
	<p>installed: <input name="installed" type="checkbox" value="true" {if $installed}checked{/if}></p>

	<h3>//MYSQL</h3>
	<p>mysql_host:<br><input name="mysql_host" type="text" size="30" value="{$mysql_host}"></p>
	<p>mysql_db:<br><input name="mysql_db" type="text" size="30" value="{$mysql_db}"></p>
	<p>mysql_user:<br><input name="mysql_user" type="text" size="30" value="{$mysql_user}"></p>
	<p>mysql_password:<br><input name="mysql_password" type="text" size="30" value="{$mysql_password}"></p>

	<h3>//JABBER</h3>
	<p>jabber_server:<br><input name="jabber_server" type="text" size="30" value="{$jabber_server}"></p>
	<p>jabber_username:<br><input name="jabber_username" type="text" size="30" value="{$jabber_username}"></p>
	<p>jabber_password:<br><input name="jabber_password" type="text" size="30" value="{$jabber_password}"></p>

	<h3>//MAIL</h3>
	<p>mail_sending_type:<br><input name="mail_sending_type" type="text" size="30" value="{$mail_sending_type}"></p>
	<p>mail_sender_adress:<br><input name="mail_sender_adress" type="text" size="30" value="{$mail_sender_adress}"></p>
	<p>mail_sender_name:<br><input name="mail_sender_name" type="text" size="30" value="{$mail_sender_name}"></p>
	<p>mail_smtp_server:<br><input name="mail_smtp_server" type="text" size="30" value="{$mail_smtp_server}"></p>
	<p>mail_smtp_username:<br><input name="mail_smtp_username" type="text" size="30" value="{$mail_smtp_username}"></p>
	<p>mail_smtp_password:<br><input name="mail_smtp_password" type="text" size="30" value="{$mail_smtp_password}"></p>
	<p>mail_smtp_login_auth:<br><input name="mail_smtp_login_auth" type="text" size="30" value="{$mail_smtp_login_auth}"></p>
	<p>mail_smtp_ssl:<br><input name="mail_smtp_ssl" type="text" size="30" value="{$mail_smtp_ssl}"></p>

	<h3>//NETWORK</h3>
	<p>net_prefix:<br><input name="net_prefix" type="text" size="30" value="{$net_prefix}"></p>
	<p>community_name:<br><input name="community_name" type="text" size="30" value="{$community_name}"></p>
	<p>community_website:<br><input name="community_website" type="text" size="30" value="{$community_website}"></p>
	<p>networkPolicy:<br><input name="networkPolicy" type="text" size="30" value="{$networkPolicy}"></p>

	<h3>//VPNKEYS</h3>
	<p>expiration:<br><input name="expiration" type="text" size="30" value="{$expiration}"></p>

	<h3>//PROJEKT</h3>
	<p>portal_history_hours:<br><input name="portal_history_hours" type="text" size="30" value="{$portal_history_hours}"></p>
	<p>days_to_keep_portal_history:<br><input name="days_to_keep_portal_history" type="text" size="30" value="{$days_to_keep_portal_history}"></p>
	<p>mysql_querry_log_time:<br><input name="mysql_querry_log_time" type="text" size="30" value="{$mysql_querry_log_time}"></p>


	<p><input type="submit" value="Absenden"></p>
</form>