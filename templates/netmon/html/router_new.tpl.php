{literal}
	<!--http://plugins.jquery.com/project/zendjsonrpc-->
	
	<script type="text/javascript" src="lib/classes/extern/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/json2.js"></script>
	<script type="text/javascript" src="lib/classes/extern/zend_framework_json-rpc/jquery.zend.jsonrpc.js"></script>
	<script type="text/javascript">
		function getSubnetInfo(subnet_id) {
			$(document).ready(function(){
				test = jQuery.Zend.jsonrpc({url: 'api_main.php'});
				subnet = test.subnet_info(subnet_id);
				if(subnet.dhcp_kind!='no') {
					document.getElementById('section_dhcp').style.display = 'block';
					
					if(subnet.dhcp_kind=='ips') {
						document.getElementById('dhcp_kind_ips').style.display = 'block';
						document.getElementById('dhcp_kind_subnet').style.display = 'none';
						document.getElementById('dhcp_kind_nat').style.display = 'none';
					} else if(subnet.dhcp_kind=='subnet') {
						document.getElementById('dhcp_kind_subnet').style.display = 'block';
						document.getElementById('dhcp_kind_ips').style.display = 'none';
						document.getElementById('dhcp_kind_nat').style.display = 'none';
					} else if(subnet.dhcp_kind=='nat') {
						document.getElementById('dhcp_kind_nat').style.display = 'block';
						document.getElementById('dhcp_kind_subnet').style.display = 'none';
						document.getElementById('dhcp_kind_ips').style.display = 'none';
					}
				} else {
					document.getElementById('section_dhcp').style.display = 'none';
				}
			});
		}
	</script>
	
	<script type="text/javascript">
		var longitude;
		var latitude;
		var location;
	</script>
{/literal}

<h1>Einen neuen Router anlegen:</h1>
<form action="./routereditor.php?section=insert" method="POST">
	<h2>Grunddaten</h2>
	<p>
		Hostname: (prägnanter Name für den Router) <br><input name="hostname" type="text" size="35" maxlength="50" value="{$smarty.get.hostname}">
	</p>

	<p>
		Anmerkungen: <br><textarea name="description" cols="60" rows="6"></textarea>
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
					{/literal}" name="no_location" id="no_location" type="checkbox" value="1"> Ich möchte nicht, dass Standortdaten gespeichert werden.
		</p>

		<div id="section_location">
			<div style="width: 100%; overflow: hidden;" class="section_location">
				<script type="text/javascript" src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
				<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>
				<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
				<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
				
				<div id="map" style="height:400px; width:600px; border:solid 1px black;font-size:9pt;">
					{literal}
						<script type="text/javascript">
							new_ip_map();
						</script>
					{/literal}
				</div>
			</div>
			
			<p>
				<div style="float: right; width: 60%;">
					Länge:<br><input id="longitude" name="longitude" size="20" maxlength="15" value="Klick auf die Karte!">
				</div>
				<div style="width: 40%;">
					Breite:<br><input id="latitude" name="latitude" size="20" maxlength="15" value="Klick auf die Karte!">
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
				<option value="9999999" selected='selected'>Unbekannt</option>
			{foreach item=chipset from=$chipsets}
				<option value="{$chipset.id}">{$chipset.name}</option>
			{/foreach}
		</select>
	</p>

	<h2>Statusdaten</h2>
	<p>
		Statusaktualisierung:
		<select name="crawl_method" onChange="getProjectInfo(this.options[this.selectedIndex].value)">
			<option value="crawler" {if $smarty.get.crawl_method=='crawler'}selected='selected'{/if}>Netmon Crawlt den Router</option>
			<option value="router" {if $smarty.get.crawl_method=='router' OR empty($smarty.get.crawl_method)}selected='selected'{/if}>Der Router sendet die Daten selbstständig</option>
		</select>
	</p>

	<h2>Benachrichtigungen</h2>
	<p><input name="twitter_notification" type="checkbox" value="1" checked> Freifunker Per Twitter über den neuen Router informieren.</p>
	<p><input name="notify" type="checkbox" value="1" checked> Ich möchte benachrichtigt werden, wenn dieser Router <input name="notification_wait" type="text" size="1" maxlength="5" value="6"> Crawlzyklen offline ist.</p>

	<h2>Netmon Autozuweisung</h2>
	<p>
		<input name="allow_router_auto_assign" type="checkbox" value="1" {if $smarty.get.allow_router_auto_assign==1}checked{/if}> Erlaube automatische Router Zuweisung
	</p>

	<p>
		Mac Adresse: <br><input name="router_auto_assign_login_string" type="text" size="35" maxlength="50" value="{$smarty.get.router_auto_assign_login_string}">
	</p>

	<p><input type="submit" value="Absenden"></p>
</form>