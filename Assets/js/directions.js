var points = [],
url_osrm_nearest = '//router.project-osrm.org/nearest/v1/driving/',
url_osrm_route = '//router.project-osrm.org/route/v1/driving/',
vectorSource = new ol.source.Vector({
    url: 'https://openlayers.org/en/v4.2.0/examples/data/geojson/countries.geojson',
    // format: new ol.format.GeoJSON()
  }),
vectorLayer = new ol.layer.Vector({
    source: vectorSource,
}),

styles = {
    route: new ol.style.Style({
        stroke: new ol.style.Stroke({
        width: 3, color: [40, 4, 40, 0.8]
        })
    }),
    icon: new ol.style.Style({
        image: new ol.style.Icon({
        anchor: [0.5, 1],
        // src: icon_url
        })
    })
};

console.clear();

var center = ol.proj.fromLonLat([-93.59277789139063, 41.590686510484915]);
markerGroup = new ol.layer.Group({
    layers: [],
    name: 'markerGroup'
});
var map = new ol.Map({
    target: 'map',
    layers: 
    [
        new ol.layer.Tile({
            source: new ol.source.OSM(),
        }),
        vectorLayer
    ],
    view: new ol.View({
        center: center,
        zoom: 5,
        maxZoom: 18,
    }),
});
map.setTarget($("#map")[0]);


var distance = 0
var duration = 0
var weight = 0

function secondsToHms(d) {
    d = Number(d);
    var h = Math.floor(d / 3600);
    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);

    var hDisplay = h > 0 ? h + (h == 1 ? " hr, " : " hrs, ") : "";
    var mDisplay = m > 0 ? m + (m == 1 ? " min, " : " mins, ") : "";
    var sDisplay = s > 0 ? s + (s == 1 ? " sec" : " secs") : "";
    // return hDisplay + mDisplay + sDisplay; 
    return hDisplay + mDisplay; 
}

var ndistance = 0,
nduration = 0,
nweight = 0

function get_directions(coordinates){
    var last_point = points[points.length - 1];
    // console.log(points)
    var points_length = points.push(coordinates);

    var msg_el = $(".directions_info")

    var feature = new ol.Feature({
        type: 'place',
        geometry: new ol.geom.Point(ol.proj.fromLonLat(coordinates)),
    });

    var src = points.length == 1 ? './Assets/Images/Not Available.png' : 'https://maps.google.com/mapfiles/kml/paddle/'+ [points.length] + '.png'

    feature.setStyle(
        new ol.style.Style({
        image: new ol.style.Icon({
            anchor: [0.5, 1],
            src: src,
            // src: './markers.svg',
            // src: "./image.php?color=red&number=2",
            // size: [40, 40],
            scale: 0.6
            // width: '10'
        })
    }),);
    
    vectorSource.addFeature(feature);

    if (points_length < 2) {
        msg_el.html('Click to add another point');
        return;
    }

    //get the route
    var point1 = last_point.join();
    var point2 = coordinates.join();
    
    fetch(url_osrm_route + point1 + ';' + point2).then(function(r) { 
        return r.json();
    }).then(function(json) {
        // console.log(json.routes[0])
        if(json.code !== 'Ok') {
            msg_el.html('No route found.');
            return;
        }
        ndistance += (json.routes[0].distance)
        nduration += (json.routes[0].duration)

        var html = `<h4>Estimated distance and duration</h4>
            <p>
                <i class="fa-solid fa-route"></i>
                <span>Est : Distance: </span>
                <span  style="color: #dadada; " >${(parseFloat(ndistance * 0.000621371192).toFixed(2) + " mi" )}</span>
            </p>
            <p>
                <i class="fa-regular fa-calendar-check"></i>
                <span>Est : Duration: </span>
                <span  style="color: #dadada; " >${(secondsToHms(nduration))}</span>
        </p>`

        msg_el.html(html)

        utils.createRoute(json.routes[0].geometry);
    });
}

map.on('dblclick', function(evt){
    evt.preventDefault()

    utils.getNearest(evt.coordinate).then(function(coord_street){
        get_directions(coord_street)
    });
});

