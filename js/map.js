/*global
 google:true,
 Planner:true,
 Airspace:true,
 Graph:true,
 geoXML3:true,
 JQuery:true,
 slider:true,
 */
function UKNXCL_Map($container) {
    this.MAP = 1;
    this.EARTH = 2;

    this.planner = new Planner(this);
    this.airspace = new Airspace();
    this.graph = new Graph($('#graph_wrapper'));
    if (typeof google != 'undefined') {
        this.internal_map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: new google.maps.LatLng(53, -2),
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            streetViewControl: false
        });
    }

    this.callback = function () {};

    this.$container = $container;
    this.$body = $('body');
    //this.$slider = $('#slider');
    this.$tree = $('#tree_content');
    this.mode = this.EARTH;
    /*@param {google.earth}*/
    this.ge = null;
    this.map = null;
    this.obj = null;
    this.kmls = [];
    this.drawRadius = false;
    this.timer = 0;
    this.playCycles = 10;
    this.playCount = 0;
    this.comp = null;
    this.coordinate_tracks = [];

    this.resize = function () {
        var pageWidth = this.$body.width();
        var pageHeight = this.$body.height();
        if (pageWidth < 730) {
            this.$container.hide();
        } else {
            this.$container.show();
        }
        this.$container.css({width: pageWidth - 727});
        this.graph.resize(pageWidth - 745);
        $('#main').css({'height': pageHeight - 50});
        if ($('#colorbox').width()) {
            colorbox_recenter();
        }
    };

    this.load_map = function () {
        map.mode = map.MAP;
        var $map = $('#map').hide();
        var $earth = $('#map3d').show();
        $earth.hide();
        $map.children('p.loading').remove();
        $map.show().css({display: 'block'});
        google.maps.event.addListener(this.internal_map, 'click', function (event) {
            if (map.planner.enabled) {
                var latlon = event.latLng;
                map.planner.addWaypoint(latlon.lat(), latlon.lng());
            }
        });

        this.radiusCircle = new google.maps.Circle({
            center: new google.maps.LatLng(0, 0),
            radius: 0,
            map: this.internal_map,
            strokeColor: "#FFFFFF",
            strokeOpacity: 1,
            zIndex: 1
        });

        this.GeoXMLsingle = new geoXML3.Parser({
            map: this.internal_map,
            singleInfoWindow: true,
            processStyles: true,
            afterParse: function (doc) {
                var path = doc[0].url.split('&id=');
                if ((path[1].isNumber())) {
                    map.kmls[path[1]].google_data = doc[0];
                    map.kmls[path[1]].is_ready();
                }
            }
        });
        this.GeoXMLcomp = new geoXML3.Parser({
            map: this.internal_map,
            singleInfoWindow: true,
            afterParse: function (doc) {
                map.comp.google_data = doc[0];
                map.comp.is_ready();
            }
        });
        map.callback();
    };

    this.swap = function (obj) {
        if (this.obj) { this.obj.hide(); }
        obj.show();
        obj.center();

        if (obj.type === 0 && map.mode === map.MAP) {
            //this.internal_map.fitBounds(obj.get_bounds());
        }
        //this.$slider.slider({max: obj.size()});
        this.graph.swap(obj);
        $('#airspace').hide();
        this.obj = obj;
    };

    this.move = function (value) {
        if (this.obj !== null) {
            value = parseInt(value, 10);
            this.obj.move_marker(value);
            this.setTime(value);
        }
    };

    this.parseKML = function (url, caller) {
        map.caller = caller;
        google.earth.fetchKml(this.ge, 'http://' + window.location.hostname + '/' + url, function (kmlObject) {
            if (!kmlObject) { alert('Error loading KML'); }
            map.ge.getFeatures().appendChild(kmlObject);
            kmlObject.setVisibility(true);
            map.caller.google_data = {root: kmlObject};
            map.caller.is_ready();
            delete map.caller;
        });
    };

    this.load_airspace = function () {
        if (this.mode == this.MAP) {
            $.fn.ajax_factory('\\object\\airspace', 'load_js');
        } else {
            this.parseKML('/resources/airspace.kmz', this.airspace);
        }
        $(".load_airspace").remove();
        $("#tree_content").prepend('<div class=\'kmltree new\'><ul class=\'kmltree\'>' + '<li data-path=\'{"type":"airspace","path":[]}\' class=\'kmltree-item check KmlFolder visible open\'><div class=\'expander\'></div><div class=\'toggler\'></div>Airspace<ul>' + '<li data-path=\'{"type":"airspace","path":[0]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>Prohibited</li>' + '<li data-path=\'{"type":"airspace","path":[1]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>Restricted</li>' + '<li data-path=\'{"type":"airspace","path":[2]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>Danger</li>' + '<li data-path=\'{"type":"airspace","path":[3]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>Other</li>' + '<li data-path=\'{"type":"airspace","path":[4]}\' class=\'kmltree-item check KmlFolder hideChildren visible\'><div class=\'expander\'></div><div class=\'toggler\'></div>CTR/CTA</li>' + '</ul></li></ul></div>');
        return false;
    };

    this.setTime = function (index) {
        var timeInSecs = index * (this.obj.nxcl_data.EndT - this.obj.nxcl_data.StartT) / this.obj.size();
        var hours = Math.floor(timeInSecs / 3600);
        var min = Math.floor((timeInSecs - hours * 3600) / 60);
        var sec = Math.floor(timeInSecs - hours * 3600 - min * 60);
        if (min < 10) {
            min = '0' + min;
        }
        if (sec < 10) {
            sec = '0' + sec;
        }
        $('#Time').html(hours + ":" + min + ":" + sec);
    };

    this.play = function () {
        clearTimeout(this.timer);
        this.playCount = 0;
        this.playCycles = this.obj.size() / 100;
        this.playing();
    };

    this.pause = function () {
        clearTimeout(this.timer);
    };

    this.playing = function () {
        if (this.playCount < this.obj.size() - this.playCycles) {
            this.move(this.playCount += this.playCycles);
        } else {
            this.move(this.obj.size());
            clearTimeout(this.timer);
            return;
        }
        this.timer = setTimeout(function () {map.playing();}, 100);
    };

    this.add_flightC = function (cords, id) {
        var array2 = [];
        var parms = cords.split(';');
        parms.each(function (os, i) {
            var cord = new Coordinate();
            array2[i] = cord.set_from_OS(os);
        });
        this.draw_coords();
    };

    this.draw_coords = function () {
        this.coordinate_tracks.each(function (coord) {
            if (this.mode === map.MAP) {
                if (this.mapObject) {
                    this.mapObject.setMap(null);
                }
                this.mapObject = new google.maps.Polyline({
                    map: this.internal_map,
                    path: function () {

                    },
                    strokeColor: "000000",
                    strokeOpacity: 1,
                    strokeWeight: 1.4
                });
            } else {
                if (!this.mapObject) {
                    var lineStringPlacemark = this.parent.ge.createPlacemark('');
                    this.mapObject = this.parent.ge.createLineString('');
                    lineStringPlacemark.setGeometry(this.mapObject);
                    this.mapObject.setTessellate(true);
                    this.parent.ge.getFeatures().appendChild(lineStringPlacemark);
                    lineStringPlacemark.setStyleSelector(this.parent.ge.createStyle(''));
                    lineStringPlacemark.getStyleSelector().getLineStyle().setColor('FFFFFF');
                }
                this.mapObject.getCoordinates().clear();
                this.coordinates.each(function (coordinate, i, context) {
                    context.mapObject.getCoordinates().pushLatLngAlt(coordinate.lat(), coordinate.lng(), 0);
                }, this);
            }
        });
    };

    this.add_flight = function (id, airspace, reload_flight, temp) {
        this.$tree.find('.track_' + id).remove();
        this.$tree.append('<div class="track_' + id + '"></div>');
        if (this.kmls[id] === undefined || reload_flight) {
            this.kmls[id] = new Track(id, temp);
            this.kmls[id].load();
        } else {
            this.swap(map.kmls[id]);
        }
    };

    this.add_comp = function (id) {
        $('#comp_list').prepend('<div class="loading_shroud">Loading...</div>');
        if (this.comp !== null) {
            this.comp.remove();
        }
        this.$tree.find('.comp_' + id).remove();
        this.$tree.append('<div class="comp_' + id + '"></div>');
        this.comp = new Comp(id);
    };

    this.remove = function (id) {
        this.kmls[id].remove();
        if (this.obj.id === id) {
            Graph.setGraph(null);
        }
    };

    this.load_earth = function () {
        google.load("earth", "1", {'callback': 'map.init_earth'});
        $('#map_interface_3d').find('span.show').click(function () {
            $('#map_interface_3d').stop().animate({left: -250});
            $('#map_interface_3d').find('span.show').hide();
            $('#map_interface_3d').find('span.hide').show();
        });
        $('#map_interface_3d').find('span.hide').click(function () {
            $('#map_interface_3d').stop().animate({left: 0});
            $('#map_interface_3d').find('span.hide').hide();
            $('#map_interface_3d').find('span.show').show();
        });
    };

    this.init_earth = function () {
        $('#map').hide();
        var $earth = $('#map3d').show().css({display: 'block'});
        /*        google.earth.createInstance('map3d', function (instance) {
         $earth.children('p.loading').remove();
         map.mode = map.EARTH;
         map.ge = instance;
         map.ge.getWindow().setVisibility(true);
         map.ge.getNavigationControl().setVisibility(map.ge.VISIBILITY_AUTO);
         map.ge.getLayerRoot().enableLayerById(map.ge.LAYER_ROADS, true);
         var la = map.ge.createLookAt('');
         la.set(52, -2, 5, map.ge.ALTITUDE_RELATIVE_TO_GROUND, 0, 0, 500000);
         map.ge.getView().setAbstractView(la);
         google.earth.addEventListener(map.ge.getGlobe(), 'mousedown', function (event) {
         map._earth_drag = false;
         });
         google.earth.addEventListener(map.ge.getGlobe(), 'mousemove', function (event) {
         map._earth_drag = true;
         });
         google.earth.addEventListener(map.ge.getGlobe(), 'mouseup', function (event) {
         if (map.planner.enabled) {
         if (!map._earth_drag && !map.dragInfo) {
         map.planner.addWaypoint(event.getLatitude(), event.getLongitude(), 'http://maps.google.com/intl/en_us/mapfiles/ms/micons/blue.png');
         }
         map.planner.writeplanner();
         map.planner.draw();
         }
         map.dragInfo = null;
         map.mousedown = false;
         });
         $earth.css({display: 'block'});
         if (map.callback) {
         map.callback();
         }
         }, function () {*/
        map.load_map();
        //});
    };
    this.resize();
}


