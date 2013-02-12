<script type="text/javascript">
	document.body.id='tab1';
</script>

<ul id="tabnav">
	<li class="tab1"><a href="./config.php?section=edit">Datenbank</a></li>
	<li class="tab2"><a href="./config.php?section=edit_netmon">Netmon</a></li>
	<li class="tab3"><a href="./config.php?section=edit_community">Community</a></li>
	<li class="tab4"><a href="./config.php?section=edit_email">Email</a></li>
	<li class="tab5"><a href="./config.php?section=edit_jabber">Jabber</a></li>
	<li class="tab6"><a href="./config.php?section=edit_twitter">Twitter</a></li>
	<li class="tab7"><a href="./config.php?section=edit_hardware">Hardware</a></li>
</ul>

<h1>Netmon Konfiguration</h1>
<form action="./config.php?section=insert_edit" method="POST">
	<h2>MySQL-Datenbank</h2>
	<p>Host:<br><input name="mysql_host" type="text" size="30" value="{$mysql_host}"></p>
	<p>Datenbank:<br><input name="mysql_db" type="text" size="30" value="{$mysql_db}"></p>
	<p>Benutzer:<br><input name="mysql_user" type="text" size="30" value="{$mysql_user}"></p>
	<p>Passwort:<br><input name="mysql_password" type="text" size="30" value="{$mysql_password}"></p>

	<h3>Crawler</h3>
	<p>Ping Timeout des Crawlers:<br><input name="crawler_ping_timeout" type="text" size="30" value="{$crawler_ping_timeout}"></p>
	<p>Curl Timeout des Crawlers:<br><input name="crawler_curl_timeout" type="text" size="30" value="{$crawler_curl_timeout}"></p>

	<p><input type="submit" value="Absenden"></p>
</form>