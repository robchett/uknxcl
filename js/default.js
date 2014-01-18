var map;
var main_scrollpane;
var throttleTimeout;
var $body;

$(document).ready(function () {
    if (typeof google != 'undefined') {
        map = new UKNXCL_Map($("#map_wrapper"));
        map.load_map();
    } else {
        $('#map').children('p.loading').html('Failed to load Google resources');
    }

    main_scrollpane = $("#main_wrapper").jScrollPane().data('jsp');
    page_handeler.defaults.complete.push('reload_scrollpane')

    $body = $("body");


    $body.on('change', 'input[name=flights]', function () {
        map.swap(map.kmls[$(this).val()]);
    });

    $body.on('click', '.kmltree .toggler', function (event) {
        kmlPath = new UKNXCL_Map.KmlPath(event, $(this));
        if (kmlPath.load()) {
            kmlPath.toggle();
        }
    });

    $body.on('click', '.kmltree .expander', function () {
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
        var $cb = $('#colorbox');
        var width = $cb.width();
        if (width < 725) {
            $cb.animate({left: (725 - width) / 2});
        } else {
            $cb.animate({left: 0});
        }
    });
});

function reload_scrollpane() {
    if (main_scrollpane) {
        main_scrollpane.reinitialise();
    }
}

window.onresize = function () {
    if (!throttleTimeout) {
        throttleTimeout = setTimeout(function () {
            if (map) {
                map.resize();
            }
            reload_scrollpane();
            throttleTimeout = null;
        }, 50);
    }
};

Number.prototype.toRad = function () {  // convert degrees to radians
    return this * Math.PI / 180;
};
Number.prototype.toDeg = function () {  // convert radians to degrees (signed)
    return this * 180 / Math.PI;
};
Number.prototype.padLz = function (w) {
    var n = this.toString();
    var l = n.length;
    for (var i = 0; i < w - l; i++) n = '0' + n;
    return n;
};
Number.prototype.round = function (dp) {
    return Math.floor(this * Math.pow(10, dp)) / Math.pow(10, dp);
};