function Track(id, temp) {
    var ths = this;
    this.type = 0;
    this.id = id;
    this.google_data = null;
    this.nxcl_data = new trackData();
    this.loaded = false;
    this.visible = true;
    this.temp = temp ? '&temp=true' : '';

    this.add_google_data = function () {
        if (map.mode === map.MAP) {
            map.GeoXMLsingle.parse('?module=\\object\\flight&act=download&type=kmz' + this.temp + '&id=' + this.id, null, this.id);
        } else {
            map.parseKML('/uploads/flight/' + this.temp + this.id + '/track_earth.kmz', this);
        }
    };

    this.center = function () {
        if (map.mode == map.EARTH) {
            var lookAt = map.ge.createLookAt('');
            lookAt.setLatitude(this.nxcl_data.bounds.center.lat);
            lookAt.setLongitude(this.nxcl_data.bounds.center.lon);
            lookAt.setRange(this.nxcl_data.bounds.range);
            map.ge.getView().setAbstractView(lookAt)
        } else {

        }
    };

    this.add_nxcl_data = function (callback) {
        $.ajax({
            url: '?module=\\object\\flight&act=get_js&id=' + this.id,
            context: this,
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                this.nxcl_data.loadFromAjax(result);
                callback();
            }
        });
    };

    this.add_marker = function () {
        if (map.mode === map.MAP) {
            this.marker = new google.maps.Marker({
                position: new google.maps.LatLng(this.nxcl_data.track[0].coords[0][0], this.nxcl_data.track[0].coords[0][1]),
                map: map.internal_map,
                cursor: this.nxcl_data.track[0].pilot,
                title: this.nxcl_data.track[0].pilot,
                icon: "../img/Markers/" + this.nxcl_data.track[0].colour + "-" + ( this.nxcl_data.track[0].pilot[0] || 'a' ) + ".png"
            });
        }
    };

    this.is_ready = function () {
        if (this.nxcl_data.loaded && this.google_data) {
            this.loaded = true;
            this.add_marker();
            $('#tree_content .track_' + this.id).html(map.mode == map.MAP ? this.nxcl_data.html : this.nxcl_data.html_earth);
            map.swap(this);
        }
    };

    this.show = function () {
        if (map.mode === map.MAP) {
            this.marker.setMap(map.internal_map);
            this.google_data.gpolylines.each(function (polyline) {
                polyline.setMap(map.internal_map);
            });
        }
        this.center();
        this.visible = true;
        map.graph.setGraph();
    };

    this.hide = function () {
        if (map.mode == map.MAP) {
            this.marker.setMap(null);
            this.google_data.gpolylines.each(function (polyline) {
                polyline.setMap(null);
            });
            map.graph.setGraph();
        }
    };

    this.remove = function (depth) {
        this.hide();
    };

    this.get_bounds = function () {
        return this.google_data.bounds;
    };

    this.size = function () {
        return this.nxcl_data.track[0].coords.length - 1;
    };

    this.move_marker = function (pos) {
        this.marker.setPosition(new google.maps.LatLng(this.nxcl_data.track[0].coords[pos][0], this.nxcl_data.track[0].coords[pos][1]));
        if (map.drawRadius) {
            map.radiusCircle.setCenter(new google.maps.LatLng(this.nxcl_data.track[0].coords[pos][0], this.nxcl_data.track[0].coords[pos][1]));
            map.radiusCircle.setRadius(400);
        } else {
            map.radiusCircle.setRadius(0);
        }
        if (map.airspace.varyWithTrack) {
            this.airspace.reload(this.nxcl_data.track[0].coords[pos][2]);
        }
    };

    this.toggle_track = function (id, bool) {
        id++;
        if (bool) {
            this.google_data.gpolylines[id].setMap(map.internal_map);
        } else {
            this.google_data.gpolylines[id].setMap(null);
        }
    };

    this.load = function () {
        this.add_google_data();
        this.add_nxcl_data(function () {ths.is_ready()});
    }
}

