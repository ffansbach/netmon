<h1>IP {$net_prefix}.{$ip_data.ip} editieren</h1>

<form action="./ipeditor.php?section=insert_edit&id={$ip_data.ip_id}" method="POST">
	<h2>Reichweite</h2>
	<p>
		Radius (optional): <input name="radius" type="text" size="5" maxlength="10" value="{$ip_data.radius}"><br>
		Sinnvoll wenn Typ "ip" ist und man die ungefähre Reichweite seines W-Lan-Netzes in metern weiß.</p>
	</p>

	{if $ccd}
	<h2>VPN-CCD</h2>
	<div style="width: 100%; overflow: hidden;">
		<div style="float:left; width: 55%;">
			<h3>CCD-Eintrag (mehrzeilige Einträge möglich)</h3>
			<textarea name="ccd" cols="50" rows="5">{$ccd}</textarea>
		</div>
		
		<div style="float:left; width: 45%;">
			<h3>Mögliche Optionen</h3>
			<input type="checkbox" name="ccd_redirect_gateway" value="true" {if $ip_data.ccd_redirect_gateway==1}checked{/if}> VPN-Server als Default-Gateway nutzen
        </div>
	</div>


	{/if}
	<p><input type="submit" value="Ändern"></p>
</form>

<form action="./ipeditor.php?section=delete&id={$ip_data.ip_id}" method="POST">
	<h1>IP Löschen?</h2>
	Ja <input type="checkbox" name="delete" value="true">
	<p><input type="submit" value="Löschen!"></p>
</form>