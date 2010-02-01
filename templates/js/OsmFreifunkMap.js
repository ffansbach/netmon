var map;
var layerNetwork;

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
	if(name == "Netzwerklocation") {
		layerNetwork = new OpenLayers.Layer.Vector(name, {
			protocol: new OpenLayers.Protocol.HTTP({
				url: url,
				format: new OpenLayers.Format.KML({extractStyles: true})
			}),
			strategies: [new OpenLayers.Strategy.Fixed()],
			eventListeners: { 'loadend': NetworkLayerLoaded }
		});
		map.addLayer(layerNetwork);
	} else {
		var layerNodes = new OpenLayers.Layer.Vector(name, {
			protocol: new OpenLayers.Protocol.HTTP({
				url: url,
				format: new OpenLayers.Format.KML({extractStyles: true})
			}),
			strategies: [new OpenLayers.Strategy.Fixed()]
		});

		map.addLayer(layerNodes);

		// Define bubbles
		selectControl = new OpenLayers.Control.SelectFeature(layerNodes, {onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});
		map.addControl(selectControl);
		selectControl.activate();
	}
}

function NetworkLayerLoaded(){
	map.zoomToExtent(layerNetwork.getDataExtent());
}

/*function SubnetLayer(name, lon, lat, radius) {
	polygonLayer = new OpenLayers.Layer.Vector(name);

	var lonlat = new OpenLayers.LonLat(lon, lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	var center = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
	geometry = new OpenLayers.Geometry.Polygon.createRegularPolygon(center, radius, 40, 0);
	circle = new OpenLayers.Feature.Vector(geometry);
	polygonLayer.addFeatures([circle]);

	map.addLayer(polygonLayer);
}*/

function setPolygonLocation(obj){

/*            var points = obj.geometry.toString();

            document.getElementById("polygon_location").value=points;
   //Save this point

            polycontrol.deactivate();*/

            var type = 'kml';
            // second argument for pretty printing (geojson only)
			var pretty;

            var in_options = {
                'internalProjection': map.baseLayer.projection,
                'externalProjection': new OpenLayers.Projection("EPSG:4326")
            };   
            var out_options = {
                'internalProjection': map.baseLayer.projection,
                'externalProjection': new OpenLayers.Projection("EPSG:4326")
            };

            var gmlOptions = {
                featureType: "feature",
                featureNS: "http://example.com/feature"
            };
            var gmlOptionsIn = OpenLayers.Util.extend(
                OpenLayers.Util.extend({}, gmlOptions),
                in_options
            );
            var gmlOptionsOut = OpenLayers.Util.extend(
                OpenLayers.Util.extend({}, gmlOptions),
                out_options
            );
            var kmlOptionsIn = OpenLayers.Util.extend(
                {extractStyles: true}, in_options)



            var formats = {
              'in': {
                wkt: new OpenLayers.Format.WKT(in_options),
                geojson: new OpenLayers.Format.GeoJSON(in_options),
                georss: new OpenLayers.Format.GeoRSS(in_options),
                gml2: new OpenLayers.Format.GML.v2(gmlOptionsIn),
                gml3: new OpenLayers.Format.GML.v3(gmlOptionsIn),
                kml: new OpenLayers.Format.KML(kmlOptionsIn)
              }, 
              'out': {
                wkt: new OpenLayers.Format.WKT(out_options),
                geojson: new OpenLayers.Format.GeoJSON(out_options),
                georss: new OpenLayers.Format.GeoRSS(out_options),
                gml2: new OpenLayers.Format.GML.v2(gmlOptionsOut),
                gml3: new OpenLayers.Format.GML.v3(gmlOptionsOut),
                kml: new OpenLayers.Format.KML(out_options)
              } 
            };


            var str = formats['out'][type].write(obj, pretty);

            document.getElementById('polygon_location').value = str;
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


	//Adding a second layer makes google Satelite weird!
//	AddKmlLayer("Verbindungen", "./api.php?class=apiMap&section=conn");
	// Please do *not* uncomment the following line
	// currently there is a problem with SelectFeature for multiple layers
	// if the following line is uncommented online nodes can't be selected any longer
//	AddKmlLayer("offline Nodes", "./api.php?class=apiMap&section=getgoogleearthkmlfile_offline");

//	AddKmlLayer("online Nodes", "./api.php?class=apiMap&section=getgoogleearthkmlfile_online");
//	AddKmlLayer("online and offline Nodes", "./api.php?class=apiMap&section=getOnlineAndOfflineServiceKML");

	map.addControl(new OpenLayers.Control.LayerSwitcher());
	map.addControl(new OpenLayers.Control.PanZoomBar());
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.Permalink());
	map.addControl(new OpenLayers.Control.Attribution());

}

