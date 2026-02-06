KFA.Map.ResultLayer = Backbone.View.extend({

    initialize: function ($options) {
        var style = new OpenLayers.StyleMap({
            'default': {
                pointRadius : '5',
                fillColor : 'red',
                strokeColor: 'red',
                fillOpacity: 0.5
            },
            select: {
                pointRadius: '5',
                fillColor: 'blue',
                strokeColor: 'blue',
                fillOpacity: 0.5
            }
        });
        this._layer = new OpenLayers.Layer.Vector("Search Results", {
            strategies: [
                new OpenLayers.Strategy.Cluster()
            ],
            styleMap: style
        });
        this._format = new VC.SearchResultsFormat();
        this.listenTo(this.model, "change", this._searchResultsChanged);
    },

    _refreshLayer: function ($data) {
        var feats = this._format.read($data.features || []);
        // Transform from WGS84 (GeoJSON lon/lat) to web mercator for the map
        var epsg4326 = new OpenLayers.Projection("EPSG:4326");
        var epsg900913 = new OpenLayers.Projection("EPSG:900913");
        for (var i = 0; i < feats.length; i++) {
            if (feats[i].geometry) {
                feats[i].geometry.transform(epsg4326, epsg900913);
            }
        }
        this._layer.addFeatures(feats);

        // Auto-zoom to results if any features were added
        if (feats.length > 0) {
            var extent = this._layer.getDataExtent();
            if (extent) {
                // small padding in map units (~WebMercator meters)
                extent.left  -= 50;
                extent.right += 50;
                extent.top   += 50;
                extent.bottom-= 50;
                // Access parent map via the OpenLayers layer's map ref
                if (this._layer.map) {
                    this._layer.map.zoomToExtent(extent);
                }
            }
        }
    },

    _searchResultsChanged: function () {
        this._layer.removeAllFeatures();

        $.get("../map_search/search/search.php", this.model.toJSON(), 
            function ($context) {
                return function ($data) {
                    if (typeof $data.error !== "undefined") {
                        alert($data.error);
                        return;
                    }
                    $context._refreshLayer($data);
                };
            }(this), "json");
    }
});
