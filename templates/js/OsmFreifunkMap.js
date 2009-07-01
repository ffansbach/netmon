var map;

// default settings
var lon = 8.214340209960938;
var lat = 53.14275482515843;
var zoom = 12;

function onPopupClose(evt) {
	selectControl.unselect(selectedFeature);
}

function onFeatureSelect(feature) {
	selectedFeature = feature;
	popup = new OpenLayers.Popup.FramedCloud("chicken", 
		feature.geometry.getBounds().getCenterLonLat(),
		new OpenLayers.Size(150,150),
		"<h2>"+feature.attributes.name + "</h2><div>" + feature.attributes.description + "</div>",
		null, true, onPopupClose);
	feature.popup = popup;
	map.addPopup(popup);
}

function onFeatureUnselect(feature) {
	map.removePopup(feature.popup);
	feature.popup.destroy();
	feature.popup = null;
}

function AddKmlLayer(name, url) {
	// Add KML layer containing the nodes
	layerNodes = new OpenLayers.Layer.GML(name, url,
		{format: OpenLayers.Format.KML, formatOptions: { extractStyles: true, extractAttributes: true } });
	map.addLayer(layerNodes);

	// Define bubbles
	selectControl = new OpenLayers.Control.SelectFeature(layerNodes, {onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});
	map.addControl(selectControl);
	selectControl.activate();
}

function SubnetLayer(name, lon, lat, radius) {
	polygonLayer = new OpenLayers.Layer.Vector(name);

	var lonlat = new OpenLayers.LonLat(lon, lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	var center = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
	geometry = new OpenLayers.Geometry.Polygon.createRegularPolygon(center, radius, 40, 0);
	circle = new OpenLayers.Feature.Vector(geometry);
	polygonLayer.addFeatures([circle]);

	map.addLayer(polygonLayer);
}

function init() {
	// Handle image load errors
	OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;
	OpenLayers.Util.onImageLoadErrorColor = "transparent";

	// Initialize the map
	map = new OpenLayers.Map ("map", {
		controls:[new OpenLayers.Control.ScaleLine(), new OpenLayers.Control.Navigation()],

		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		units: "m",

                maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34,
                                                 20037508.34, 20037508.34)

	} );

/*        map = new OpenLayers.Map('map', {
		controls:[new OpenLayers.Control.ScaleLine(), new OpenLayers.Control.Navigation()],
                projection: new OpenLayers.Projection("EPSG:900913"),
                displayProjection: new OpenLayers.Projection("EPSG:4326"),
                units: "m",
                maxResolution: 156543.0339,
                maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34,
                                                 20037508.34, 20037508.34)
	} );*/


	// Add the map layer(s)
	layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik");
//	layerOsmarender = new OpenLayers.Layer.OSM.Osmarender("Osmarender");
	layerCycleMap = new OpenLayers.Layer.OSM.CycleMap("CycleMap");
//	layerMaplint = new OpenLayers.Layer.OSM.Maplint("Maplint");
//	layerYahooSat = new OpenLayers.Layer.cdauth.Yahoo.Satellite("Yahoo Luftbilder");
/*
            var gphy = new OpenLayers.Layer.Google(
                "Google Physical",
                {type: G_PHYSICAL_MAP}
            );

            var gmap = new OpenLayers.Layer.Google(
                "Google Streets", // the default
                {sphericalMercator:true}
            );

            var ghyb = new OpenLayers.Layer.Google(
                "Google Hybrid",
                {type: G_HYBRID_MAP, numZoomLevels: 20}
            );
  */          var gsat = new OpenLayers.Layer.Google(
                "Google Satellite",
                {sphericalMercator:true, type: G_SATELLITE_MAP, numZoomLevels: 20}
            );

  	map.addLayers([layerMapnik, layerCycleMap, gsat]);


	// Set map center
	point = new OpenLayers.LonLat(lon, lat);
	point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	map.setCenter(point, zoom);


}

function MiniMapControls() {
	map.addControl(new OpenLayers.Control.LayerSwitcher());
	map.addControl(new OpenLayers.Control.Attribution({position: new OpenLayers.Pixel(170,288)}));
}

function MapControls() {
	map.addControl(new OpenLayers.Control.LayerSwitcher());
	map.addControl(new OpenLayers.Control.PanZoomBar());
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.Permalink());
	map.addControl(new OpenLayers.Control.Attribution());
}

function fullmap() {
	init();
	MapControls();
	//Adding a second layer makes google Satelite weird!
	AddKmlLayer("Verbindungen", "./api.php?class=apiMap&section=conn");
	AddKmlLayer("online Nodes", "./api.php?class=apiMap&section=getgoogleearthkmlfile_online");
	// Please do *not* uncomment the following line
	// currently there is a problem with SelectFeature for multiple layers
	// if the following line is uncommented online nodes can't be selected any longer
	//AddKmlLayer("offline Nodes", "./index.php?get=getinfo&section=getgoogleearthkmlfile_offline");
}