var utils = {
    getNearest: function(coord){
        var coord4326 = utils.to4326(coord);
        return new Promise(function(resolve, reject) {
            //make sure the coord is on street
            fetch(url_osrm_nearest + coord4326.join()).then(function(response) { 
                // Convert to JSON
                return response.json();
            }).then(function(json) {
                console.log( json)
                if (json.code === 'Ok') resolve(json.waypoints[0].location);
                else reject();
            });
        });
    },
    createFeature: function(coord) {
        var feature = new ol.Feature({
            type: 'place',
            geometry: new ol.geom.Point(ol.proj.fromLonLat(coord))
        });
        feature.setStyle(
            new ol.style.Style({
            image: new ol.style.Icon({
                anchor: [0.5, 1],
                src: 'https://maps.google.com/mapfiles/kml/paddle/'+ [i + 1] + '.png',
                // size: [40, 40],
                scale: 0.6
                // width: '10'
            })
        }),);
        vectorSource.addFeature(feature);
    },
    createRoute: function(polyline) {
        // route is ol.geom.LineString
        var route = new ol.format.Polyline({
            factor: 1e5
        }).readGeometry(polyline, {
            dataProjection: 'EPSG:4326',
            featureProjection: 'EPSG:3857'
        });
        var feature = new ol.Feature({
            type: 'route',
            geometry: route
        });
        feature.setStyle(styles.route);
        vectorSource.addFeature(feature);
    },
    to4326: function(coord) {
        return ol.proj.transform([
            parseFloat(coord[0]), parseFloat(coord[1])
        ], 'EPSG:3857', 'EPSG:4326');
    }
};

$(".address_con").on("change", ".start", function(e){
    var parentElement = $(this).parent();
    if ( parentElement.is(':last-child') ) {
        $(".add_destination").css({"display": "block"})
    }  
})

$("body").on('click keydown', '.start', function(e) {
    var lat = $(this).next()
    var lng = lat.next()

    initMap(this, lat, lng)
})

function initMap(ele, lat, lng) {
    var startValue;
    var startAutocomplete = new google.maps.places.Autocomplete(ele);

    startAutocomplete.addListener("place_changed", function() {
        var place = startAutocomplete.getPlace()
        lat.val(place.geometry.location.lat())
        lng.val(place.geometry.location.lng())
        startValue = place.formatted_address;

        var coordinates = [place.geometry.location.lng(), place.geometry.location.lat()]
        get_directions(coordinates)
    });
    
}

$("body").on("click", ".add_destination", function(e){
    var html = `<div class="des_div loc">
        <span class="icon "> <div class="end_pin"></div> </span>
        <input type="text" class="start" placeholder="Add the destination point"  style="width: 250px; font-size: 15px;">
        <input type="hidden" name="closest_lat" id="closest_lat" value="">
        <input type="hidden" name="closest_lng" id="closest_lng" value="">
        <span class="remove"><i class="fa-regular fa-circle-xmark"></i></span>
    </div>`

    $(".address_con").append(html)

    $(this).css({"display": "none"})
})


$("body").on("click", ".remove", function(e){
    var parent = $(this).parent()
    var lat = Number(parent.find("#closest_lat").val())
    var lng = Number(parent.find('#closest_lng').val())
    var d = 0, dd = 0
    var msg_el = $(".directions_info")

    points = $.grep(points, function(item) {
        return !arraysEqual(item, [lng, lat]);
    });

    iconFeatures = [];
    var features = vectorSource.getFeatures();
    features.forEach((feature) => {
        vectorSource.removeFeature(feature);
    });
    vectorSource.clear();
    vectorSource.addFeatures(iconFeatures);


    for(i=0; i< points.length; i++){
        var last_point = ""
        i == 0 ? last_point = points[i] : last_point = points[i - 1]
        var feature = new ol.Feature({
            type: 'place',
            geometry: new ol.geom.Point(ol.proj.fromLonLat(points[i]))
        });

        var src = i == 0 ? './Assets/Images/Not Available.png' : 'https://maps.google.com/mapfiles/kml/paddle/'+ [i+1] + '.png'
        feature.setStyle(
            new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.5, 1],
                    src: src,
                    // size: [40, 40],
                    scale: 0.6
                    // width: '10'
                })
            }),
        )
        vectorSource.addFeature(feature);

        //get the route
        var point1 = last_point.join();
        var point2 = points[i].join();
        
        fetch(url_osrm_route + point1 + ';' + point2).then(function(r) { 
            return r.json();
        }).then(function(json) {
            
            if(json.code !== 'Ok') {
                return;
            }

            d += (json.routes[0].distance)
            dd += (json.routes[0].duration)

            var html = `<h4>Estimated distance and duration</h4>
                <p>
                    <i class="fa-solid fa-route"></i>
                    <span>Est : Distance: </span>
                    <span  style="color: #dadada; " >${(parseFloat(d * 0.000621371192).toFixed(2) + " mi" )}</span>
                </p>
                <p>
                    <i class="fa-regular fa-calendar-check"></i>
                    <span>Est : Duration: </span>
                    <span  style="color: #dadada; " >${(secondsToHms(dd))}</span>
            </p>`

            msg_el.html(html)
            utils.createRoute(json.routes[0].geometry);
        });
    }

    $(this).parent().remove()
})



function arraysEqual(arr1, arr2) {
    if (arr1.length !== arr2.length) {
      return false;
    }
  
    for (var i = 0; i < arr1.length; i++) {
      if (arr1[i] !== arr2[i]) {
        return false;
      }
    }
  
    return true;
}