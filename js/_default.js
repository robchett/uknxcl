$(document).ready(function () {
    $('body').on('click', 'a', function (e) {
        if (typeof $(this).data('page-post') != 'undefined') {
            e.preventDefault();
            page($(this).attr('href'), $(this).data('page-post'));
        }
    });

    $.fn.ajax_factory.defaults.complete.push('page_callback');
    $.fn.ajax_factory.defaults.load_pages_ajax = true;
});

function page_callback(json) {
    if (json && json.push_state) {
        toggle_page($(json.push_state.data.id));
    }
};

function toggle_page($page) {
    if ($page.css('z-index') != 2) {
        var $main = $('#main');
        $page.hide();
        $main.stop(true, true).addClass('flipped');
        $children = $main.children('div');
        setTimeout(function () {
            $children.hide();
            $page.show();
            $main.removeClass('flipped');
        }, 600);

        $("a").removeClass('sel').parent('li').removeClass('sel');
        var $links = $('a[href="' + $page.data('url') + '"]');
        $links.addClass('sel').parent('li').addClass('sel');
        $main.scrollTop(0);
    }
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
    post.is_popped = is_popped || 0;
    var $page = $("div[data-url='" + url + "']");
    if ($page.length) {
        if (!is_popped) {
            window.history.pushState(post, '', url);
        }
        toggle_page($page);
    } else {
        delete post.module;
        delete post.act;
        post.url = url;
        $.fn.ajax_factory(module, act, post);
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