function Comp(id) {
    // variables
    this.type = 1;
    this.id = id;
    this.google_data = null;
    this.nxcl_data = new trackDataArray();
    this.loaded = false;
    this.visible = true;
    this.marker = new Array();
    this.temp = '';

    this.add_google_data = function () {
        if (map.mode === map.MAP) {
            map.GeoXMLcomp.parse('?module=\\module\\comps\\object\\comp&act=download&type=kmz' + this.temp + '&id=' + this.id, null, this.id);
            this.google_data = true;
            this.is_ready();
        } else {
            map.parseKML('/uploads/comp/' + this.id + '/track_earth.kmz', this);
        }
    };

    this.add_nxcl_data = function () {
        $.ajax({
            url: '?module=\\module\\comps\\object\\comp&act=get_js&id=' + this.id,
            context: this,
            cache: false,
            async: true,
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                this.nxcl_data.loadFromAjax(result);
                this.is_ready();
            }
        });
    };

    this.add_marker = function () {
        if (map.mode === map.MAP) {
            this.nxcl_data.track.each(function (track, a, root) {
                root.marker[a] = new google.maps.Marker({
                    position: new google.maps.LatLng(track.coords[0][0], track.coords[0][1]),
                    map: map.internal_map,
                    cursor: track.pilot,
                    title: track.pilot,
                    icon: "../img/Markers/" + track.colour + "-" + track.pilot[0] + ".png"
                });
            }, this);
        }
    };

    this.is_ready = function () {
        if (this.nxcl_data.loaded && this.google_data) {
            this.loaded = true;
            this.add_marker();
            $('#WriteHereComp').html(map.mode == map.MAP ? this.nxcl_data.html : this.nxcl_data.html);
            $('#comp_list .loading_shroud').remove();
            map.swap(this);
        }
    };

    this.center = function () {
        if (map.mode == map.EARTH) {
            var lookAt = map.ge.createLookAt('');
            lookAt.setLatitude(this.nxcl_data.bounds.center.lat);
            lookAt.setLongitude(this.nxcl_data.bounds.center.lon);
            lookAt.setRange(this.nxcl_data.bounds.range);
            map.ge.getView().setAbstractView(lookAt)
        } else {

        }
    };

    this.show = function () {
        this.nxcl_data.track.each(function (track) {
            track.drawGraph = true;
        });
        this.marker.each(function (marker) {
            marker.setMap(map.internal_map);
        });
        if (map.mode == map.MAP) {
            this.google_data.gpolylines.each(function (element) {
                element.setMap(map.internal_map);
            });
            this.google_data.gpolygons.each(function (element) {
                element.setMap(map.internal_map);
            });
            this.visible = true;
            map.graph.setGraph();
        }
    };

    this.hide = function () {
        this.nxcl_data.track.each(function (track) {
            track.drawGraph = false;
        });
        this.marker.each(function (marker) {
            marker.setMap(null);
        });
        this.google_data.gpolylines.each(function (polyline) {
            polyline.setMap(null);
        });
        this.google_data.gpolygons.each(function (polygons) {
            polygons.setMap(null);
        });
        this.visible = false;
        map.graph.setGraph();
    };

    this.remove = function () {
        this.hide();
    };

    this.get_bounds = function () {
        return this.google_data.bounds;
    };

    this.size = function () {
        return this.nxcl_data.track[0].coords.length - 1;
    };

    this.toggle_track = function (id, bool) {
        if (!bool) {
            this.marker[id].setMap(null);
            this.google_data.gpolylines[id].setMap(null);
            this.nxcl_data.track[id].drawGraph = bool;
        } else {
            this.marker[id].setMap(map.internal_map);
            this.google_data.gpolylines[id].setMap(map.internal_map);
            this.nxcl_data.track[id].drawGraph = bool;
        }
        map.graph.setGraph();
    };

    this.move_marker = function (pos) {
        this.marker.each(function (marker, a, root) {
            marker.setPosition(new google.maps.LatLng(root.nxcl_data.track[a].coords[pos][0], root.nxcl_data.track[a].coords[pos][1]));
        }, this);
    };

    // construct
    this.add_google_data();
    this.add_nxcl_data();
}

