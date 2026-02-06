var VC = VC || {};
VC.SearchResultsFormat = function () {
    this._geojson = new OpenLayers.Format.GeoJSON();
};
VC.SearchResultsFormat.prototype.read = function (features) {
    return this._geojson.read({
        type: "FeatureCollection",
        features: features
    });
};
