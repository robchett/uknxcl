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
        $("body").addClass('waypoint_mode');
    };

    this.get_share_link = function() {
        var out = document.location.host + '/planner/';
        this.coordinates.each(function(coordinate) {
            out += coordinate.lat().toFixed(6) + ',' + coordinate.lng().toFixed(6) + ';';
        });
        return out.trim(';');
    };

    this.writeplanner = function () {
        this.calculate_distances();
        var out =
            "<table class='results main' style='width:100%'>" +
            "<thead><tr><th></th><th>Lat</th><th>Lng</th><th>Distance</th><th></th><th></th></tr></thead>";
        this.coordinates.each(function (coordinate, a) {
            out += '<tr>' + '<td>Turnpoint ' + a + '</td>' + '<td>Lat:' + Math.round(coordinate.lat() * 10000) / 10000 + '</td>' + '<td>Lng:' + Math.round(coordinate.lng() * 10000) / 10000 + '</td>' + '<td>' + Math.round(map.planner.distance_array[a] * 10000) / 10000 + 'km</td>' + '<td>' + Math.round((map.planner.total_distance_array[a] / map.planner.get_total_distance()) * 10000) / 100 + '%</td>' + '<td><a class="remove" href="#" onclick="map.planner.remove(' + a + '); return false;">[x]</a></td>' + '</tr>';
        });
        out += '<tr class="total"><td>Total</td><td/><td/><td>' + Math.floor(this.get_total_distance() * 10000) / 10000 + 'km</td><td/><td></td></tr>';
        $('#path').html(out + '</table><h4 class="heading">Share this track:</h4><p><pre>' + this.get_share_link() + '</pre></p>');
        var ft = this.get_flight_type();
        if (ft == 'od') { $('#decOD').removeAttr('disabled'); } else { $('#decOD').attr('disabled', 'disabled');}
        if (ft == 'or') { $('#decOR').removeAttr('disabled'); } else { $('#decOR').attr('disabled', 'disabled');}
        if (ft == 'tr') { $('#decTR').removeAttr('disabled'); } else { $('#decTR').attr('disabled', 'disabled');}

        var coordinates = this.get_coordinates();
        $('#decOR, #decOD, #decTR').each(function () {
            var obj = $(this).data('ajax-post');
            obj.coordinates = coordinates;
            $(this).data('ajax-post', obj);
        });
    };

    this.set_triangle_guides = function () {
        if (this.parent.isMap()) {
            this.triangle_guides = [
                new google.maps.Polygon({clickable: false, fillColor: '#FF0000', fillOpacity: 0.25, strokeColor: '#FF0000', strokeWeight: 0.8}), new google.maps.Polygon({clickable: false, fillColor: '#FF0000', fillOpacity: 0.25, strokeColor: '#FF0000', strokeWeight: 0.8}), new google.maps.Polygon({clickable: false, fillColor: '#FF0000', fillOpacity: 0.25, strokeColor: '#FF0000', strokeWeight: 0.8})
            ];
        } else {
            this.triangle_guides = Array(3);
            var polygonPlacemark = this.parent.ge.createPlacemark('');
            polygonPlacemark.setStyleSelector(this.parent.ge.createStyle(''));
            var lineStyle = polygonPlacemark.getStyleSelector().getLineStyle();
            var polyStyle = polygonPlacemark.getStyleSelector().getPolyStyle();
            lineStyle.setWidth(2);
            lineStyle.getColor().set('990000ff');
            polyStyle.getColor().set('330000ff');
            polyStyle.setFill(1);

            var polygon = this.parent.ge.createPolygon('');
            polygonPlacemark.setGeometry(polygon);
            var polygon2 = this.parent.ge.createPolygon('');
            polygonPlacemark.setGeometry(polygon);
            var polygon3 = this.parent.ge.createPolygon('');
            polygonPlacemark.setGeometry(polygon);

            ge.getFeatures().appendChild(polygonPlacemark);

            this.triangle_guides[0] = polygon;
            this.triangle_guides[1] = polygon2;
            this.triangle_guides[2] = polygon3;
        }
    };

    this.get_flight_type = function () {
        if (this.count === 4 && this.is_equal(0, 3)) {
            if (this.min_leg() > 0.28 * this.get_total_distance()) {
                return 'tr';
            } else {
                return 'ftr';
            }
        }
        if (this.is_equal(0, 2)) { return 'or';}
        if ((this.count >= 2 && this.count <= 5)) { return 'od';}
    };

    this.min_leg = function () {
        var min = this.distance_array[1];
        this.distance_array.each(function (distance) {
            if (distance < min && distance) {
                min = distance;
            }
        });
        return min;
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
        if (this.parent.isMap()) {
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
        var type = this.get_flight_type();
        if (type == 'tr' || type == 'ftr') {
            this.draw_triangle_guides();
        } else {
            this.hide_triangle_guides();
        }
    };

    this.hide_triangle_guides = function () {
        if (this.parent.isMap()) {
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
        if (this.parent.isMap()) {
            this.triangle_guides[0].setPath(yessan([point1, point2, point3]));
            this.triangle_guides[1].setPath(yessan([point2, point3, point1]));
            this.triangle_guides[2].setPath(yessan([point3, point1, point2]));
            this.triangle_guides[0].setMap(this.parent.internal_map);
            this.triangle_guides[1].setMap(this.parent.internal_map);
            this.triangle_guides[2].setMap(this.parent.internal_map);
        } else {
            var t = function (points, polygon) {
                polygon.getCoordinates().clear();
                var inner = this.parent.ge.createLinearRing('');
                points.each(function (a) {
                    inner.getCoordinates().pushLatLngAlt(a.lat(), a.lng(), 0);
                });
                polygon.setOuterBoundary(inner);

            };
            var points = yessan([point1, point2, point3]);
            t(points, this.triangle_guides[0]);
            points = yessan([point2, point3, point1]);
            t(points, this.triangle_guides[1]);
            points = yessan([point3, point1, point2]);
            t(points, this.triangle_guides[2]);

        }
    };

    this.clear = function () {

        this.waypoints.each(function (point, count, ths) {
            if (ths.parent.isMap()) {
                point.setMap(null);
            } else {
                ths.parent.ge.getFeatures().removeChild(point);
            }
        }, this);
        this.waypoints = [];
        this.coordinates = [];
        this.count = 0;
        this.enabled = false;
        $("body").removeClass('waypoint_mode');
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
        if (this.parent.isEarth()) {
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
        return marker;
    };

    this.load_string = function(string) {
        this.enable();
        var parts = string.split(';');
        parts.each(function(part, count, ths) {
            var sub = part.split(',');
            if(sub.length > 1) {
                var marker = ths.addWaypoint(sub[0], sub[1]);
                new google.maps.event.trigger(marker, 'click');
            }
        }, this);
        this.draw_triangle_guides();
        map.center(this.coordinates);
    };

    this.add_point_full = function(lat, lng) {
        this.enable();
        var marker = this.addWaypoint(lat, lng);
        new google.maps.event.trigger( marker, 'click' );
    }
}