function Coordinate(lat, lon) {
    if (typeof lat == 'object') {
        this.placemark = lat;
        this.lat = function () {
            if (this.placemark.hasOwnProperty('getLatitude')) {
                return this.placemark.getLatitude();
            } else {
                return this.placemark.position.lat();
            }
        };
        this.lng = function () {
            if (this.placemark.hasOwnProperty('getLongitude')) {
                return this.placemark.getLongitude();
            } else {
                return this.placemark.position.lng();
            }
        };
    } else {
        this._lat = lat || 0;
        this._lon = lon || 0;
        this.lat = function () {
            return this._lat;
        };
        this.lng = function () {
            return this._lon;
        };
        this.set_lat = function (lat) {
            this._lat = lat;
        };
        this.set_lng = function (lng) {
            this._lng = lng;
        };
    }
    this._gridref = null;

    this.set_from_OS = function (gridref) {
        this._gridref = gridref;
        var l1 = gridref.toUpperCase().charCodeAt(0) - 'A'.charCodeAt(0);
        var l2 = gridref.toUpperCase().charCodeAt(1) - 'A'.charCodeAt(0);
        // shuffle down letters after 'I' since 'I' is not used in grid:
        if (l1 > 7) {l1--;}
        if (l2 > 7) {l2--;}

        // convert grid letters into 100km-square indexes from false origin (grid square SV):
        var e = ((l1 - 2) % 5) * 5 + (l2 % 5);
        var n = (19 - Math.floor(l1 / 5) * 5) - Math.floor(l2 / 5);
        if (e < 0 || e > 6 || n < 0 || n > 12) {return false;}

        // skip grid letters to get numeric part of ref, stripping any spaces:
        gridref = gridref.slice(2).replace(/ /g, '');

        // append numeric part of references to grid index:
        e += gridref.slice(0, gridref.length / 2);
        n += gridref.slice(gridref.length / 2);

        // normalise to 1m grid, rounding up to centre of grid square:
        switch (gridref.length) {
            case 0:
                e += '50000';
                n += '50000';
                break;
            case 2:
                e += '5000';
                n += '5000';
                break;
            case 4:
                e += '500';
                n += '500';
                break;
            case 6:
                e += '50';
                n += '50';
                break;
            case 8:
                e += '5';
                n += '5';
                break;
            case 10:
                break; // 10-digit refs are already 1m
        }
        var E = e;
        var N = n;

        var a = 6377563.396, b = 6356256.910;              // Airy 1830 major & minor semi-axes
        var F0 = 0.9996012717;                             // NatGrid scale factor on central meridian
        var lat0 = 49 * Math.PI / 180, lon0 = -2 * Math.PI / 180;  // NatGrid true origin
        var N0 = -100000, E0 = 400000;                     // northing & easting of true origin, metres
        var e2 = 1 - (b * b) / (a * a);                          // eccentricity squared
        n = (a - b) / (a + b);
        var n2 = n * n, n3 = n * n * n;

        var lat = lat0, M = 0;
        do {
            lat = (N - N0 - M) / (a * F0) + lat;

            var Ma = (1 + n + (5 / 4) * n2 + (5 / 4) * n3) * (lat - lat0);
            var Mb = (3 * n + 3 * n * n + (21 / 8) * n3) * Math.sin(lat - lat0) * Math.cos(lat + lat0);
            var Mc = ((15 / 8) * n2 + (15 / 8) * n3) * Math.sin(2 * (lat - lat0)) * Math.cos(2 * (lat + lat0));
            var Md = (35 / 24) * n3 * Math.sin(3 * (lat - lat0)) * Math.cos(3 * (lat + lat0));
            M = b * F0 * (Ma - Mb + Mc - Md);                // meridional arc

        } while (N - N0 - M >= 0.00001);  // ie until < 0.01mm

        var cosLat = Math.cos(lat), sinLat = Math.sin(lat);
        var nu = a * F0 / Math.sqrt(1 - e2 * sinLat * sinLat);              // transverse radius of curvature
        var rho = a * F0 * (1 - e2) / Math.pow(1 - e2 * sinLat * sinLat, 1.5);  // meridional radius of curvature
        var eta2 = nu / rho - 1;

        var tanLat = Math.tan(lat);
        var tan2lat = tanLat * tanLat, tan4lat = tan2lat * tan2lat, tan6lat = tan4lat * tan2lat;
        var secLat = 1 / cosLat;
        var nu3 = nu * nu * nu, nu5 = nu3 * nu * nu, nu7 = nu5 * nu * nu;
        var VII = tanLat / (2 * rho * nu);
        var VIII = tanLat / (24 * rho * nu3) * (5 + 3 * tan2lat + eta2 - 9 * tan2lat * eta2);
        var IX = tanLat / (720 * rho * nu5) * (61 + 90 * tan2lat + 45 * tan4lat);
        var X = secLat / nu;
        var XI = secLat / (6 * nu3) * (nu / rho + 2 * tan2lat);
        var XII = secLat / (120 * nu5) * (5 + 28 * tan2lat + 24 * tan4lat);
        var XIIA = secLat / (5040 * nu7) * (61 + 662 * tan2lat + 1320 * tan4lat + 720 * tan6lat);

        var dE = (E - E0), dE2 = dE * dE, dE3 = dE2 * dE, dE4 = dE2 * dE2, dE5 = dE3 * dE2, dE6 = dE4 * dE2, dE7 = dE5 * dE2;
        lat = lat - VII * dE2 + VIII * dE4 - IX * dE6;
        var lon = lon0 + X * dE - XI * dE3 + XII * dE5 - XIIA * dE7;

        this._lat = lat.toDeg();
        this._lon = lon.toDeg();
    };

    this.gridref = function () {
        if (!this._gridref) {
            this._gridref = this.set_grid_ref();
        }
        return this._gridref;
    };
    this.is_valid_gridref = function () {
        return this._gridref.match(/^(h[l-z]|n[a-hj-z]|s[a-hj-z]|t[abfglmqrvw])[0-9]{6}$/i);

    };

    this.set_grid_ref = function () {
        var lat = this.lat().toRad();
        var lon = this.lng().toRad();

        var a = 6377563.396, b = 6356256.910;          // Airy 1830 major & minor semi-axes
        var F0 = 0.9996012717;                         // NatGrid scale factor on central meridian
        var lat0 = (49).toRad(), lon0 = (-2).toRad();  // NatGrid true origin is 49ºN,2ºW
        var N0 = -100000, E0 = 400000;                 // northing & easting of true origin, metres
        var e2 = 1 - (b * b) / (a * a);                      // eccentricity squared
        var n = (a - b) / (a + b), n2 = n * n, n3 = n * n * n;

        var cosLat = Math.cos(lat), sinLat = Math.sin(lat);
        var nu = a * F0 / Math.sqrt(1 - e2 * sinLat * sinLat);              // transverse radius of curvature
        var rho = a * F0 * (1 - e2) / Math.pow(1 - e2 * sinLat * sinLat, 1.5);  // meridional radius of curvature
        var eta2 = nu / rho - 1;

        var Ma = (1 + n + (5 / 4) * n2 + (5 / 4) * n3) * (lat - lat0);
        var Mb = (3 * n + 3 * n * n + (21 / 8) * n3) * Math.sin(lat - lat0) * Math.cos(lat + lat0);
        var Mc = ((15 / 8) * n2 + (15 / 8) * n3) * Math.sin(2 * (lat - lat0)) * Math.cos(2 * (lat + lat0));
        var Md = (35 / 24) * n3 * Math.sin(3 * (lat - lat0)) * Math.cos(3 * (lat + lat0));
        var M = b * F0 * (Ma - Mb + Mc - Md);              // meridional arc

        var cos3lat = cosLat * cosLat * cosLat;
        var cos5lat = cos3lat * cosLat * cosLat;
        var tan2lat = Math.tan(lat) * Math.tan(lat);
        var tan4lat = tan2lat * tan2lat;

        var I = M + N0;
        var II = (nu / 2) * sinLat * cosLat;
        var III = (nu / 24) * sinLat * cos3lat * (5 - tan2lat + 9 * eta2);
        var IIIA = (nu / 720) * sinLat * cos5lat * (61 - 58 * tan2lat + tan4lat);
        var IV = nu * cosLat;
        var V = (nu / 6) * cos3lat * (nu / rho - tan2lat);
        var VI = (nu / 120) * cos5lat * (5 - 18 * tan2lat + tan4lat + 14 * eta2 - 58 * tan2lat * eta2);

        var dLon = lon - lon0;
        var dLon2 = dLon * dLon, dLon3 = dLon2 * dLon, dLon4 = dLon3 * dLon, dLon5 = dLon4 * dLon, dLon6 = dLon5 * dLon;

        var N = I + II * dLon2 + III * dLon4 + IIIA * dLon6;
        var E = E0 + IV * dLon + V * dLon3 + VI * dLon5;

        var e = E, n = N;
        if (e == NaN || n == NaN) {
            return '??';
        }

        // get the 100km-grid indices
        var e100k = Math.floor(e / 100000), n100k = Math.floor(n / 100000);

        if (e100k < 0 || e100k > 6 || n100k < 0 || n100k > 12) return '';

        // translate those into numeric equivalents of the grid letters
        var l1 = (19 - n100k) - (19 - n100k) % 5 + Math.floor((e100k + 10) / 5);
        var l2 = (19 - n100k) * 5 % 25 + e100k % 5;

        // compensate for skipped 'I' and build grid letter-pairs
        if (l1 > 7) {l1++;}
        if (l2 > 7) {l2++;}
        var letPair = String.fromCharCode(l1 + 'A'.charCodeAt(0), l2 + 'A'.charCodeAt(0));

        // strip 100km-grid indices from easting & northing, and reduce precision
        e = Math.floor((e % 100000) / Math.pow(10, 2));
        n = Math.floor((n % 100000) / Math.pow(10, 2));

        var gridRef = letPair + e.padLz(3) + n.padLz(3);

        return gridRef;
    }
}

