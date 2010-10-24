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
		<div style="width: 100%; overflow: hidden;">
			<div style="float:left; width: 55%;">
				<script type="text/javascript" src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
				<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
				<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
				<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
				
				<div id="map" style="height:200px; width:400px; border:solid 1px black;font-size:9pt;">
					{literal}
						<script type="text/javascript">
							new_ip_map();
						</script>
					{/literal}
				</div>
			</div>
		
			<div style="float:left; width: 45%;">
				<p>
					Länge: <input id="longitude" name="longitude" size="15" maxlength="15" value="{$router_data.longitude}"><br>
					Breite: <input id="latitude" name="latitude" size="15" maxlength="15" value="{$router_data.latitude}">
				</p>
			</div>
		</div>
	</div>
	
	<p>Kurze Beschreibung des Standorts:<br><input name="location" type="text" size="60" maxlength="60" value="{$router_data.location}"></p>

	<h2>Hardware</h2>
	<p>
		Chipset:
		<select name="chipset_id">
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
	<p>
		Möchtest du benachrichtigt werden, wenn dieser Router <input name="notification_wait" type="text" size="1" maxlength="5" value="{$router_data.notification_wait}"> Crawlzyklen offline ist?
		<select name="notify">
			<option value="1" {if $router_data.notify==1}selected='selected'{/if}>Ja</option>
			<option value="0" {if $router_data.notify==0}selected='selected'{/if}>Nein</option>
		</select>
	</p>

	<h2>Netmon Autozuweisung</h2>
	<p>
		<input name="allow_router_auto_assign" type="checkbox" value="1" {if $router_data.allow_router_auto_assign==1}checked{/if}> Erlaube automatische Router Zuweisung
	</p>

	<p>
		Autozuweisungs Login: <br><input name="router_auto_assign_login_string" type="text" size="35" maxlength="50" value="{$router_data.router_auto_assign_login_string}">
	</p>

	<p><input type="submit" value="Absenden"></p>
</form>