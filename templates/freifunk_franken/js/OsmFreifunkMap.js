var map;
var layerNetwork;

// default settings
var lon = 10.65;
var lat = 49.71;
var zoom = 9;

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
	load_kml = new OpenLayers.Protocol.HTTP({
				url: url,
				format: new OpenLayers.Format.KML({extractStyles: true})
			});

	if(name == "Netzwerklocation") {
		layerNetwork = new OpenLayers.Layer.Vector(name, {
			protocol: load_kml,
			strategies: [new OpenLayers.Strategy.Fixed()],
			eventListeners: { 'loadend': NetworkLayerLoaded }
		});

		map.addLayer(layerNetwork);
	} else {
		var layerNodes = new OpenLayers.Layer.Vector(name, {
			protocol: load_kml,
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

function loadKmlLayer(name, url) {
	load_kml = new OpenLayers.Protocol.HTTP({
				url: url,
				format: new OpenLayers.Format.KML({extractStyles: true})
			});

	if(name == "Netzwerklocation") {
		layerNetwork = new OpenLayers.Layer.Vector(name, {
			protocol: load_kml,
			strategies: [new OpenLayers.Strategy.Fixed()],
			eventListeners: { 'loadend': NetworkLayerLoaded }
		});

		return layerNetwork;
	} else {
		var layerNodes = new OpenLayers.Layer.Vector(name, {
			protocol: load_kml,
			strategies: [new OpenLayers.Strategy.Fixed()]
		});

		return layerNodes;
	}
}

function fullmap() {
	// Handle image load errors
	OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;
	OpenLayers.Util.onImageLoadErrorColor = "transparent";

	// Initialize the map
	map = new OpenLayers.Map ("map", {
		controls:[new OpenLayers.Control.ScaleLine(), 
				  new OpenLayers.Control.TouchNavigation({
                dragPanOptions: {
                    enableKinetic: true
                }
            }),
	    new OpenLayers.Control.Navigation()],

		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		units: "m",

                maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34,
                                                 20037508.34, 20037508.34)
	} );

	// Add the map layer(s)
	var layerMapnik = new OpenLayers.Layer.OSM.Mapnik(
                "Open Street Map",
                {sphericalMercator:true, numZoomLevels: 20}
        );

        var gstreet = new OpenLayers.Layer.Google(
                "Google Streets",
                {sphericalMercator:true, numZoomLevels: 20}
        );

        var gphy = new OpenLayers.Layer.Google(
                "Google Physical",
                {sphericalMercator:true, type: G_PHYSICAL_MAP, numZoomLevels: 20}
        );

        var gsat = new OpenLayers.Layer.Google(
                "Google Satellite",
                {sphericalMercator:true, type: G_SATELLITE_MAP, numZoomLevels: 20}
            );

	var ghybrid = new OpenLayers.Layer.Google(
		"Google Hybrid",
		{sphericalMercator:true, type: G_HYBRID_MAP, numZoomLevels: 20}
	);

/*        var bstreet = new OpenLayers.Layer.VirtualEarth(
		"Bing Streets",
		{sphericalMercator:true, type: VEMapStyle.Shaded, numZoomLevels: 20}
	);

        var bhybrid = new OpenLayers.Layer.VirtualEarth(
		"Bing Hybrid",
		{sphericalMercator:true, type: VEMapStyle.Hybrid, numZoomLevels: 20}
	);

        var bsatelite = new OpenLayers.Layer.VirtualEarth(
		"Bing Satelite",
		{sphericalMercator:true, type: VEMapStyle.Aerial, numZoomLevels: 20}
	);*/

//  	map.addLayers([layerMapnik, gstreet, gphy, gsat, ghybrid, bstreet, bhybrid, bsatelite]);
  	map.addLayers([layerMapnik, gstreet, gphy, gsat, ghybrid]);

	// Set map center
	point = new OpenLayers.LonLat(lon, lat);
	point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	map.setCenter(point, zoom);

	map.addControl(new OpenLayers.Control.LayerSwitcher());
	map.addControl(new OpenLayers.Control.PanZoomBar());
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.Permalink());
	map.addControl(new OpenLayers.Control.Attribution());

	//Make Layers
//	var layer_conn = loadKmlLayer('Olsr Verbindungen', './api.php?class=apiMap&section=olsr_conn');
//	var layer_nodes_offline = loadKmlLayer('Offline Knoten', './api.php?class=apiMap&section=getOfflineServiceKML');
	var layer_nodes = loadKmlLayer('Knoten ', './api.php?class=apiMap&section=getRouters');
	var layer_traffic = loadKmlLayer('Traffic ', './api.php?class=apiMap&section=getRoutersTraffic');
	
	var layer_clients = loadKmlLayer('Clients ', './api.php?class=apiMap&section=getRoutersClients');
	//	var layer_nodes_offline = loadKmlLayer('Offline Knoten ', './api.php?class=apiMap&section=getOfflineRouters');
//	var batman_adv_conn = loadKmlLayer('Bat. Adv. Originators', './api.php?class=apiMap&section=batman_advanced_conn');
	var batman_adv_conn_nexthop = loadKmlLayer('Bat. Adv. Nexthop', './api.php?class=apiMap&section=batman_advanced_conn_nexthop');
	var olsr_conn = loadKmlLayer('Olsr Verbindungen', './api.php?class=apiMap&section=olsr_conn');

	
	
	/*
    var: new OpenLayers.Layer.OSM("Cycle &amp; Hiking", "tiles/overlay_ch/", {numZoomLevels: 19, isBaseLayer: false, visibility: false }),
    pt: new OpenLayers.Layer.OSM("Public Transport", "tiles/overlay_pt/", {numZoomLevels: 19, isBaseLayer: false, visibility: false }),
    car: new OpenLayers.Layer.OSM("Individual Transport", "tiles/overlay_car/", {numZoomLevels: 19, isBaseLayer: false, visibility: false }),
*/
//    var food = new OpenLayers.Layer.OSM("Food &amp; Drink", "tiles/overlay_food/", {numZoomLevels: 19, isBaseLayer: false, visibility: false });

    /*culture: new OpenLayers.Layer.OSM("Culture &amp; Tourism", "tiles/overlay_culture/", {numZoomLevels: 19, isBaseLayer: false, visibility: false }),
    services: new OpenLayers.Layer.OSM("Services", "tiles/overlay_services/", {numZoomLevels: 19, isBaseLayer: false, visibility: false }),
    shop: new OpenLayers.Layer.OSM("Shops", "tiles/shop/", {numZoomLevels: 19, isBaseLayer: false, visibility: false }),
    agri_ind: new OpenLayers.Layer.OSM("Agriculture &amp; Industry", "tiles/agri_ind/", {numZoomLevels: 19, isBaseLayer: false, visibility: false })
	*/
	
	//Add Layers
        map.addLayers([layer_clients, layer_traffic, layer_nodes, batman_adv_conn_nexthop, olsr_conn]);

	
	// Define bubbles
	selectControl = new OpenLayers.Control.SelectFeature([layer_nodes], {onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});
	map.addControl(selectControl);
	selectControl.activate();
}


function router_map(highlight_router_id) {
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
	var layerMapnik = new OpenLayers.Layer.OSM.Mapnik(
                "Open Street Map",
                {sphericalMercator:true, numZoomLevels: 20}
        );

        var gstreet = new OpenLayers.Layer.Google(
                "Google Streets",
                {sphericalMercator:true, numZoomLevels: 20}
        );

        var gsat = new OpenLayers.Layer.Google(
                "Google Satellite",
                {sphericalMercator:true, type: G_SATELLITE_MAP, numZoomLevels: 20}
            );

	var ghybrid = new OpenLayers.Layer.Google(
		"Google Hybrid",
		{sphericalMercator:true, type: G_HYBRID_MAP, numZoomLevels: 20}
	);

  	map.addLayers([layerMapnik, gstreet, gsat, ghybrid]);

	// Set map center
	point = new OpenLayers.LonLat(lon, lat);
	point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	map.setCenter(point, zoom);

	map.addControl(new OpenLayers.Control.LayerSwitcher());

	map.addControl(new OpenLayers.Control.PanPanel());
	map.addControl(new OpenLayers.Control.ZoomPanel());

	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.Permalink());
	map.addControl(new OpenLayers.Control.Attribution());

/*	//Make Layers
	var layer_conn = loadKmlLayer('Olsr Verbindungen', './api.php?class=apiMap&section=olsr_conn');
	var layer_nodes_offline = loadKmlLayer('Offline Knoten', './api.php?class=apiMap&section=getOfflineServiceKML&highlighted_service='+highlighted_service);
	var layer_nodes_online = loadKmlLayer('Online Knoten ', './api.php?class=apiMap&section=getOnlineServiceKML&highlighted_service='+highlighted_service);

	//Add Layers
        map.addLayers([layer_conn, layer_nodes_offline, layer_nodes_online]);*/

	var layer_nodes = loadKmlLayer('Knoten ', './api.php?class=apiMap&section=getRouters&highlight_router_id='+highlight_router_id);
//	var batman_adv_conn = loadKmlLayer('Bat. Adv. Verbindungen', './api.php?class=apiMap&section=batman_advanced_conn');
	var batman_adv_conn_nexthop = loadKmlLayer('Bat. Adv. Nexthop', './api.php?class=apiMap&section=batman_advanced_conn_nexthop');
	var olsr_conn = loadKmlLayer('Olsr Verbindungen', './api.php?class=apiMap&section=olsr_conn');
	
	//Add Layers
        map.addLayers([layer_nodes, batman_adv_conn_nexthop, olsr_conn]);
	
	// Define bubbles
	selectControl = new OpenLayers.Control.SelectFeature([layer_nodes], {onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});
	map.addControl(selectControl);
	selectControl.activate();
}

