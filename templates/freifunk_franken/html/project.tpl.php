<h1>Projekt {$project_data.title}</h1>

<h2>Eigenschaften</h2>

<ul>
  {if $project_data.is_batman_adv=='1'}<li>B.A.T.M.A.N advanced</li>{/if}
  {if $project_data.is_olsr=='1'}<li>Olsr</li>{/if}
  {if $project_data.is_wlan=='1'}<li>Wlan</li>{/if}
  {if $project_data.is_vpn=='1'}<li>VPN</li>{/if}
  {if $project_data.is_ipv4=='1'}<li>IPv4</li>{/if}
  {if $project_data.is_ipv6=='1'}<li>IPv6</li>{/if}
</ul>

<script type="text/javascript" src='https://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
<script type="text/javascript" src="./lib/extern/openlayers/OpenLayers.js"></script>
<script type="text/javascript" src="./templates/{$template}/js/OpenStreetMap.js"></script>
<script type="text/javascript" src="./templates/{$template}/js/OsmFreifunkMap.js"></script>
				
<div id="map" style="height:200px; width:400px; border:solid 1px black;font-size:9pt;">
	<script type="text/javascript">
		projectmap({$project_data.project_id});
	</script>
</div>
