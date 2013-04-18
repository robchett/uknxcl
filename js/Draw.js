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

    this.$container = $container;
    this.$body = $('body');
    this.$slider = $('#slider');
    this.mode = this.EARTH;
    /*@param {google.earth}*/
    this.ge = null;
    this.trees = [];
    this.map = null;
    this.obj = null;
    this.kmls = [];
    this.drawRadius = false;
    this.timer = 0;
    this.playCycles = 0;
    this.comp = null;
    this.Waypointmode = false;
    this.visible_flight_types = [true, true, true, true];

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

        this.internal_map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: new google.maps.LatLng(53, -2),
            mapTypeId: google.maps.MapTypeId.TERRAIN,
            streetViewControl: false
        });
        var custom_ui = document.createElement('div');
        custom_ui.id = 'custom_ui';
        var script = document.createElement('script');
        script.innerHTML = 'setTimeout(function(){map.load_custom_ui()},5000);';
        custom_ui.appendChild(script);
        this.internal_map.controls[google.maps.ControlPosition.TOP_RIGHT].push(custom_ui);

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
                var path = doc[0].baseUrl.split('/');
                if (isNumber(path[3])) {
                    map.kmls[path[3]].google_data = doc[0];
                    map.kmls[path[3]].is_ready();
                } else {
                    map.kmls[path[4]].google_data = doc[0];
                    map.kmls[path[4]].is_ready();
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
    };

    this.load_custom_ui = function () {
        if (!$('#custom_ui').children("div").length) {
            $("#custom_ui").html('<div id="flight_types_selector" class="maps_ui_option">' + '<div class="title">Track Types</div>' + '<ul>' + '<li><a onclick="map.toggle_type(1)"><span id="type_1_toggle" class="' + (!map.showing(1) ? 'hidden' : '') + '"><div></div></span><label style="color:blue">Open Distance</label></a></li>' + '<li><a onclick="map.toggle_type(2)"><span id="type_2_toggle" class="' + (!map.showing(2) ? 'hidden' : '') + '"><div></div></span><label style="color:green">Out and Return</label></a></li>' + '<li><a onclick="map.toggle_type(3)"><span id="type_3_toggle" class="' + (!map.showing(3) ? 'hidden' : '') + '"><div></div></span><label style="color:red">Triangle</label></a></li>' + '</ul>' + '</div>' + '<div class="maps_ui_option" id="drawn_flights">' + '<div class="title">Flights</div>' + '<ul></ul>' + '</div>' + '<div id="airspace_selector" class="maps_ui_option">' + '<div class="title">Airspace</div>' + '<ul>' + '<li><p>THIS IS FOR USE AS A GUIDE ONLY</p></li>' + '<li><a onclick="map.toggle_airspace(\'PROHIBITED\');"><span id="airspace_PROHIBITED" class="' + (this.airspace.isVisible('PROHIBITED') ? 'hidden' : '') + '"><div></div></span><label>PROHIBITED</label></a></li>' + '<li><a onclick="map.toggle_airspace(\'RESTRICTED\');"><span id="airspace_RESTRICTED" class="' + (this.airspace.isVisible('RESTRICTED') ? 'hidden' : '') + '"><div></div></span><label>RESTRICTED</label></a></li>' + '<li><a onclick="map.toggle_airspace(\'DANGER\');"    ><span id="airspace_DANGER"     class="' + (this.airspace.isVisible('DANGER') ? 'hidden' : '') + '"><div></div></span><label>DANGER</label></a></li>' + '<li><a onclick="map.toggle_airspace(\'OTHER\');"     ><span id="airspace_OTHER"      class="' + (this.airspace.isVisible('OTHER') ? 'hidden' : '') + '"><div></div></span><label>OTHER</label></a></li>' + '<li><a onclick="map.toggle_airspace(\'CTACTR\');"    ><span id="airspace_CTACTR"     class="' + (this.airspace.isVisible('CTRCTA') ? 'hidden' : '') + '"><div></div></span><label>CTR/CTA</label></a></li>' + '<li><a onclick="map.toggle_airspace(\'ALL\');"       ><span id="airspace_ALL"        class="' + (this.airspace.isVisible('ALL') ? 'hidden' : '') + '"><div></div></span><label>All</label></a></li>' + '<li><a onchange="map.airspace.setHeight(this.value);map.airspace.reload();"><label>Change Height Base</label><select >' + '<option value="4000">4000</option>' + '<option value="4500">4500</option>' + '<option value="5000">5000</option>' + '<option value="5500">5500</option>' + '<option value="6000">6000</option>' + '<option value="6500">6500</option>' + '<option value="7000">7000</option>' + '<option value="7500" selected>7500</option></a></li>' + '<li><a onclick="map.airspace.varyWithTrack=true;" title="Show only airspace that you would cross at your current height level. Move through the track, if your marker is within a coloured zone you have likely entered airspace" class="help"><span><div></div></span><label>vary with flight height [?]</label></a></li>' + '</ul>' + '</div>');
            $('.maps_ui_option').hover(function () {
                $('.maps_ui_option ul').hide();
                $(this).children('ul').stop().show();
            }, function () {
                $(this).children('ul').stop().delay(1000).hide(1);
            });
        }
    };

    //    this.$slider.slider({
    //        max: 0,
    //        value: 0,
    //        slide: function (event, ui) {
    //            map.move(ui.value);
    //        }
    //    });

    this.toggle_task = function (bool) {
        if (typeof this.obj === 'object') {
            this.obj.toggle_task(bool);
        }
    };

    this.swap = function (obj) {
        if (this.obj) {
            this.obj.hide();
        }
        obj.show();

        if (obj.type === 0 && map.mode === map.MAP) {
            this.internal_map.fitBounds(obj.get_bounds());
        }
        //this.$slider.slider({max: obj.size()});
        this.graph.swap(obj);
        this.writeList(obj.id);
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
        google.earth.fetchKml(this.ge, 'http://uk.local.com' + url, function (kmlObject) {
            if (!kmlObject) { alert('Error loading KML'); }
            map.ge.getFeatures().appendChild(kmlObject);
            kmlObject.setVisibility(true);
            map.caller.google_data = {root: kmlObject};
            map.caller.is_ready();
            delete map.caller;
        });
    }

    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

    this.showing = function (a) {
        return this.visible_flight_types[a];
    };

    this.toggle_type = function (a) {
        this.visible_flight_types[a] = !this.visible_flight_types[a];
        $('#type_' + a + '_toggle').toggleClass('hidden');
        if (this.obj && this.obj.nxcl_data) {
            if (this.visible_flight_types[a]) {
                this.obj.google_data.gpolylines[a].setMap(this.internal_map);
            } else {
                this.obj.google_data.gpolylines[a].setMap(null);
            }
        }
    };

    this.toggle_airspace = function (type) {
        $('#airspace_' + type).toggleClass('hidden');

        this.airspace.setVisible(type, !this.airspace.isVisible(type));
        this.airspace.reload();
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
        this.playCycles = Math.floor($('#slider').slider('option', 'max') / 600);
        this.playing();
    };

    this.pause = function () {
        clearTimeout(this.timer);
    };

    this.playing = function () {
        if (this.$slider.slider('value') < this.$slider.slider('option', 'max') - this.playCycles) {
            this.$slider.slider('value', this.$slider.slider('value') + this.playCycles);
            this.move(this.$slider.slider('value'));
        } else {
            this.move(this.$slider.slider('value'));
            this.$slider.slider('option', 'value', this.$slider.slider('option', 'max'));
            clearTimeout(this.timer);
            return;
        }
        this.timer = setTimeout(function () {map.playing();}, 100);
    };

    this.add_flightC = function (cords, id) {
        var array2 = [];
        var parms = cords.split(';');
        for (var i in parms) {
            if (parms[i]) {
                var cord = new Coordinate();
                cord.set_from_OS(parms[i]);
                array2[i] = new google.maps.LatLng(cord.lat(), cord.lng());
            }
        }
        var Polyopt = {path: array2, strokeOpacity: 1.0, strokeWeight: 2, map: map.internal_map, strokeColor: 'FF0000'};
        this.coordinate_tracks.push([id, new google.maps.Polyline(Polyopt)]);
    };

    this.add_flight = function (id, airspace, reload_flight, temp) {
        if (this.comp !== null) {
            this.comp.hide();
        }
        $('#tree_content .track_' + id).remove();
        $('#tree_content').append('<div class="track_' + id + '"></div>');
        if (this.kmls[id] === undefined || reload_flight) {
            this.kmls[id] = new Track(id, temp);
        } else {
            this.swap(map.kmls[id]);
        }
    };

    this.add_comp = function (id) {
        $('#comp_list').prepend('<div class="loading_shroud">Loading...</div>');
        if (this.comp !== null) {
            this.comp.remove();
        }
        if (this.mode === this.MAP) {
            if (!this.airspace.enabled) {
                $('#airspace').hide();
                $('#Airspace').attr('disabled', 'disabled');
                this.airspace.loadAll(false);
                this.airspace.reload();
            }
        }
        if (id !== '0') {
            $('#tree_content .comp_' + id).remove();
            $('#tree_content').append('<div class="comp_' + id + '"></div>');
            this.comp = new Comp(id);
        }
    };


    this.writeList = function (id) {
        var out = '';
        for (var a in this.kmls) {
            if (this.kmls[a].visible) {
                var t = this.kmls[a].nxcl_data;
                out += '<li><a onclick="map.swap(map.kmls[' + a + '])"><span class="' + ((a !== id) ? 'hidden' : '' ) + '"><div></div></span><label>' + a + ' - ' + t.track[0].pilot + '</label></a><a class="remove" href="#" onclick="map.remove(' + a + '); return false;">[x]</a></li>';
            }
        }
        $('#drawn_flights ul').html(out);
        $('#map_interface_3d #content').html(out);
    };

    this.remove = function (id) {
        this.kmls[id].remove();
        if (this.obj.id === id) {
            Graph.setGraph(null);
            var selectedKML = null;
        }
        this.writeList(this.obj.id);
    };

    this.addRectangle = function (lat1, lat2, lon1, lon2, used, score) {
        var rec;
        this.rectangles = [];
        if (!used) {
            this.rectangles.push(new google.maps.Rectangle({
                map: map.internal_map,
                bounds: new google.maps.LatLngBounds(new google.maps.LatLng(lat1, lon1), new google.maps.LatLng(lat2, lon2)),
                strokeColor: "#000000",
                fillColor: "#000000",
                fillOpacity: 0.1,
                strokeOpacity: 0.1,
                strokeWeight: 2,
                title: score
            }));
        } else {
            this.rectangles.push(new google.maps.Rectangle({
                map: map.internal_map,
                bounds: new google.maps.LatLngBounds(new google.maps.LatLng(lat1, lon1), new google.maps.LatLng(lat2, lon2)),
                strokeColor: "#FF0000",
                fillColor: "#FF0000",
                fillOpacity: 0.1,
                strokeOpacity: 1,
                strokeWeight: 4,
                title: score
            }));
            if (lat1 === lat2 && lon1 === lon2) {
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat1, lon1),
                    map: map.internal_map
                });
                marker.id = map.planner.waypoints.length;
                google.maps.event.addListener(marker, 'click', function (event) {
                    map.planner.push(event.latLng);
                    map.planner[map.planner.length - 1].markerId = marker.id;
                    map.planner.writeplanner();
                    map.planner.draw();
                    map.event = event;
                });
                map.planner.waypoints.push(marker);
            }
        }
    };

    this.OSGridToLatLong = function (gridref) {
        var cord = new Coordinate();
        cord.set_from_OS(gridref);
        return [cord.lat(), cord.lng()];
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
        google.earth.createInstance('map3d', function (instance) {
            $('#map').hide();
            $('#map_interface').hide();
            $('#map3d').show();
            $('#map_interface_3d').show();
            map.mode = map.EARTH;
            map.ge = instance;
            map.ge.getWindow().setVisibility(true);
            map.ge.getNavigationControl().setVisibility(map.ge.VISIBILITY_AUTO);
            map.ge.getLayerRoot().enableLayerById(map.ge.LAYER_ROADS, true);
            var la = map.ge.createLookAt('');
            la.set(52, -2, 5, map.ge.ALTITUDE_RELATIVE_TO_GROUND, 0, 0, 500000);
            map.ge.getView().setAbstractView(la);
            google.earth.addEventListener(map.ge.getGlobe(), 'mousedown', function (event) {
                if (map.planner.enabled) {
                    event.preventDefault();
                    event.stopPropagation();
                    map._earth_drag = false;
                }
            });
            google.earth.addEventListener(map.ge.getGlobe(), 'mousemove', function (event) {
                if (map.planner.enabled) {
                    map._earth_drag = true;
                }
            });
            google.earth.addEventListener(map.ge.getGlobe(), 'mouseup', function (event) {
                if (map.planner.enabled) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (!map._earth_drag) {
                        map.planner.addWaypoint(event.getLatitude(), event.getLongitude(), 'http://maps.google.com/intl/en_us/mapfiles/ms/micons/blue.png');
                    }
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
        }, function () {
            map.mode = map.MAP;
            map.load_map();
            $('#map').show();
            $('#map3d').hide();
        });
    };
    this.resize();
}


function Track(id, temp) {
    // variables
    this.type = 0;
    this.id = id;
    this.google_data = null;
    this.nxcl_data = new trackData();
    this.loaded = false;
    this.visible = true;
    this.temp = temp ? 'temp/' : '';

    this.add_google_data = function () {
        if (map.mode === map.MAP) {
            this.google_data = map.GeoXMLsingle.parse('/uploads/track/' + this.temp + this.id + '/Track.kml', null, id);
        } else {
            map.parseKML('/uploads/track/' + this.temp + this.id + '/Track_Earth.kml', this);
        }
    };

    this.center = function () {
        if (map.mode = map.EARTH) {
            var lookAt = map.ge.createLookAt('');
            lookAt.setLatitude(this.nxcl_data.bounds.center.lat);
            lookAt.setLongitude(this.nxcl_data.bounds.center.lon);
            lookAt.setRange(this.nxcl_data.bounds.range);
            map.ge.getView().setAbstractView(lookAt)
        } else {

        }
    }

    this.add_nxcl_data = function () {
        $.ajax({
            url: '?module=flight&act=get_js&id=' + this.id,
            context: this,
            cache: false,
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                this.nxcl_data.loadFromAjax(result);
                this.is_ready();
            },
            fail: function (result) {
                console.log(result);
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
            $('#tree_content .track_' + this.id).html( map.mode == map.MAP ? this.nxcl_data.html : this.nxcl_data.html_earth);
            map.swap(this);
        }
    };

    this.show = function () {
        if (!this.visible) {
            if (map.mode === map.MAP) {
                this.marker.setMap(map.internal_map);
                for (var a in this.google_data.gpolylines) {
                    if (map.showing(a)) {
                        this.google_data.gpolylines[a].setMap(map.internal_map);
                    }
                }
            }
        }
        this.center();
        this.visible = true;
    };

    this.hide = function (depth) {
        this.marker.setMap(null);
        for (var a in this.google_data.gpolylines) {
            if (this.google_data.gpolylines[a]) {
                this.google_data.gpolylines[a].setMap(null);
            }
        }
    };

    this.remove = function (depth) {
        this.marker.setMap(null);
        for (var a in this.google_data.gpolylines) {
            if (this.google_data.gpolylines[a]) {
                this.google_data.gpolylines[a].setMap(null);
            }
        }
        this.visible = false;
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

    this.toggle_task = function () {
    };

    this.add_google_data();
    this.add_nxcl_data();
}

function Comp(id) {
    // variables
    this.type = 1;
    this.id = id;
    this.google_data = null;
    this.nxcl_data = new trackDataArray();
    this.loaded = false;
    this.visible = true;
    this.marker = [];
    this.temp = '';

    this.add_google_data = function () {
        if (map.mode === map.MAP) {
            map.GeoXMLcomp.parse('/uploads/comp/' + this.id + '/track.kml?' + Math.floor(Math.random() * 1000), null, id);
            this.google_data = true;
            this.is_ready();
        } else {
            map.parseKML('/uploads/comp/' + this.id + '/track.kml', this);
        }
    };

    this.add_nxcl_data = function () {
        $.ajax({
            url: '?module=comp&act=get_js&id=' + this.id,
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
            for (var a in this.nxcl_data.track) {
                if (this.nxcl_data.track[a]) {
                    this.marker[a] = new google.maps.Marker({
                        position: new google.maps.LatLng(this.nxcl_data.track[a].coords[0][0], this.nxcl_data.track[a].coords[0][1]),
                        map: map.internal_map,
                        cursor: this.nxcl_data.track[a].pilot,
                        title: this.nxcl_data.track[a].pilot,
                        icon: "../img/Markers/" + this.nxcl_data.track[a].colour + "-" + this.nxcl_data.track[a].pilot[0] + ".png"
                    });
                }
            }
        }
    };

    this.is_ready = function () {
        if (this.nxcl_data.loaded && this.google_data) {
            this.loaded = true;
            this.add_marker();
            this.center();
            $('#WriteHereComp').html(map.mode == map.MAP ? this.nxcl_data.html : this.nxcl_data.html);
            $('#comp_inner').animate({'left': -730});
            $('#comp_list .loading_shroud').remove();
            map.graph.swap(this);
            map.swap(this);
        }
    };

    this.center = function () {
        if (map.mode = map.EARTH) {
            var lookAt = map.ge.createLookAt('');
            lookAt.setLatitude(this.nxcl_data.bounds.center.lat);
            lookAt.setLongitude(this.nxcl_data.bounds.center.lon);
            lookAt.setRange(this.nxcl_data.bounds.range);
            map.ge.getView().setAbstractView(lookAt)
        } else {

        }
    }

    this.show = function () {
        for (var a in this.marker) {
            if (this.marker[a]) {
                this.marker[a].setMap(map.internal_map);
            }
        }
        for (a in this.google_data.gpolylines) {
            if (this.marker[a]) {
                this.google_data.gpolylines[a].setMap(map.internal_map);
            }
        }
        this.center();
        this.visible = true;
    };

    this.hide = function (depth) {
        for (var a in this.marker) {
            if (this.marker[a]) {
                this.marker[a].setMap(null);
            }
        }
        for (a in this.google_data.gpolylines) {
            if (a !== depth) {this.google_data.gpolylines[a].setMap(null);}
        }
        for (a in this.google_data.gpolygons) {
            if (a !== depth) {this.google_data.gpolygons[a].setMap(null);}
        }
        this.visible = false;
    };

    this.remove = function (depth) {
        for (var a in this.marker) {
            if (this.marker[a]) {
                this.marker[a].setMap(null);
            }
        }
        for (a in this.google_data.gpolylines) {
            if (this.google_data.gpolylines[a]) {
                if (a !== depth) {this.google_data.gpolylines[a].setMap(null);}
            }
        }
        for (a in this.google_data.gpolygons) {
            if (a !== depth) {
                this.google_data.gpolygons[a].setMap(null);
            }
        }
        this.visible = false;
    };

    this.get_bounds = function () {
        return this.google_data.bounds;
    };

    this.size = function () {
        return this.nxcl_data.track[0].coords.length - 1;
    };

    this.toggle_track = function (id, bool) {
        if (id === null) {
            for (var i in this.nxcl_data.track) {
                if (this.nxcl_data.track[i]) {
                    this.toggle_track(i, bool);
                }
            }
            if (bool) {
                $('.track').attr('checked', 'checked');
            } else {
                $('.track').removeAttr('checked');
            }
        } else if (!bool) {
            this.marker[id].setMap(null);
            this.google_data.gpolylines[id].setMap(null);
        } else {
            this.marker[id].setMap(map.internal_map);
            this.google_data.gpolylines[id].setMap(map.internal_map);
        }
    };

    this.toggle_graph = function (id, bool) {
        if (id === null) {
            for (var i in this.nxcl_data.track) {
                if (this.nxcl_data.track[i]) {
                    this.nxcl_data.track[i].drawGraph = bool;
                }
            }
            if (bool) {
                $('.graph').attr('checked', 'checked');
            } else {
                $('.graph').removeAttr('checked');
            }
        } else {this.nxcl_data.track[id].drawGraph = bool;}
        Graph.setGraph();
    };

    this.toggle_task = function (bool) {
        if (bool) {
            this.google_data.gpolygons[this.google_data.gpolygons.length - 1].setMap(map.internal_map);
        } else {
            this.google_data.gpolygons[this.google_data.gpolygons.length - 1].setMap(null);
        }
    };

    this.move_marker = function (pos) {
        for (var a in this.marker) {
            if (this.marker[a]) {
                this.marker[a].setPosition(new google.maps.LatLng(this.nxcl_data.track[a].coords[pos][0], this.nxcl_data.track[a].coords[pos][1]));
            }
        }
    };

    // construct
    this.add_google_data();
    this.add_nxcl_data();
}

function Coordinate(lat, lon) {
    this._lat = lat || 0;
    this._lon = lon || 0;
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

    this.lat = function () {
        return this._lat;
    };
    this.lng = function () {
        return this._lon;
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

    this.writeplanner = function () {
        var out = "<table style='width:100%'>";
        for (var a in this.coordinates) {
            if (this.coordinates[a]) {
                out += '<tr>' + '<td>Turnpoint ' + a + '</td>' + '<td>Lat:' + Math.round(this.coordinates[a].lat() * 10000) / 10000 + '</td>' + '<td>Lng:' + Math.round(this.coordinates[a].lng() * 10000) / 10000 + '</td>' + '<td>' + Math.round(this.distance_array[a] * 10000) / 10000 + 'km</td>' + '<td>' + Math.round((this.total_distance_array[a] / this.get_total_distance()) * 10000) / 100 + '%</td>' + '<td><a class="remove" href="#" onclick="map.planner.remove(' + a + '); return false;">[x]</a></td>' + '</tr>';
            }
        }
        out += '<tr class="total"><td>Total</td><td/><td/><td>' + Math.floor(this.get_total_distance() * 10000) / 10000 + 'km</td><td/></tr>';
        $('#path').html(out + '</table>');
        if ((this.count >= 2 && this.count <= 5)) {$('#decOD').removeAttr('disabled');} else { $('#decOD').attr('disabled', 'disabled');}
        if (this.is_equal(0, 2)) { $('#decOR').removeAttr('disabled'); } else { $('#decOR').attr('disabled', 'disabled');}
        if (this.count === 4 && this.is_equal(0, 3)) { $('#decTR').removeAttr('disabled'); } else { $('#decTR').attr('disabled', 'disabled');}

        coordinates = this.get_coordinates();
        $('#decOR, #decOD, #decTR').each(function (count) {
            var obj = $(this).data('ajax-post');
            obj.coordinates = coordinates;
            $(this).data('ajax-post', obj);
        });
    };

    this.get_coordinates = function () {
        var str = [];
        for (var a in this.coordinates) {
            if (this.coordinates[a]) {
                str.push(this.coordinates[a].gridref());
            }
        }
        return str.join(';');
    };

    this.draw = function () {
        if (this.parent.mode === this.MAP) {
            if (this.mapObject) {
                this.mapObject.setMap(null);
            }
            this.mapObject = new google.maps.Polyline({
                map: this.parent.internal_map,
                path: this.coordinates,
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
            for (var a in this.coordinates) {
                if (this.coordinates[a]) {
                    this.mapObject.getCoordinates().pushLatLngAlt(this.coordinates[a].lat(), this.coordinates[a].lng(), 0);
                }
            }
        }
    };

    this.clear = function () {

        for (var a in this.waypoints) {
            if (this.waypoints[a]) {
                if (this.parent.mode === this.parent.MAP) {
                    this.waypoints[a].setMap(null);
                } else {
                    this.parent.ge.getFeatures().removeChild(this.waypoints[a]);
                }
            }
        }
        this.waypoints = [];
        this.coordinates = [];
        this.count = 0;
        this.enabled = false;
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
        for (var i in this.coordinates) {
            if (i != index) {
                cords.push(this.coordinates[i]);
            }
        }
        this.coordinates = cords;
        this.calculate_distances();
        this.draw();
        this.writeplanner();
        this.count--;
    };

    this.calculate_distances = function () {
        this.distance_array = [0];
        this.total_distance_array = [0];
        for (var a in this.coordinates) {
            if (a >= 1) {
                var d = Math.acos(Math.sin(this.coordinates[a - 1].lat().toRad()) * Math.sin(this.coordinates[a].lat().toRad()) + Math.cos(this.coordinates[a - 1].lat().toRad()) * Math.cos(this.coordinates[a].lat().toRad()) * Math.cos(this.coordinates[a - 1].lng().toRad() - this.coordinates[a].lng().toRad())) * this.R;

                this.distance_array.push(d);
                this.total_distance_array.push(this.total_distance_array[a - 1] + d);
            }
        }
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
                event.preventDefault();
                event.stopPropagation();
                map._earth_drag = false;
                return false;
            });
            google.earth.addEventListener(placemark, 'mousemove', function (event) {
                map._earth_drag = true;
                return false;
            });
            google.earth.addEventListener(placemark, 'mouseup', function (event) {
                event.preventDefault();
                event.stopPropagation();
                if (!map._earth_drag) {
                    var lat = event.getCurrentTarget().getGeometry().getLatitude();
                    var lon = event.getCurrentTarget().getGeometry().getLongitude();
                    map.planner.push(new Coordinate(lat, lon));
                    map.planner.writeplanner();
                    map.planner.draw();
                    map.event = event;
                }
                return false;
            });
            this.parent.ge.getFeatures().appendChild(placemark);
            map.planner.waypoints.push(placemark);
        } else {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, lon),
                map: map.internal_map
            });
            marker.id = map.planner.waypoints.length;
            google.maps.event.addListener(marker, 'click', function (event) {
                map.planner.push(event.latLng);
                map.planner[map.planner.length - 1].markerId = marker.id;
                map.planner.writeplanner();
                map.planner.draw();
                map.event = event;
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
    this.loadFromAjax = function (json) {
        for (var i in json) {
            if (json[i]) {
                this[i] = json[i];
            }
        }
        this.loaded = true;
    };
}

var map = new UKNXCL_Map($("#map_wrapper"));
map.load_earth();

$('body').on('click', '.kmltree .toggler', function () {
    var root_data = $(this).parents('div.kmltree').eq(0).data('post');
    var $li = $(this).parent();
    var data = $li.data("path");
    if( data.type == "comp" ) {
        var kml = map.comp.google_data.root;
    } else {
        var kml = map.kmls[root_data.id].google_data.root;
    }

    if (data.path !== null) {
        for (i in data.path) {
            kml = kml.getFeatures().getChildNodes().item(data.path[i]);
        }
    }
    if ($li.hasClass('visible')) {
        kml.setVisibility(false);
        $li.removeClass('visible');
        $li.find('li').removeClass('visible');
    } else {
        kml.setVisibility(true);
        $li.addClass('visible');
        $li.find('li').addClass('visible');
    }
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