function new_interface_projectmap(project_id) {
	// Handle image load errors
	OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;
	OpenLayers.Util.onImageLoadErrorColor = "transparent";

	// Initialize the map
	map = new OpenLayers.Map ("geo_polygon_map", {
		controls:[new OpenLayers.Control.ScaleLine(), new OpenLayers.Control.Navigation()],

		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		units: "m",

                maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34,
                                                 20037508.34, 20037508.34)
	} );

	// Add the map layer(s)
	var layerMapnik = new OpenLayers.Layer.OSM.Mapnik(
                "Open Street Map",
                {sphericalMercator:true, numZoomLevels: 20}
        );

        var gstreet = new OpenLayers.Layer.Google(
                "Google Streets",
                {sphericalMercator:true, numZoomLevels: 20}
        );

        var gsat = new OpenLayers.Layer.Google(
                "Google Satellite",
                {sphericalMercator:true, type: G_SATELLITE_MAP, numZoomLevels: 20}
            );

	var ghybrid = new OpenLayers.Layer.Google(
		"Google Hybrid",
		{sphericalMercator:true, type: G_HYBRID_MAP, numZoomLevels: 20}
	);

  	map.addLayers([layerMapnik, gstreet, gsat, ghybrid]);

	// Set map center
	point = new OpenLayers.LonLat(lon, lat);
	point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	map.setCenter(point, zoom);

	map.addControl(new OpenLayers.Control.LayerSwitcher());

	map.addControl(new OpenLayers.Control.PanPanel());
	map.addControl(new OpenLayers.Control.ZoomPanel());

	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.Permalink());
	map.addControl(new OpenLayers.Control.Attribution());

	//Make Layers
	var layer_project_geo = loadKmlLayer('Netzwerklocation', './api.php?class=apiMap&section=getProjectGeoPolygons&project_id='+project_id);
