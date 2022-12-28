<!DOCTYPE html>
<html>
<link rel="stylesheet" href="leaflet/leaflet.css" />
<link rel="stylesheet" href="css/leaflet.groupedlayercontrol.css" />
<link rel="stylesheet" href="css/style.css" />
<script src="leaflet/leaflet.js"></script>
<script src="js/jquery-3.6.3.min.js"></script>
<script src="js/leaflet.ajax.js"></script>
<script src="js/leaflet-providers.js"></script>
<script src="js/leaflet.groupedlayercontrol.js"></script>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebGIS Administrasi Kota Tangerang</title>
</head>

<body>
    <div id="map">
        <script>
            // MENGATUR TITIK KOORDINAT TITIK TENGAN & LEVEL ZOOM PADA BASEMAP
            var map = L.map('map').setView([-6.1770027, 106.6318335], 12);

            // MENAMPILKAN SKALA
            L.control.scale({
                imperial: false
            }).addTo(map);

            function highlightFeature(e) {
                var layer = e.target;

                layer.setStyle({
                    weight: 3,
                    color: '#666',
                    dashArray: '',
                    fillOpacity: 0.3
                });
                if (!L.Browser.ie && !L.Browser.opera) {
                    layer.bringToFront();
                }
                info.update(layer.feature.properties);
            }

            function resetHighlight(e) {
                layerAdministrasiAr.resetStyle(e.target);
                info.update();
            }

            function zoomToFeature(e) {
                map.fitBounds(e.target.getBounds());
            }

            // LAYER ADMINISTRASI POLYGON
            var layerAdministrasiAr = new L.GeoJSON.AJAX("data/administrasi_ar.geojson", {
                style: function(feature) {
                    var fillColor = feature.properties.color;
                    return {
                        color: "#999",
                        dashArray: '3',
                        weight: 2,
                        fillColor: fillColor,
                        fillOpacity: 1
                    };
                },
                onEachFeature: function(feature, layer) {
                    layer.on('mouseover', highlightFeature);
                    layer.on('mouseout', resetHighlight);
                    layer.on('click', zoomToFeature);
                }
            });
            layerAdministrasiAr.addTo(map);

            // LAYER ADMINISTRASI LINE
            var layerAdministrasiLn = new L.GeoJSON.AJAX("data/administrasi_ln.geojson", {
                style: function(feature) {
                    return {
                        color: "#dc0000",
                        dashArray: '3',
                        weight: 2,
                        fillOpacity: 1
                    };
                }
            });

            // LAYER KECAMATAN POINT
            var layerKecamatanPt = new L.GeoJSON.AJAX("data/kecamatan_pt.geojson", {
                onEachFeature: function(feature, layer) {
                    layer.bindPopup("<center>" + feature.properties.name + "</center>"), that = this;
                }
            });

            var info = L.control();

            info.onAdd = function(map) {
                this._div = L.DomUtil.create('div', 'info'); // BUAT DIV DENGAN CLASS 'info'
                this.update();
                return this._div;
            };

            // METODE YANG DIGUNAKAN UNTUK MEPERBARUI DETAIL YANG AKAN DITAMPILKAN
            info.update = function(feature) {
                this._div.innerHTML = '<h4>Administrasi Kota Tangerang</h4>' + (feature ?
                    'Kelurahan&emsp;: &nbsp;' + feature.name + '<br />Kecamatan&ensp;: &nbsp;' + feature.wadmkc :
                    'Arahkan kursor ke bagian polygon');
            };

            info.addTo(map);

            // PILIHAN BASEMAP YANG AKAN DITAMPILKAN
            var baseLayers = {
                'OpenStreetMap': L.tileLayer.provider('OpenStreetMap.Mapnik').addTo(map),
                'Esri WorldImagery': L.tileLayer.provider('Esri.WorldImagery')
            };
            // MEMBUAT PILIHAN UNTUK MEMILIH LAYER
            var overlays = {
                "Kota Tangerang": {
                    "Administrasi Polygon": layerAdministrasiAr,
                    "Administrasi Line": layerAdministrasiLn
                },
                "Lokasi": {
                    "Kecamatan": layerKecamatanPt
                }
            };
            var options = {
                exclusiveGroups: ["Kota Tangerang"],
                groupCheckboxes: true
            };
            // MENAMPILKAN TOOLS UNTUK MEMILIH BASEMAP
            L.control.groupedLayers(baseLayers, overlays, options).addTo(map);
        </script>
    </div>
</body>

</html>