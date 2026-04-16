
"use strict";
var markers = [],
    map, marker_clusterer;

$(document).ready(function () {


    var mapId = $(".btn[data-bs-target='#mapModal']").is(":visible") ? "modal-main-map" : "project-map";

    mapInitialize(mapId, projects);
    document.getElementById('mapModal').addEventListener('shown.bs.modal', function () {
        mapInitialize(mapId, projects);
    });
    $('#mapModal').on('shown.bs.modal', function () {
        if (!map) {
            mapInitialize(mapId, projects);
        } else {
            map.invalidateSize();
        }
    });
});

function mapInitialize(mapId, projects) {
    window.activeMap && window.activeMap.remove();

    var l = !0,
        p = mapStyle,
        o = !0;
    var defaultCoordinates = [51.505, -0.09]; // Default coordinates (e.g., London)
    var defaultZoom = 3;
 
    map = L.map(mapId, {
        center: [105.931426295101, 160.020130352685],
        minZoom: 0,
        maxZoom: 22,
        scrollWheelZoom: o,
        tap: !L.Browser.mobile,
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topleft'
        }
    });


    var t = L.tileLayer('//{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {

        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map);
    var clusters = L.markerClusterGroup();
    if (projects.length > 0) {
        projects.forEach(element => {
            var s = '<div class="marker-container"><div class="marker-card"><div class="front face"> <i class="fal fa-' + (element.type == 'commercial' ? 'building' : 'home') + '"></i> </div><div class="marker-arrow"></div></div></div>',
                a = L.marker([element.latitude, element.longitude], {
                    icon: L.divIcon({
                        html: s,
                        className: 'open_steet_map_marker google_marker',
                        iconSize: [40, 46],
                        popupAnchor: [1, -35],
                        iconAnchor: [20, 46],
                    })
                });
            let projectUrl = baseURL + '/project/' + element.slug;
            a.bindPopup('<div class="product-default p-0"> <figure class="product-img"> <a href="' + projectUrl + '" class="lazy-container ratio ratio-1-1"> <img class="lazyload" src="assets/images/placeholder.png" data-src="' + imgUrl + 'assets/img/project/featured/' + element.featured_image + '"  > </a></figure><div class="product-details"><h6 class="product-title"><a href="' + projectUrl + '">' + element.title + '</a></h6><span class="product-location icon-start"><i class="fal fa-map-marker-alt"></i>' + element.address + '</span></div></div>', jpopup_customOptions);
            clusters.addLayer(a);
            markers.push(a);
            map.addLayer(clusters);
        });

        if (markers.length) {
            var e = [];
            for (var i in markers) {
                if (typeof markers[i]['_latlng'] == 'undefined') continue;
                var c = [markers[i].getLatLng()];
                e.push(c)
            };
            var r = L.latLngBounds(e);
            map.fitBounds(r)
        };
        if (!markers.length) { }
    } else {
        map.setView(defaultCoordinates, defaultZoom);
    }
    // }
    window.activeMap = map;

    var timerMap, ad_galleries, firstSet = !1,
        mapRefresh = !0,
        loadOnTab = !0,
        zoomOnMapSearch = 9,
        clusterConfig = null,
        markerOptions = null,
        mapDisableAutoPan = !1,
        rent_inc_id = '55',
        scrollWheelEnabled = !1,
        myLocationEnabled = !0,
        rectangleSearchEnabled = !0,
        mapSearchbox = !0,
        mapRefresh = !0,
        map_main, styles, mapStyle = [{
            'featureType': 'landscape',
            'elementType': 'geometry.fill',
            'stylers': [{
                'color': '#fcf4dc'
            }]
        }, {
            'featureType': 'landscape',
            'elementType': 'geometry.stroke',
            'stylers': [{
                'color': '#c0c0c0'
            }, {
                'visibility': 'on'
            }]
        }];

    var jpopup_customOptions = {
        'maxWidth': 'initial',
        'width': 'initial',
        'className': 'popupCustom'
    };

}
