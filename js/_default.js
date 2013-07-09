$(document).ready(function () {
    $('body').on('click','a', function(e) {
        if(typeof $(this).data('page-post') != 'undefined') {
            e.preventDefault();
            page($(this).attr('href'), $(this).data('page-post'));
        }
    });

    $.fn.ajax_factory.defaults.complete.push('page_callback');
});

function page_callback(json) {
    if(json && json.push_state) {
      toggle_page(json.push_state.data);
    }
};

function toggle_page(data) {
    $('#main').children('div').hide();
    $(data.id).show();

    $("a").removeClass('sel').parent('li').removeClass('sel');
    var $links = $('a[href="' + data.url + '"]');
    $links.addClass('sel').parent('li').addClass('sel');
}


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

function page(url, post, is_popped) {
    var module = post.module;
    var act = post.act;
    is_popped = is_popped || 0;
    if (!is_popped) {
        delete post.module;
        delete post.act;
        $.fn.ajax_factory(module, act, post);
        //loaded_modules[url] = true;
    } else {
        toggle_page(post)
    }
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

window.onpopstate = function (event) {
    if (event && event.state) {
        page(event.state.url, event.state, 1);
    }
};