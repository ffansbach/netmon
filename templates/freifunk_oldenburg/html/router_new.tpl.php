<h1>Einen neuen Router anlegen:</h1>
<form action="./routereditor.php?section=insert" method="POST">
	<p>
		<b>Hostname:</b> <br><input name="hostname" type="text" size="40" maxlength="50" value="{if !empty($smarty.get.hostname)}{$smarty.get.hostname}{/if}">
	</p>
	<br>

	<p>
		<b>Anmerkungen:</b><br><textarea name="description" cols="40" rows="2"></textarea>
	</p>
	<br>

	<p>
		<b>Standortsspeicherung:</b><br>
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
				{/literal}" name="no_location" id="no_location" type="checkbox" value="1"> Ich möchte nicht, dass Standortdaten gespeichert werden.
	</p>
	<br>

	<p>
		<b>Standtort</b><br>
		<div id="section_location">
			<div style="width: 100%; overflow: hidden;" class="section_location">
				<script type="text/javascript" src='https://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
				<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
				<script type="text/javascript" src="./templates/{$template}/js/OpenStreetMap.js"></script>
				<script type="text/javascript" src="./templates/{$template}/js/OsmFreifunkMap.js"></script>
				
				<div id="map" style="height:300px; width:400px; border:solid 1px black;font-size:9pt;">
					<script type="text/javascript">
						new_router_map('{$community_location_longitude}', '{$community_location_latitude}', '{$community_location_zoom}', '{$template}');
					</script>
				</div>
			</div>
		</div>
		<input type="hidden" id="longitude" name="longitude" size="20" maxlength="15" value="">
		<input type="hidden" id="latitude" name="latitude" size="20" maxlength="15" value="">
	</p>
	<br>

	<p>
		<b>Kurze Beschreibung des Standorts:</b><br>
		<textarea name="location" cols="40" rows="2"></textarea>
	</p>
	<br>

	<p>
		<b>Chipset:</b><br>
		Wählen Sie wenn bekannt das Chipset ihres Routers aus 
		<select name="chipset_id">
			<option value="9999999" selected='selected'>Unbekannt</option>
			{foreach item=chipset from=$chipsets}
				<option value="{$chipset.id}">{$chipset.name}</option>
			{/foreach}
		</select>
	</p>
	<br>

	<p>
		<b>Statusaktualisierung:</b><br>
		Wählen Sie aus wie der Status ihres Routers aktualisiert wird 
		<select name="crawl_method" onChange="getProjectInfo(this.options[this.selectedIndex].value)">
			<option value="crawler" {if !isset($smarty.get.crawl_method) OR $smarty.get.crawl_method=='crawler'}selected='selected'{/if}>Netmon Crawlt den Router</option>
			<option value="router" {if isset($smarty.get.crawl_method) AND $smarty.get.crawl_method=='router'}selected='selected'{/if}>Der Router sendet die Daten selbstständig</option>
		</select>
	</p>
	<br>

<!--	<p>
		<b>Offlinebenachrichtigung:</b><br>
		<input name="notify" type="checkbox" value="1" checked> Ich möchte benachrichtigt werden, wenn dieser Router <input name="notification_wait" type="text" size="1" maxlength="5" value="6"> Crawlzyklen offline ist.
	</p>
	<br>-->

	<p>
		<b>Automatische Routerzuweisung:</b><br>
		<input name="allow_router_auto_assign" type="checkbox" value="1" {if !isset($smarty.get.allow_router_auto_assign) OR $smarty.get.allow_router_auto_assign==1}checked{/if}> Erlauben <input name="router_auto_assign_login_string" type="text" size="20" maxlength="50" value="{if isset($smarty.get.router_auto_assign_login_string)}{$smarty.get.router_auto_assign_login_string}{else}Mac-Adresse...{/if}"
 {if !isset($smarty.get.router_auto_assign_login_string) OR $smarty.get.router_auto_assign_login_string == 'Mac-Adresse...'}onfocus="if(this.value == this.defaultValue) this.value = '';" onblur="if(!this.value) this.value = this.defaultValue;"{/if}>
	</p>
	<br>

	{if !empty($twitter_token)}
	<p>
		<b>Twitterankündigung:</b><br>
		<input name="twitter_notification" type="checkbox" value="1" checked> Freifunker Per Twitter über den neuen Router informieren.
	</p>
	<br>
	{/if}
	
	<p><input type="submit" value="Absenden"></p>
</form>