function Planner(parent) {
    this.parent = parent || {};
    this.waypoints = [];
    this.enabled = false;
    this.count = 0;
    this.mapObject = null;
    this.coordinates = [];
    this.distance_array = [0];
    this.total_distance_array = [0];
    this.R = 6371;

    this.enable = function () {
        this.enabled = true;
        $("#waypoint_mode_help").show();
        $("#wp_overlay").hide();
    };

    this.writeplanner = function () {
        var out = "<table style='width:100%'>";
        this.coordinates.each(function (coordinate, a) {
            out += '<tr>' + '<td>Turnpoint ' + a + '</td>' + '<td>Lat:' + Math.round(coordinate.lat() * 10000) / 10000 + '</td>' + '<td>Lng:' + Math.round(coordinate.lng() * 10000) / 10000 + '</td>' + '<td>' + Math.round(map.planner.distance_array[a] * 10000) / 10000 + 'km</td>' + '<td>' + Math.round((map.planner.total_distance_array[a] / map.planner.get_total_distance()) * 10000) / 100 + '%</td>' + '<td><a class="remove" href="#" onclick="map.planner.remove(' + a + '); return false;">[x]</a></td>' + '</tr>';
        });
        out += '<tr class="total"><td>Total</td><td/><td/><td>' + Math.floor(this.get_total_distance() * 10000) / 10000 + 'km</td><td/></tr>';
        $('#path').html(out + '</table>');
        var ft = this.get_flight_type();
        if (ft == 'od') { $('#decOD').removeAttr('disabled'); } else { $('#decOD').attr('disabled', 'disabled');}
        if (ft == 'or') { $('#decOR').removeAttr('disabled'); } else { $('#decOR').attr('disabled', 'disabled');}
        if (ft == 'tr') { $('#decTR').removeAttr('disabled'); } else { $('#decTR').attr('disabled', 'disabled');}

        coordinates = this.get_coordinates();
        $('#decOR, #decOD, #decTR').each(function (count) {
            var obj = $(this).data('ajax-post');
            obj.coordinates = coordinates;
            $(this).data('ajax-post', obj);
        });
    };

    this.set_triangle_guides = function () {
        if (map.mode === map.MAP) {
            this.triangle_guides = [
                new google.maps.Polygon({clickable: false, fillColor: '#FF0000', fillOpacity: 0.25, strokeColor: '#FF0000', strokeWeight: 0.8}), new google.maps.Polygon({clickable: false, fillColor: '#FF0000', fillOpacity: 0.25, strokeColor: '#FF0000', strokeWeight: 0.8}), new google.maps.Polygon({clickable: false, fillColor: '#FF0000', fillOpacity: 0.25, strokeColor: '#FF0000', strokeWeight: 0.8})
            ];
        } else {
            this.triangle_guides = Array(3);
            var polygonPlacemark = map.ge.createPlacemark('');
            polygonPlacemark.setStyleSelector(map.ge.createStyle(''));
            var lineStyle = polygonPlacemark.getStyleSelector().getLineStyle();
            var polyStyle = polygonPlacemark.getStyleSelector().getPolyStyle();
            lineStyle.setWidth(2);
            lineStyle.getColor().set('990000ff');
            polyStyle.getColor().set('330000ff');
            polyStyle.setFill(1);

            var polygon = map.ge.createPolygon('');
            polygonPlacemark.setGeometry(polygon);
            var polygon2 = map.ge.createPolygon('');
            polygonPlacemark.setGeometry(polygon);
            var polygon3 = map.ge.createPolygon('');
            polygonPlacemark.setGeometry(polygon);

            ge.getFeatures().appendChild(polygonPlacemark);

            this.triangle_guides[0] = polygon;
            this.triangle_guides[1] = polygon2;
            this.triangle_guides[2] = polygon3;
        }
    };

    this.get_flight_type = function () {
        if (this.count === 4 && this.is_equal(0, 3)) { return 'tr';}
        if (this.is_equal(0, 2)) { return 'or';}
        if ((this.count >= 2 && this.count <= 5)) { return 'od';}
    };

    this.get_coordinates = function () {
        var str = [];
        this.coordinates.each(function (coordinate) {
            str.push(coordinate.gridref());
        });
        return str.join(';');
    };

    this.toGoogleEarth = function () {
        var arr = [];
        this.coordinates.each(function (c) {
            arr.push(new google.maps.LatLng(c.lat(), c.lng()));
        });
        return arr;
    };

    this.draw = function () {
        if (!this.triangle_guides) {
            this.set_triangle_guides();
        }
        if (this.parent.mode === map.MAP) {
            if (this.mapObject) {
                this.mapObject.setMap(null);
            }
            this.mapObject = new google.maps.Polyline({
                map: this.parent.internal_map,
                path: map.planner.toGoogleEarth(),
                strokeColor: "FF0000",
                strokeOpacity: 1,
                strokeWeight: 2
            });
        } else {
            if (!this.mapObject) {
                var lineStringPlacemark = this.parent.ge.createPlacemark('');
                this.mapObject = this.parent.ge.createLineString('');
                lineStringPlacemark.setGeometry(this.mapObject);
                this.mapObject.setTessellate(true);
                this.parent.ge.getFeatures().appendChild(lineStringPlacemark);
                lineStringPlacemark.setStyleSelector(this.parent.ge.createStyle(''));
                var style = lineStringPlacemark.getStyleSelector().getLineStyle();
                style.getColor().set('AA0000CC');
                style.setWidth(4);
            }
            this.mapObject.getCoordinates().clear();
            this.coordinates.each(function (coordinate, i, context) {
                context.mapObject.getCoordinates().pushLatLngAlt(coordinate.lat(), coordinate.lng(), 0);
            }, this);
        }
        if (this.get_flight_type() == 'tr') {
            this.draw_triangle_guides();
        } else {
            this.hide_triangle_guides();
        }
    };

    this.hide_triangle_guides = function () {
        if (this.parent.mode === map.MAP) {
            this.triangle_guides[0].setMap(null);
            this.triangle_guides[1].setMap(null);
            this.triangle_guides[2].setMap(null);
        } else {
            this.triangle_guides[0].setMap(null);
        }
    };

    this.draw_triangle_guides = function () {
        var point1 = [this.coordinates[0].lat(), this.coordinates[0].lng()];
        var point2 = [this.coordinates[1].lat(), this.coordinates[1].lng()];
        var point3 = [this.coordinates[2].lat(), this.coordinates[2].lng()];
        if (this.parent.mode === map.MAP) {
            this.triangle_guides[0].setPath(yessan([point1, point2, point3]));
            this.triangle_guides[1].setPath(yessan([point2, point3, point1]));
            this.triangle_guides[2].setPath(yessan([point3, point1, point2]));
            this.triangle_guides[0].setMap(map.internal_map);
            this.triangle_guides[1].setMap(map.internal_map);
            this.triangle_guides[2].setMap(map.internal_map);
        } else {
            var t = function (points, polygon) {
                polygon.getCoordinates().clear();
                var inner = map.ge.createLinearRing('');
                points.each(function (a) {
                    inner.getCoordinates().pushLatLngAlt(a.lat(), a.lng(), 0);
                });
                polygon.setOuterBoundary(inner);

            };
            var points = yessan([point1, point2, point3]);
            t(points, this.triangle_guides[0]);
            var points = yessan([point2, point3, point1]);
            t(points, this.triangle_guides[1]);
            var points = yessan([point3, point1, point2]);
            t(points, this.triangle_guides[2]);

        }
    };

    this.clear = function () {

        this.waypoints.each(function (point) {
            if (map.mode === map.MAP) {
                point.setMap(null);
            } else {
                map.ge.getFeatures().removeChild(point);
            }
        });
        this.waypoints = [];
        this.coordinates = [];
        this.count = 0;
        this.enabled = false;
        $("#waypoint_mode_help").hide();
        $("#wp_overlay").show();
        this.writeplanner();
        this.draw();
    };

    this.push = function (coordinate) {
        this.coordinates.push(coordinate);
        this.calculate_distances();
        this.count++;
    };
    this.remove = function (index) {
        var cords = [];
        this.coordinates.each(function (coordinate, i) {
            if (i != index) {
                cords.push(coordinate);
            }
        });
        this.coordinates = cords;
        this.calculate_distances();
        this.draw();
        this.writeplanner();
        this.count--;
        return false;
    };

    this.calculate_distances = function () {
        this.distance_array = [0];
        this.total_distance_array = [0];
        this.coordinates.each(function (coordinate, a, context) {
            if (a >= 1) {
                var d = Math.acos(Math.sin(context.coordinates[a - 1].lat().toRad()) * Math.sin(coordinate.lat().toRad()) + Math.cos(context.coordinates[a - 1].lat().toRad()) * Math.cos(coordinate.lat().toRad()) * Math.cos(context.coordinates[a - 1].lng().toRad() - coordinate.lng().toRad())) * context.R;

                context.distance_array.push(d);
                context.total_distance_array.push(context.total_distance_array[a - 1] + d);
            }
        }, this);
    };

    this.is_equal = function (a, b) {
        var count = this.coordinates.length;
        if (count > a && count > b) {
            if (Math.abs(this.coordinates[a].lat() - this.coordinates[b].lat()) < 0.0001 && Math.abs(this.coordinates[a].lng() - this.coordinates[b].lng()) < 0.0001) {
                return true;
            }
        }
        return false;
    };

    this.get_total_distance = function () {
        return this.total_distance_array[this.total_distance_array.length - 1];
    };

    this.addWaypoint = function (lat, lon, image) {
        if (!this.enabled) {
            return;
        }
        if (this.parent.mode === this.parent.EARTH) {
            var placemark = this.parent.ge.createPlacemark('');
            placemark.setName('');
            var point = this.parent.ge.createPoint('');
            point.setLatitude(parseFloat(lat));
            point.setLongitude(parseFloat(lon));
            placemark.setGeometry(point);
            var icon = this.parent.ge.createIcon('');
            icon.setHref(image || 'http://maps.google.com/mapfiles/kml/paddle/red-circle.png');
            var style = this.parent.ge.createStyle('');
            style.getIconStyle().setIcon(icon);
            style.getIconStyle().getHotSpot().set(0.5, this.parent.ge.UNITS_FRACTION, 0, this.parent.ge.UNITS_FRACTION);
            placemark.setStyleSelector(style);
            google.earth.addEventListener(placemark, 'mousedown', function (event) {
                map.dragInfo = {
                    placemark: event.getTarget(),
                    dragged: false
                };
            });
            google.earth.addEventListener(placemark, 'mousemove', function (event) {
                if (map.dragInfo) {
                    event.preventDefault();
                    var point = map.dragInfo.placemark.getGeometry();
                    point.setLatitude(event.getLatitude());
                    point.setLongitude(event.getLongitude());
                    map.dragInfo.dragged = true;
                    map.planner.calculate_distances();
                    map.planner.writeplanner();
                    map.planner.draw();
                }
            });
            google.earth.addEventListener(placemark, 'mouseup', function (event) {
                if (map.dragInfo && map.dragInfo.dragged) {
                    map.dragInfo = null;
                } else {
                    map.planner.push(new Coordinate(map.dragInfo.placemark.getGeometry()));
                    map.dragInfo = null;
                    map.event = event;
                }
                map.planner.writeplanner();
                map.planner.draw();
                event.preventDefault();
                event.stopPropagation();
            });
            this.parent.ge.getFeatures().appendChild(placemark);
            map.planner.waypoints.push(placemark);
        } else {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, lon),
                map: map.internal_map,
                draggable: true
            });
            marker.id = map.planner.waypoints.length;
            google.maps.event.addListener(marker, 'click', function (event) {
                map.planner.push(new Coordinate(marker));
                map.planner.coordinates[map.planner.coordinates.length - 1].markerId = marker.id;
                map.planner.writeplanner();
                map.planner.draw();
                map.event = event;
            });
            google.maps.event.addListener(marker, 'drag', function (event) {
                map.planner.writeplanner();
                map.planner.draw();
                map.planner.calculate_distances()
            });
            google.maps.event.addListener(marker, 'dragend', function (event) {
                map.planner.writeplanner();
                map.planner.draw();
                map.planner.calculate_distances()
            });

            map.planner.waypoints.push(marker);
        }
    };
}

