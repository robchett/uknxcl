$(document).ready(function () {
    $('body').on('click', null, function (event) {
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
            }
        }
    });
    $('body').on('change', ':input', function (event) {
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
            }
        }
    });
})

$.fn.ajax_factory = function (module, act, post, options) {
    var options = options || {};
    var post = post || {};
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
        url: '/',
        global: false,
        async: true,
        type: 'POST',
        dataType: 'json',
        cache: false,
        data: post,
        success: handle_json_response
    });
}
function colorbox_recenter(){
    $('#colorbox').stop().animate({left:(725-$('#colorbox').width())/2});
}

$('body').on('submit', 'form.ajax', function (e) {
    e.preventDefault();
    var arr = $(this).attr('action').split(':');
    var module = arr[0];
    var act = arr[1];
    var data = {};
    var ajax_shroud = $(this).attr('data-ajax-shroud');

    $(this).find(':input').each(function () {
        var name = $(this).attr('name');
        if ($(this).attr('type') == 'checkbox') {
            if ($(this).is(':checked'))
                data[name] = $(this).val();
        } else
            data[name] = $(this).val();
    });

    var options = {loading_target: ajax_shroud};
    data.ajax_origin = $(event.target)[0].id;


    $.fn.ajax_factory(module, act, data, options);
    return false;
});
$('body').on('submit', 'form.noajax', function (e) {
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

function handle_json_response(json) {
    $('.loading_shroud').remove();
    for (i in json.update) {
        var upd = json.update[i];
        $(upd.id).html(upd.html);
    }
    for (i in json.inject) {
        var inj = json.inject[i];
        if (inj.over != '')
            $(inj.over).remove();
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
    }
    if (typeof json.push_state != "undefined")
        window.history.pushState(json.push_state.data, json.push_state.title, json.push_state.url);
}