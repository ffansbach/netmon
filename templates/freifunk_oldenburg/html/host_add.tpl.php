<h2>Eine neue Domain anlegen</h1>

<form action="./dnseditor.php?section=insert_add_host" method="POST">
	<h2>Hostname</h2>
	<p>
		Hostname: <input name="host" type="text" size="20" maxlength="100" value="">.ffol
	</p>

	<h2>IP-Adressen</h2>
	<p>IPv4 Adresesse
		<select name="ipv4_id" size="1">
			<option value="" selected>Keine</option>
			{foreach $ipv4_ips as $ip}
				<option value="{$ip.id}">{$ip.ip}</option>
			{/foreach}
		</select>
	</p>

	<p>IPv6 Adresesse
		<select name="ipv6_id" size="1">
			<option value="" selected>Keine</option>
			{foreach $ipv6_ips as $ip}
				<option value="{$ip.id}">{$ip.ip}</option>
			{/foreach}
		</select>
	</p>
	
	<p><input type="submit" value="Absenden"></p>
</form>