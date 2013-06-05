$(document).ready(function () {
    $('#nav').find('ul li a').click(function (e) {
        e.preventDefault();
        page($(this).attr('href'), $(this).data('page-post'));
    });
});

window.onresize = function () {
    if (map)map.resize()
};

function startUpload(a) {
    if (!a) a = "main";
    $(document).trigger('hideCluetip');
    a = '#' + a;
    $(a).html('<div id="loadingImage"></div>');
    return true;
}

function page(a, post, is_push) {
    var module = post.module;
    is_push = is_push || 0;
    $('#main').children('div').hide();
    $('#nav').find('ul li').removeClass('s');
    if (!loaded_modules[a]) {
        delete post.module;
        $.fn.ajax_factory(module, 'ajax_load', post);
        loaded_modules[a] = true;
    } else if (!is_push) {
        window.history.pushState({page: {url: a}, post: post}, '', a)
    }
    if (post.page) {
        var page = '-' + post.page;
    } else {
        page = '';
    }
    $('#nav-' + module + page).attr('class', 's');
    $('#' + module + page).show();
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
};

window.onpopstate = function(event) {
    if(event && event.state) {
        if ('page' in event.state) {
            page(event.state.page.url, event.state.post, 1);
        }
    }
};