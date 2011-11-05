<script type="text/javascript">
	var longitude;
	var latitude;
	var location;
</script>

<h1>Router {$router_data.hostname} editieren:</h1>

<h2>Router löschen</h2>
<form action="./routereditor.php?section=insert_delete&router_id={$router_data.router_id}" method="POST">
	<input name="really_delete" type="checkbox" value="1"> Router {$router_data.hostname} wirklich löschen?
	<p><input type="submit" value="Löschen"></p>
</form>

{if !empty($router_data.router_auto_assign_hash)}
<h2>Router auto assign Hash zurücksetzen</h2>
<form action="./routereditor.php?section=insert_reset_auto_assign_hash&router_id={$router_data.router_id}" method="POST">
	<p>Aktueller Hash: {$router_data.router_auto_assign_hash}</p>
	<p><input type="submit" value="Zurücksetzen"></p>
</form>
{/if}

<h2>Grunddaten ändern</h2>
<form action="./routereditor.php?section=insert_edit&router_id={$router_data.router_id}" method="POST">
	<p>
		Hostname: <br><input name="hostname" type="text" size="35" maxlength="50" value="{$router_data.hostname}">
	</p>

	<p>
		Anmerkungen: <br><textarea name="description" cols="40" rows="3">{$router_data.description}</textarea>
	</p>

	<div>
		<h2>Standort</h2>
		<p>
			<input onChange="
					{literal}
						if(document.getElementById('no_location').checked) {
							document.getElementById('section_location').style.display = 'none';
							this.longitude = document.getElementById('longitude').value;
							this.latitude = document.getElementById('latitude').value;
							this.location = document.getElementById('location').value;
							
							document.getElementById('longitude').value = '';
							document.getElementById('latitude').value = '';
							document.getElementById('location').value = '';
						} else {
							document.getElementById('section_location').style.display = 'block';
							document.getElementById('longitude').value = this.longitude;
							document.getElementById('latitude').value = this.latitude;
							document.getElementById('location').value = this.location;
						}
					{/literal}" name="no_location" id="no_location" type="checkbox" value="1" {if empty($router_data.longitude) AND empty($router_data.latitude) AND empty($router_data.location)}checked{/if}> Ich möchte nicht, dass Standortdaten gespeichert werden.
		</p>

		<div id="section_location">
			<div style="width: 100%; overflow: hidden;" class="section_location">
				<script type="text/javascript" src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
				<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
				<script type="text/javascript" src="./templates/{$template}/js/OpenStreetMap.js"></script>
				<script type="text/javascript" src="./templates/{$template}/js/OsmFreifunkMap.js"></script>
				
				<div id="map" style="height:400px; width:600px; border:solid 1px black;font-size:9pt;">
					{literal}
						<script type="text/javascript">
							new_router_map();
						</script>
					{/literal}
				</div>
			</div>
				
			<p>
				<div style="float: right; width: 60%;">
					Länge:<br><input id="longitude" name="longitude" size="20" maxlength="15" value="{$router_data.longitude}">
				</div>
				<div style="width: 40%;">
					Breite:<br><input id="latitude" name="latitude" size="20" maxlength="15" value="{$router_data.latitude}">
				</div>
				<br style="clear:both;">
			</p>
			
			<p>Kurze Beschreibung des Standorts:<br><input id="location" name="location" type="text" size="60" maxlength="60" value="{$router_data.location}"></p>
		</div>
	</div>

	<h2>Hardware</h2>
	<p>
		Chipset:
		<select name="chipset_id">
				<option value="9999999" {if $chipset.id==$router_data.chipset_id}selected='selected'{/if}>Unbekannt</option>
			{foreach item=chipset from=$chipsets}
				<option value="{$chipset.id}" {if $chipset.id==$router_data.chipset_id}selected='selected'{/if}>{$chipset.name}</option>
			{/foreach}
		</select>
	</p>

	<h2>Statusdaten</h2>
	<p>
		Statusaktualisierung:
		<select name="crawl_method" onChange="getProjectInfo(this.options[this.selectedIndex].value)">
			<option value="crawler" {if $router_data.crawl_method=='crawler'}selected='selected'{/if}>Netmon Crawlt den Router</option>
			<option value="router" {if $router_data.crawl_method=='router'}selected='selected'{/if}>Der Router sendet die Daten selbstständig</option>
		</select>
	</p>

	<h2>Offline Benachrichtigung</h2>
	<p><input name="notify" type="checkbox" value="1" {if $router_data.notify==1}checked{/if}> Ich möchte benachrichtigt werden, wenn dieser Router <input name="notification_wait" type="text" size="1" maxlength="5" value="{$router_data.notification_wait}"> Crawlzyklen offline ist.</p>

	<h2>Netmon Autozuweisung</h2>
	<p>
		<input name="allow_router_auto_assign" type="checkbox" value="1" {if $router_data.allow_router_auto_assign==1}checked{/if}> Erlaube automatische Router Zuweisung
	</p>

	<p>
		Mac Adresse (ohne Bindestriche oder Doppelpunkte): <br><input name="router_auto_assign_login_string" type="text" size="35" maxlength="50" value="{$router_data.router_auto_assign_login_string}">
	</p>

	<p><input type="submit" value="Absenden"></p>
</form>