function trackDataArray() {
    this.loaded = false;
    this.id = 0;
    this.StartT = 0;
    this.EndT = 0;
    this.od_score = 0;
    this.or_score = 0;
    this.tr_score = 0;
    this.od_time = 0;
    this.or_time = 0;
    this.tr_time = 0;
    this.track = [];
    this.loadFromAjax = function (json) {
        for (var i in json) {
            if (json[i]) {
                this[i] = json[i];
            }
        }
        this.loaded = true;
    };
}

function trackData() {
    this.loaded = false;
    this.drawGraph = 1;
    this.pilot = 0;
    this.colour = 0;
    this.maxEle = 0;
    this.minEle = 0;
    this.maximum_cr = 0;
    this.min_cr = 0;
    this.maximum_speed = 0;
    this.total_dist = 0;
    this.av_speed = 0;
    this.coords = [];
    this.StartT = 0;
    this.EndT = 0;
    this.loadFromAjax = function (json) {
        for (var i in json) {
            if (typeof json[i] != "function") {
                this[i] = json[i];
            }
        }
        this.loaded = true;
    };
}

var map = new UKNXCL_Map($("#map_wrapper"));
if (typeof google != 'undefined') {
    map.load_earth();
} else {
    $('#map').children('p.loading').html('Failed to load Google resources');
}