//	var layer_conn = loadKmlLayer('Olsr Verbindungen', './api.php?class=apiMap&section=olsr_conn');
//	var layer_nodes_offline = loadKmlLayer('Offline Knoten', './api.php?class=apiMap&section=getOfflineServiceKML&highlighted_subnet='+subnet_id);
//	var layer_nodes = loadKmlLayer('Online Knoten ', './api.php?class=apiMap&section=getOnlineServiceKML&highlighted_subnet='+subnet_id);

	//Add Layers
    map.addLayers([layer_project_geo]);

	// Define bubbles
/*	selectControl = new OpenLayers.Control.SelectFeature([layer_conn, layer_nodes_offline, layer_nodes], {onSelect: onFeatureSelect, onUnselect: onFeatureUnselect});
	map.addControl(selectControl);
	selectControl.activate();*/
}

/*
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
	layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Open Street Map");
//	layerOsmarender = new OpenLayers.Layer.OSM.Osmarender("Osmarender");

         var gsat = new OpenLayers.Layer.Google(
                "Google Satellite",
                {sphericalMercator:true, type: G_SATELLITE_MAP, numZoomLevels: 20}
            );

/*fpshelves = new OpenLayers.Layer.Vector( "FP Shelves" );
	fpshelves.addFeatures([
	new OpenLayers.Feature.Vector(OpenLayers.Geometry.fromWKT('POLYGON((915295.1950733978 7011778.805450861,910479.6622922943 7008721.3243200015,918658.4243173432 7007383.67632525,915295.1950733978 7011778.805450861))'))
	]);
*/

/*
	var vectors = new OpenLayers.Layer.Vector("Vector Layer");

  	map.addLayers([layerMapnik, gsat, vectors]);

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

}*/

function newproject_map() {
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
	layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Open Street Map");
//	layerOsmarender = new OpenLayers.Layer.OSM.Osmarender("Osmarender");

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

  	map.addLayers([layerMapnik, gsat, vectors]);

	// Set map center
	point = new OpenLayers.LonLat(lon, lat);
	point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	map.setCenter(point, zoom);
	
	map.addControl(new OpenLayers.Control.LayerSwitcher());
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.PanPanel());
	map.addControl(new OpenLayers.Control.ZoomPanel());
	
	polycontrol = new OpenLayers.Control.DrawFeature(vectors,
			OpenLayers.Handler.Polygon, {'featureAdded': setPolygonLocation}
	);
	
	map.addControl(polycontrol);
	polycontrol.activate();
}

function new_router_map() {
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
	layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Open Street Map");
          var gsat = new OpenLayers.Layer.Google(
                "Google Satellite",
                {sphericalMercator:true, type: G_SATELLITE_MAP, numZoomLevels: 20}
            );

  	map.addLayers([layerMapnik, gsat]);

	// Set map center
	point = new OpenLayers.LonLat(lon, lat);
	point.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	map.setCenter(point, zoom);


	map.addControl(new OpenLayers.Control.LayerSwitcher());
	map.addControl(new OpenLayers.Control.PanZoomBar());
	map.addControl(new OpenLayers.Control.MousePosition());
	map.addControl(new OpenLayers.Control.Permalink());
	map.addControl(new OpenLayers.Control.Attribution());

	map.events.register('click', map, function (e) {
		var lonlat = map.getLonLatFromPixel(e.xy).transform(
			new OpenLayers.Projection("EPSG:900913"),
			new OpenLayers.Projection("EPSG:4326")
		);
	
		document.getElementById('longitude').value = lonlat.lon;
		document.getElementById('latitude').value = lonlat.lat;
	});
}
