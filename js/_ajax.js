$(document).ready(function () {

    function get_form_data($this) {
        var data = {}
        $this.find(':input').each(function () {
            var name = $(this).attr('name');
            if ($(this).attr('type') == 'checkbox') {
                if ($(this).is(':checked'))
                    data[name] = $(this).val();
            } else
                data[name] = $(this).val();
        });
        return data;
    }

    var $body = $('body');
    $body.on('click', null, function (event) {
        var $target = $(event.target);
        if (!$target.attr('disabled') && !$target.hasClass('disabled')) {
            if ($target.attr('data-ajax-click')) {
                event.preventDefault();
                var arr = $target.data('ajax-click').split(':');
                var module = arr[0];
                var act = arr[1];
                var options = {};
                if ($target.data('ajax-shroud')) {
                    options.loading_target = $target.data('ajax-shroud');
                }
                var data = $target.data('ajax-post') || {};
                data['origin'] = $target.attr('id');
                $.fn.ajax_factory(module, act, data, options);
            } else if ($target.is('a') && $.fn.ajax_factory.defaults.load_pages_ajax) {
                var href = $target.attr('href');
                var rel = $target.attr('rel');
                if (typeof href != "undefined" && href != '#' && (typeof rel == 'undefined' || rel != 'external')) {
                    if (!href.match('http')) {
                        var post = {module: 'core', act: 'load_page'};
                        var options = {call_as_uri: href, loading_target: '#main' };
                        event.preventDefault();
                        var $page = $("div[data-url='" + href + "']");
                        if ($page.length) {
                            window.history.pushState(post, '', href);
                            toggle_page($page);
                        } else {
                            $.fn.ajax_factory('core', 'load_page', post, options);
                        }
                    }
                }
            }
        }
    });
    $body.on('change', ':input', function (event) {
        var $target = $(event.target);
        if (!$target.attr('disabled') && !$target.hasClass('disabled')) {
            if ($target.attr('data-ajax-change')) {
                var options = {};
                event.preventDefault();
                var arr = $target.attr('data-ajax-change').split(':');
                var module = arr[0];
                var act = arr[1];
                var data = eval('(' + $target.attr('data-ajax-post') + ')') || {};
                if ($target.attr('type') === 'checkbox') {
                    data.value = ($target.is(':checked') ? 1 : 0 );
                } else {
                    data.value = $target.val();
                }
                data['origin'] = $target.attr('id');
                if ($target.data('ajax-shroud')) {
                    options.loading_target = $target.data('ajax-shroud');
                }
                $.fn.ajax_factory(module, act, data, options);
            } else {
                var $parent = $target.parents('form').eq(0);
                if ($parent.data('ajax-change')) {
                    var arr = $parent.attr('data-ajax-change').split(':');
                    var module = arr[0];
                    var act = arr[1];
                    var ajax_shroud = $parent.attr('data-ajax-shroud');
                    var data = get_form_data($parent);
                    var options = {loading_target: ajax_shroud};
                    data.ajax_origin = $parent.id;
                    $.fn.ajax_factory(module, act, data, options);
                    return false;
                }
            }
        }
    });

    $body.on('submit', 'form.ajax', function (e) {
        e.preventDefault();
        var arr = $(this).attr('action').split(':');
        var module = arr[0];
        var act = arr[1];
        var ajax_shroud = $(this).attr('data-ajax-shroud');
        var data = get_form_data($(this));
        var options = {loading_target: ajax_shroud};
        data.ajax_origin = $(e.target)[0].id;

        $.fn.ajax_factory(module, act, data, options);
        return false;
    });
    $body.on('submit', 'form.noajax', function () {
        var ajax_shroud = $(this).attr('data-ajax-shroud');
        if (typeof ajax_shroud != 'undefined') {
            var div = document.createElement('div');
            div.className = 'loading_shroud';
            div.style.width = $(ajax_shroud).outerWidth() + 'px';
            div.style.height = $(ajax_shroud).outerHeight() + 'px';
            div.style.left = 0;
            div.style.top = 0;
            if ($(ajax_shroud).css('position') != 'absolute' || $(ajax_shroud).css('position') != 'relative') {
                $(ajax_shroud).css({'position': 'relative'});
            }
            $(ajax_shroud).prepend(div);
        }
    });
    $.fn.ajax_factory = function (module, act, post, options) {
        options = options || {};
        post = post || {};
        if (typeof (options.loading_target) !== 'undefined') {
            var div = document.createElement('div');
            div.className = 'loading_shroud';
            div.style.width = $(options.loading_target).outerWidth() + 'px';
            div.style.height = $(options.loading_target).outerHeight() + 'px';
            div.style.left = 0;
            div.style.top = 0;
            if ($(options.loading_target).css('position') != 'absolute' || $(options.loading_target).css('position') != 'relative') {
                $(options.loading_target).css({'position': 'relative'});
            }
            $(options.loading_target).prepend(div);
            $.fn.colorbox.resize();
            colorbox_recenter();
        }
        $.extend(post, ({'module': module, 'act': act}));
        $(".error_message").remove();
        $.ajax({
            url: options.call_as_uri || '/',
            global: false,
            async: true,
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: post,
            success: handle_json_response
        });
    }
    $.fn.ajax_factory.defaults = {
        complete: [],
        load_pages_ajax: false
    };
});

function colorbox_recenter() {
    var $cb = $('#colorbox');
    $cb.stop().animate({left: (725 - $cb.width()) / 2});
}

function handle_json_response(json) {
    $('.loading_shroud').remove();
    json.update.each(function (upd) {
        $(upd.id).html(upd.html);
    });
    json.inject.each(function (inj) {
        if (inj.over != '') {
            $(inj.over).remove();
        }
        switch (inj.pos) {
            case 'append':
                $(inj.id).append(inj.html);
                break;
            case 'prepend':
                $(inj.id).prepend(inj.html);
                break;
            case 'before':
                $(inj.id).before(inj.html);
                break;
            case 'after':
                $(inj.id).after(inj.html);
                break;
        }
    })
    if (typeof json.push_state != "undefined") {
        if (json.push_state.push) {
            window.history.pushState(json.push_state.data, json.push_state.title, json.push_state.url);
        } else if (json.push_state.replace) {
            window.history.replaceState(json.push_state.data, json.push_state.title, json.push_state.url);
        }
    }
    if ($.fn.ajax_factory.defaults.complete) {
        $.fn.ajax_factory.defaults.complete.each(function (method, i, json) {
            window[method](json);
        }, json);
    }
}

Array.prototype.each = function (callback, context) {
    for (var i = 0; i < this.length; i++) {
        callback(this[i], i, context);
    }
}
Array.prototype.count = function () {
    return this.length - 2;
}
String.prototype.isNumber = function () {
    return !isNaN(parseFloat(this)) && isFinite(this);
};

