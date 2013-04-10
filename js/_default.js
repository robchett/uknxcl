var Airspacetoggle = false;
var sidebarvisible = true;
var mapvisible = false;
var tablevisible = true;
var ge;

$(document).ready(function () {
    $('#nav ul li a').click(function (e) {
        e.preventDefault();
        page($(this).attr('href'), $(this).data('page-post'));
    });
});

window.onresize = function () {
    if (map)map.resize()
};

function colorbox_recenter(){
    $('#colorbox').stop().animate({left:(725-$('#colorbox').width())/2});
}

function showhide(element, el_to_hide) {
    $(element).toggle();
    if (el_to_hide){
        $(el_to_hide).hide();
    }
}

function startUpload(a) {
    if (!a) a = "main";
    $(document).trigger('hideCluetip');
    a = '#' + a;
    $(a).html('<div id="loadingImage"></div>');
    return true;
}

function stopUpload(success, a) {
    $('#console').html(success);
    return true;
}

$("body").on('click','.form_toggle', function() {
    $("#basic_tables_form_wrapper").hide();
    $("#advanced_tables_wrapper").hide();
    $('#' + $(this).data("show")).show();
});

function showChildren(ref, level) {
    if (level == 1) {
        $('.comp_super').children().hide();
        $('.comp_inner').children().hide();
    }
    if (level == 2) {
        $('.comp_inner').children().hide();
    }
    $(ref).children().toggle();
}


function page(a,post, is_push) {
    var module = post.module;
    is_push = is_push || 0;
    $('#main > div').hide();
    $('#nav ul li').removeClass('s');
    if (!loaded_modules[a]) {
        delete post.module;
        $.fn.ajax_factory(module,'ajax_load', post);
        loaded_modules[a] = true;
    } else if (!is_push) {
        window.history.pushState({page: {url: a}}, '', a)
    }
    if(post.page) {
        var page = '-' + post.page;
    } else {
        var page = '';
    }
    $('#nav-' + module + page ).attr('class', 's');
    $('#' + module + page ).show();
}
function reloadLatest() {
    $.fn.ajax_factory('latest', 'ajax_load');
}

function updatePilots() {
    $.fn.ajax_factory('pilot', 'do_update_selector');
}

function updateGliders() {
    $.fn.ajax_factory('glider', 'do_update_selector');
}

function console_log(string, target) {
    $('#console').append("<br/><a class='console'>" + string + "</a>");
}

$('body').on('change', 'input[name=flights]', function () {
    map.swap(map.kmls[$(this).val()]);
});


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
}

window.onpopstate = function (data) {
    if (data.state !== undefined && data.state !== null) {
        if ('page' in data.state) {
            page(data.state.page.url, 1);
        }
    }
};