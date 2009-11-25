<h1>IP {$net_prefix}.{$ip_data.ip} editieren</h1>

<form action="./ipeditor.php?section=insert_edit&id={$ip_data.ip_id}" method="POST">
	<h2>Reichweite</h2>
	<p>
		Radius (optional): <input name="radius" type="text" size="5" maxlength="10" value="{$ip_data.radius}"><br>
		Sinnvoll wenn Typ "ip" ist und man die ungefähre Reichweite seines W-Lan-Netzes in metern weiß.</p>
	</p>

	{if $ccd}
	<h2>VPN-CCD</h2>
		CCD-Eintrag: <input name="ccd" type="text" size="50" value="{$ccd}"><br>
	{/if}
	<p><input type="submit" value="Ändern"></p>
</form>

<form action="./ipeditor.php?section=delete&id={$ip_data.ip_id}" method="POST">
	<h1>IP Löschen?</h2>
	Ja <input type="checkbox" name="delete" value="true">
	<p><input type="submit" value="Löschen!"></p>
</form>