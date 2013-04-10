function google() {
    this.maps = function () {

        this.UNITS_FRACTION = 1;
        this.controls = [];
        this.LatLng = function (lat, lng) {
            this.lat = function () {};
            this.lng = function () {};
        }
        this.MapTypeId = {
            TERRAIN: 1,
        }
        this.ControlPosition = {
            TOP_RIGHT: 1
        }
        this.Map = function (node, options) {};
        this.event = function () {
            this.addListener = function (map, event, callback) {};
            this.latLng = new google.maps.LatLng();
        };
        this.Circle = function (options) {

        };
        this.fitBounds = function (bounds) {

        }
        this.geometry = function () {
            this.encoding = function () {
                this.decodePath = function (points) {

                }
            }
        }
        this.Marker = function (options) {}
    }
    this.earth = function () {
        this.addEventListener = function (map, event, callback) {};
        this.latLng = new google.maps.LatLng();
        this.getFeatures = function () {};
        this.createInstance = new google.earth(id, callback, failureCallback);
        this.createIcon = new google.earth.icon(str);
        this.createPlacemark = new google.earth.placemark(str);
        this.createPoint = new google.earth.point(str);
        this.createStyle = new google.earth.style(str);
        this.point = function () {
            this.setLatitude = function (lat) {};
            this.setLongitude = function (lon) {};
            this.getLatitude = function () {};
            this.getLongitude = function () {};
        }
        this.placemark = function () {
            this.setGeometry = function (geom) {};
            this.setStyleSelector = function (style) {};
        }
        this.icon = function () {
            this.setHref = function () {};
        }
        this.style = function () {
            this.setHref = function () {};
            this.getIconStyle = function () {return new google.earth.iconStyle()};
        }
        this.iconStyle = function () {
            this.setIcon = function (icon) {};
            this.getHotSpot = function () {};
        }

    }
}