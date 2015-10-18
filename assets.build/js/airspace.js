function Airspace() {
    this.visible = {
        'PROHIBITED': false,
        'RESTRICTED': false,
        'DANGER': false,
        'OTHER': false,
        'CTRCTA': false,
        'ALL': false
    };
    this.loaded = {
        'PROHIBITED': false,
        'RESTRICTED': false,
        'DANGER': false,
        'OTHER': false,
        'CTRCTA': false,
        'ALL': false
    };
    this._airspace = [];
    this.varyWithTrack = false;
    this.maximum_base = 7500;
    this.enabled = false;

    this.isLoaded = function (type) {
        return this.loaded[type];
    };

    this.setLoaded = function (type, bool) {
        this.loaded[type] = bool;
    };

    this.isVisible = function (type) {
        if (!type) {
            var no_visibles = true;
            for (type in this.visible) {
                if (this.visible.hasOwnProperty(type) && this.isVisible(type)) {
                    no_visibles = false;
                }
            }
            return !no_visibles;
        }
        return this.visible[type];
    };

    this.setVisible = function (type, bool) {
        if (!type) {
            for (type in this.visible) {
                if (this.visible.hasOwnProperty(type)) {
                    this.setVisible(type, bool);
                }
            }
            if (bool) {
                $('#airspace_tree .all').addClass('visible');
            } else {
                $('#airspace_tree .all').removeClass('visible');
            }
        } else {
            if (bool && !this.isLoaded(type)) {
                this.load(type);
            }
            this.visible[type] = bool;
            if (bool) {
                $('#airspace_tree #' + type).addClass('visible');
            } else {
                $('#airspace_tree #' + type).removeClass('visible');
            }
        }
    };

    this.getType = function (int) {
        switch (int) {
            case 0:
                return 'PROHIBITED';
            case 1:
                return 'RESTRICTED';
            case 2:
                return 'DANGER';
            case 3:
                return 'OTHER';
            case 4:
                return 'CTRCTA';
        }
        return false;
    };

    this.setHeight = function (val) {
        this.maximum_base = val;
    };

    this.loadAll = function (bool) {
        this.load('PROHIBITED');
        this.load('RESTRICTED');
        this.load('OTHER');
        this.load('DANGER');
        this.load('CTRCTA');
        this.visible = {
            'PROHIBITED': bool,
            'RESTRICTED': bool,
            'DANGER': bool,
            'OTHER': bool,
            'CTRCTA': bool,
            'ALL': bool
        };
    };

    var as = [];
    this.reload = function (currentHeight) {
        this._airspace.each(function (airspace, i, ths) {
            if (ths.varyWithTrack && currentHeight !== undefined) {
                if (airspace.level >= currentHeight / 0.3048 || airspace.top <= currentHeight / 0.3048) {
                    airspace.poly.setMap(null);
                    airspace.visible = false;
                } else if (airspace.level >= ths.maximum_base) {
                    airspace.poly.setMap(null);
                    airspace.visible = false;
                }
                return;
            }
            var c = airspace._class;
            if (!ths.isVisible(c) || airspace.level >= ths.maximum_base) {
                if (airspace.visible) {
                    airspace.poly.setMap(null);
                    airspace.visible = false;
                }
            } else if (!airspace.visible) {
                airspace.poly.setMap(map.internal_map);
                airspace.visible = true;
            }
        }, this);
    };

    this.toggle = function (type) {
        type = this.getType(type);
        this.setVisible(type, !this.isVisible(type));
        this.reload();
    }

    this.add = function (airClass, flightLevel, top, points, strokeWeight, strokeColour, strokeOpacity, fillColour, fillOpacity, name) {
        var polygon = new google.maps.Polygon({
            strokeColor: strokeColour,
            strokeWeight: strokeWeight,
            clickable: true,
            strokeOpacity: strokeOpacity,
            path: google.maps.geometry.encoding.decodePath(points),
            fillColor: fillColour,
            fillOpacity: fillOpacity,
            zIndex: (185 - flightLevel),
            title: name});
        this._airspace.push({poly: polygon, _class: airClass, level: flightLevel, top: top, visible: false});
    }
}