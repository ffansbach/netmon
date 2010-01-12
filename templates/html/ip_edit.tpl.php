<h1>IP {$net_prefix}.{$ip_data.ip} editieren</h1>

<form action="./ipeditor.php?section=insert_edit&id={$ip_data.ip_id}" method="POST">
	<div>
	<h2>Standort</h2>
	<div style="width: 100%; overflow: hidden;">
		<div style="float:left; width: 55%;">
			<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAACRLdP-ifG9hOW_8o3tqVjBRQgDd6cF0oYEN79IHJn82DjAEhYRR0LiPE-L7piWHBnxtDHfBWT2fTBQ'></script>
			<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
			<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
			<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
			
			<div id="map" style="height:200px; width:400px; border:solid 1px black;font-size:9pt;">
				{literal}<script type="text/javascript">
					new_ip_map();
				</script>{/literal}
			</div>
		</div>
		
		<div style="float:left; width: 45%;">
		<p>Länge: <input id="longitude" name="longitude" size="15" maxlength="15" value="{$ip_data.longitude}"><br>
		Breite: <input id="latitude" size="15" maxlength="15" name="latitude" value="{$ip_data.latitude}"></p>
	</div>
	</div>
	
	<h2>Beschreibung des Standorts</h2>
	<p>
   		Kurze Beschreibung des Standorts:<br><input name="location" type="text" size="60" maxlength="60" value="{$ip_data.location}">
	</p>

	<h2>Routertyp</h2>
	Chipset: <select name="chipset">
		{foreach item=imggen_supported_chipset from=$imggen_supported_chipsets}
			<option value="{$imggen_supported_chipset}" {if $imggen_supported_chipset==$ip_data.chipset}selected{/if}>{$imggen_supported_chipset}</option>
		{/foreach}
		</select>

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
			<ul>
				<li>
					push redirect-gateway
				</li>
			</ul>
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