UKNXCL_Map.KmlPath = function (event, ths) {
    this.internal_array = [];
    this.event = event;

    this.root = '';
    this.kml = '';
    this.root_data = ths.parents('div.kmltree').eq(0).data('post');
    this.$li = ths.parent();
    this.data = this.$li.data("path");
    this.$parent_li = this.$li.parents("li");


    this.push = function (object) {
        this.internal_array.push(object);
    };

    this.index = function (i) {
        if (i < 0) {
            if (this.internal_array.length + (i - 1) >= 0) {
                return this.internal_array[this.internal_array.length + (i - 1) ];
            }
        } else if (this.internal_array.length >= i) {
            return this.internal_array[i];
        }
        return false

    };

    this.last = function () {
        return this.internal_array[this.internal_array.length - 1];
    };

    this.load = function () {
        if (map.mode == map.EARTH) {
            this._earth_load();
        } else {
            this._map_load()
        }
    };

    this.toggle = function () {
        if (map.mode == map.EARTH) {
            this._earth_toggle();
        } else {
            this._map_toggle();
        }
    };

    this.recursiveHide = function (earthObject) {
        if (map.mode == map.EARTH) {
            this._earth_recursiveHide(earthObject);
        } else {
            this._map_recursiveHide(earthObject);
        }
    };

    this.recursiveShow = function (earthObject) {
        if (map.mode == map.EARTH) {
            this._earth_recursiveShow(earthObject);
        } else {
            this._map_recursiveShow(earthObject);
        }
    };

    this.setVisibility = function (i, bool) {
        if (map.mode == map.EARTH) {
            this.index(i).setVisibility(bool);
        } else {
            if (bool) {
                this._map_recursiveHide(this.index(i));
            } else {
                this._map_recursiveShow(this.index(i));
            }
        }
    };

    this._earth_load = function () {
        if (this.data.type == "comp") {
            this.root = map.comp;
            this.push(this.root.google_data.root);
            this.kml = this.index(0).getFeatures().getChildNodes().item(0);
        } else if (this.data.type == "flight") {
            this.root = map.kmls[this.root_data.id];
            this.push(this.root.google_data.root);
            this.kml = this.index(0).getFeatures().getChildNodes().item(0);
        } else {
            this.root = map.airspace;
            this.kml = map.airspace.google_data.root;
        }
        if (this.data.path !== null) {
            this.data.path.each(function (index, i, ths) {
                var kml = ths.kml.getFeatures().getChildNodes().item(index);
                ths.push(kml);
                ths.kml = kml;
            }, this);
        }
    };

    this._map_load = function () {
        if (this.data.type == "comp") {
            this.root = map.comp;
            this.push(this.root.google_data.structure[0][0]);
            this.path = this.root.google_data.structure[0][0];
        } else if (this.data.type == "flight") {
            this.root = map.kmls[this.root_data.id];
            this.push(this.root.google_data.structure[0][0]);
            this.path = this.root.google_data.structure[0][0];
        } else {
            this.root = map.airspace;
            this.kml = map.airspace.google_data.root;
        }
        if (this.data.path !== null) {
            this.data.path.each(function (index, i, ths) {
                ths.path = ths.path[index];
                ths.push(ths.path);

            }, this);
        }
    };

    this._earth_toggle = function () {
        if (this.$li.hasClass('visible')) {
            if (this.$parent_li.hasClass('radioFolder')) {
                return;
            }
            if (this.$li.hasClass('radioFolder') || this.$li.hasClass('KmlFolder')) {
                this.recursiveHide(this.last());
            }
            this.$li.removeClass('visible');
            this.$li.find('li').removeClass('visible');
        } else {
            if (this.$parent_li.hasClass('radioFolder')) {
                this.recursiveHide(this.index(-1));
                this.setVisibility(-1, true);
                this.$parent_li.addClass('visible');
                this.$li.siblings("li").removeClass('visible');
            }
            this.last().setVisibility(true);
            this.$li.addClass('visible');
            if (this.$li.hasClass('radioFolder')) {
                this.recursiveShow(this.last().getFeatures().getFirstChild());
                this.$li.find('li').eq(0).addClass('visible');

            } else {
                this.$li.find('li').addClass('visible');
                this.recursiveShow(this.last());
            }
        }
        this.root.center();
    };

    this._map_toggle = function () {
        if (this.$li.hasClass('visible')) {
            if (this.$parent_li.hasClass('radioFolder')) {
                return;
            }
            if (this.$li.hasClass('radioFolder') || this.$li.hasClass('KmlFolder')) {
                this.recursiveHide(this.last());
            }
            this.$li.removeClass('visible');
            this.$li.find('li').removeClass('visible');
        } else {
            if (this.$parent_li.hasClass('radioFolder')) {
                this.recursiveHide(this.index(-1));
                this.setVisibility(-1, true);
                this.$parent_li.addClass('visible');
                this.$li.siblings("li").removeClass('visible');
            }
            this.setVisibility();
            this.$li.addClass('visible');
            if (this.$li.hasClass('radioFolder')) {
                this.recursiveShow(this.last());
                this.$li.find('li').eq(0).addClass('visible');

            } else {
                this.$li.find('li').addClass('visible');
                this.recursiveShow(this.last());
            }
        }
        this.root.center();
    };

    this._earth_recursiveHide = function (earthObject) {
        if (typeof earthObject == 'object' && typeof earthObject.getFeatures == 'function') {
            var siblings = earthObject.getFeatures().getChildNodes();
            var length = siblings.getLength();
            for (var i = 0; i < length; i++) {
                this.recursiveHide(siblings.item(i));
            }
            earthObject.setVisibility(false);
        }
    };

    this._map_recursiveHide = function (relative_placemarks) {
        for (var i = 0; i < relative_placemarks.length; i++) {
            if (typeof relative_placemarks[i] == 'object') {
                this.recursiveHide(relative_placemarks[i]);
            } else {
                this.root.google_data.placemarks[relative_placemarks[i]].polyline.setMap(null);
            }
        }
    };

    this._earth_recursiveShow = function (earthObject) {
        if (typeof earthObject == 'object' && typeof earthObject.getFeatures == 'function') {
            var siblings = earthObject.getFeatures().getChildNodes();
            var length = siblings.getLength();
            for (var i = 0; i < length; i++) {
                this.recursiveShow(siblings.item(i));
            }
            earthObject.setVisibility(true);
        }
    };

    this._map_recursiveShow = function (relative_placemarks) {
        for (var i = 0; i < relative_placemarks.length; i++) {
            if (typeof relative_placemarks[i] == 'object') {
                this.recursiveShow(relative_placemarks[i]);
            } else {
                this.root.google_data.placemarks[relative_placemarks[i]].polyline.setMap(map.internal_map);
            }
        }
    }
};


