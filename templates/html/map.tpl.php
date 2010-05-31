<p>
<!-- Nur ein Beispiel was vielleicht möglich währe für Ideen: http://karte.freifunk-halle.net/ -->

<h2>Freifunk Knoten auf einen Blick</h2>

	<div style="margin-right: 10px;">
		<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key={$google_maps_api_key}'></script>
		<script src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.2&mkt=en-us"></script>

		<script type="text/javascript" src="./lib/classes/extern/openlayers/OpenLayers.js"></script>

		<script type="text/javascript" src="./templates/js/OpenStreetMap.js"></script>
		<script type="text/javascript" src="./templates/js/OsmFreifunkMap.js"></script>
		
		<div id="map" style="height:600px; width:100%; border:solid 1px black;font-size:9pt;">
			<script type="text/javascript">
				fullmap();
			</script>
		</div>
	</div>
<p>