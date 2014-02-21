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
    $.fn.ajax_factory.defaults.complete.push('center_colorbox');

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

    $(document).bind('cbox_complete', 'center_colorbox');
});

function center_colorbox() {
    $.fn.colorbox.resize();
    var $cb = $('#colorbox');
    var width = $cb.width();
    if (width < 725) {
        $cb.animate({left: (725 - width) / 2});
    } else {
        $cb.animate({left: 0});
    }
}

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
            if ($('#colorbox').width()) {
                center_colorbox();
            }
            reload_scrollpane();
            throttleTimeout = null;
        }, 50);
    }
};

page_handeler.toggle_page = function ($page) {
    if ($page.css('z-index') != 2) {
        var $main = $('#main');
        $page.css({left:800, top:0, position: "absolute"});
        $main.stop(true, true).animate({left: -800}, 200, 'linear', function () {
            var $children = $main.children('div');
            $children.hide();
            $page.show();
            $main.css({left:0});
            $page.css({left:0, position:"relative"});
            $("a").removeClass('sel').parent('li').removeClass('sel');
            var $links = $('a[href="' + $page.data('url') + '"]');
            $links.addClass('sel').parent('li').addClass('sel');
            $main.animate({scrollTo: 0});
            if (page_handeler.defaults.complete) {
                page_handeler.defaults.complete.each(function (method) {
                    if (typeof method == 'function') {
                        method();
                    } else {
                        window[method]();
                    }
                });
            }
        });
    }
};