$('body').on('click', '.kmltree .toggler', function (event) {
    kmlPath = new UKNXCL_Map.KmlPath(event, $(this));
    kmlPath.load();
    kmlPath.toggle();
});

$('body').on('click', '.kmltree .expander', function () {
    var $li = $(this).parent();
    if ($li.hasClass('open')) {
        $li.removeClass('open');
        $li.find('li').removeClass('open');
    } else {
        $li.addClass('open');
        $li.find('li').addClass('open');
    }
});

$(document).bind('cbox_complete', function () {
    var width = $('#colorbox').width();
    if (width < 725) {
        $('#colorbox').animate({left: (725 - width) / 2});
    } else {
        $('#colorbox').animate({left: 0});
    }
});

function yessan(coordinates) {
    var projection = map.internal_map.getProjection();
    coordinates.each(function (point, i) {
        coordinates[i] = projection.fromLatLngToPoint(new google.maps.LatLng(point[0], point[1]));
    });
    var sign = direction_check(coordinates);
    var offset = new google.maps.Point(coordinates[1].x - coordinates[0].x, coordinates[1].y - coordinates[0].y);
    var bearing = sign * Math.atan2(offset.y, offset.x);
    map.b = bearing;
    var e, f, h;
    var base_leg = Math.sqrt((offset.x * offset.x) + (offset.y * offset.y));
    var triangle = Array(49);
    for (f = 28; 44 > f; ++f) {
        e = base_leg * f / 28;
        h = base_leg * (72 - f) / 28;
        e = (h * h + base_leg * base_leg - e * e) / (2 * base_leg);
        h = Math.sqrt(h * h - e * e);
        triangle[f - 28] = new google.maps.Point(coordinates[0].x + e * Math.cos(bearing) - h * Math.sin(bearing), coordinates[0].y + sign * (e * Math.sin(bearing) + h * Math.cos(bearing)));
    }
    for (f = 28; 44 > f; ++f) {
        e = base_leg * (72 - f) / f;
        h = 28 * base_leg / f;
        e = (h * h + base_leg * base_leg - e * e) / (2 * base_leg);
        h = Math.sqrt(h * h - e * e);
        triangle[16 + f - 28] = new google.maps.Point(coordinates[0].x + e * Math.cos(bearing) - h * Math.sin(bearing), coordinates[0].y + sign * (e * Math.sin(bearing) + h * Math.cos(bearing)));
    }
    for (f = 44; 28 <= f; --f) {
        e = 28 * base_leg / f;
        h = base_leg * (72 - f) / f;
        e = (h * h + base_leg * base_leg - e * e) / (2 * base_leg);
        h = Math.sqrt(h * h - e * e);
        triangle[76 - f] = new google.maps.Point(coordinates[0].x + e * Math.cos(bearing) - h * Math.sin(bearing), coordinates[0].y + sign * (e * Math.sin(bearing) + h * Math.cos(bearing)));
    }
    triangle.each(function (point, i) {
        triangle[i] = projection.fromPointToLatLng(point);
    });
    map.triangles = triangle;
    return triangle;
}
function direction_check(a) {
    if (3 > a.length) {
        return 0;
    }
    a = cyclic_loop(a, function (a, c) {return new google.maps.Point(a.x - c.x, a.y - c.y)});
    a = cyclic_loop(a, function (a, c) {return (a.x * c.y) - (a.y * c.x)});
    a = loop(a, function (a, c) {return {min: Math.min(a.min, c), max: Math.max(a.max, c)}}, {min: a[0], max: a[1]});
    return 0 > a.max ? -1 : 0 < a.min ? 1 : 0
}

function cyclic_loop(a, b) {
    var c = a.length;
    var d = Array(c);
    for (var e = 0; e < c; ++e) {
        d[e] = b.call(this, a[e], a[(e + 1) % c], e, a);
    }
    return d
}

function loop(a, b, c) {
    if (a.reduce)
        return a.reduce(b, c);
    var d = c;
    a.each(function (c, f) {
        d = b.call(k, d, c, f, a)
    });
    return d
};