function subnetmap() {
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

	// Add the map layer(s)
	layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik");
	layerCycleMap = new OpenLayers.Layer.OSM.CycleMap("CycleMap");
	gsat = new OpenLayers.Layer.Google(
		"Google Satellite",
		{sphericalMercator:true, type: G_SATELLITE_MAP, numZoomLevels: 20}
	);

  	map.addLayers([layerMapnik, layerCycleMap, gsat]);

	// Set map center
	point = new OpenLayers.LonLat(lon, lat);
	point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	map.setCenter(point, zoom);

	map.addControl(new OpenLayers.Control.LayerSwitcher());
	map.addControl(new OpenLayers.Control.MousePosition());
}

function newsubnet_map() {

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


	// Add the map layer(s)
	layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik");
//	layerOsmarender = new OpenLayers.Layer.OSM.Osmarender("Osmarender");
	layerCycleMap = new OpenLayers.Layer.OSM.CycleMap("CycleMap");

         var gsat = new OpenLayers.Layer.Google(
                "Google Satellite",
                {sphericalMercator:true, type: G_SATELLITE_MAP, numZoomLevels: 20}
            );

/*fpshelves = new OpenLayers.Layer.Vector( "FP Shelves" );
	fpshelves.addFeatures([
	new OpenLayers.Feature.Vector(OpenLayers.Geometry.fromWKT('POLYGON((915295.1950733978 7011778.805450861,910479.6622922943 7008721.3243200015,918658.4243173432 7007383.67632525,915295.1950733978 7011778.805450861))'))
	]);
*/


	var vectors = new OpenLayers.Layer.Vector("Vector Layer");

  	map.addLayers([layerMapnik, layerCycleMap, gsat, vectors]);

	// Set map center
	point = new OpenLayers.LonLat(lon, lat);
	point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	map.setCenter(point, zoom);


                map.addControl(new OpenLayers.Control.LayerSwitcher());
                map.addControl(new OpenLayers.Control.MousePosition());
				map.addControl(new OpenLayers.Control.PanPanel());
				map.addControl(new OpenLayers.Control.ZoomPanel());



polycontrol = new OpenLayers.Control.DrawFeature(vectors,
OpenLayers.Handler.Polygon, {'featureAdded': setPolygonLocation});

map.addControl(polycontrol);

polycontrol.activate();

}

function new_ip_map() {

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


	//Adding a second layer makes google Satelite weird!
//	AddKmlLayer("Verbindungen", "./api.php?class=apiMap&section=conn");
	// Please do *not* uncomment the following line
	// currently there is a problem with SelectFeature for multiple layers
	// if the following line is uncommented online nodes can't be selected any longer
//	AddKmlLayer("offline Nodes", "./api.php?class=apiMap&section=getgoogleearthkmlfile_offline");

//	AddKmlLayer("online Nodes", "./api.php?class=apiMap&section=getgoogleearthkmlfile_online");
//	AddKmlLayer("online and offline Nodes", "./api.php?class=apiMap&section=getOnlineAndOfflineServiceKML");

	map.addControl(new OpenLayers.Control.LayerSwitcher());
	map.addControl(new OpenLayers.Control.PanZoomBar());
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.Permalink());
	map.addControl(new OpenLayers.Control.Attribution());

	map.events.register('click', map, function (e) {

	var lonlat = map.getLonLatFromPixel(e.xy).transform(
				    new
OpenLayers.Projection("EPSG:900913"), new
OpenLayers.Projection("EPSG:4326")
				);


            document.getElementById('longitude').value = lonlat.lon;
            document.getElementById('latitude').value = lonlat.lat;
	    });


}
