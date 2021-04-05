var map;
var main_scrollpane;
var throttleTimeout;
var $body;
var load_callback = new LoadCallback(load_callback || []);
$(document).ready(function () {
    $body = $("body");
    load_callback.trigger();
    map = new UKNXCL_Map($("#map_wrapper"), map);
    if (typeof google != 'undefined') {
        map.load_map();
    } else {
        $('#map').children('p.loading').html('Google maps are unavailable');
        $("#map_interface_3d span.toggle").hide();
        $("#map_interface").hide();
    }

    map.resize();

    reload_scrollpane();
    page_handeler.defaults.complete.push('reload_scrollpane')

    $.fn.ajax_factory.defaults.complete.push('center_colorbox');


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
        var $li = $(this).parents('kmltree-item').eq(0);
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
    //if ($body.width() < 750) {
    //    if (main_scrollpane) {
    //        main_scrollpane.destroy();
    //    }
    //} else if (main_scrollpane) {
    //    main_scrollpane.reinitialise();
    //} else {
    //    main_scrollpane = $("#main_wrapper").jScrollPane().data('jsp');
    //}
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
        $page.css({left:800, top:10, position: "absolute"});
        $main.stop(true, true).animate({left: -800}, 200, 'linear', function () {
            var $children = $main.children('div');
            $children.addClass('remove');
            $page.show().removeClass('remove');
            $main.children('div.remove').remove();
            $main.css({left:0});
            $page.css({left:0, position:"relative", top: 0});
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

/* ===========================================================
 * Bootstrap: fileinput.js v3.1.3
 * http://jasny.github.com/bootstrap/javascript/#fileinput
 * ===========================================================
 * Copyright 2012-2014 Arnold Daniels
 *
 * Licensed under the Apache License, Version 2.0 (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

+function ($) {

    var isIE = window.navigator.appName == 'Microsoft Internet Explorer'

    // FILEUPLOAD PUBLIC CLASS DEFINITION
    // =================================

    var Fileinput = function (element, options) {
        this.$element = $(element)

        this.$input = this.$element.find(':file')
        if (this.$input.length === 0) return

        this.name = this.$input.attr('name') || options.name

        this.$hidden = this.$element.find('input[type=hidden][name="' + this.name + '"]')
        if (this.$hidden.length === 0) {
            this.$hidden = $('<input type="hidden">').insertBefore(this.$input)
        }

        this.$preview = this.$element.find('.fileinput-preview')
        var height = this.$preview.css('height')
        if (this.$preview.css('display') !== 'inline' && height !== '0px' && height !== 'none') {
            this.$preview.css('line-height', height)
        }

        this.original = {
            exists: this.$element.hasClass('fileinput-exists'),
            preview: this.$preview.html(),
            hiddenVal: this.$hidden.val()
        }

        this.listen()
    }

    Fileinput.prototype.listen = function() {
        this.$input.on('change.bs.fileinput', $.proxy(this.change, this))
        $(this.$input[0].form).on('reset.bs.fileinput', $.proxy(this.reset, this))

        this.$element.find('[data-trigger="fileinput"]').on('click.bs.fileinput', $.proxy(this.trigger, this))
        this.$element.find('[data-dismiss="fileinput"]').on('click.bs.fileinput', $.proxy(this.clear, this))
    },

        Fileinput.prototype.change = function(e) {
            var files = e.target.files === undefined ? (e.target && e.target.value ? [{ name: e.target.value.replace(/^.+\\/, '')}] : []) : e.target.files

            e.stopPropagation()

            if (files.length === 0) {
                this.clear()
                return
            }

            this.$hidden.val('')
            this.$hidden.attr('name', '')
            this.$input.attr('name', this.name)

            var file = files[0]

            if (this.$preview.length > 0 && (typeof file.type !== "undefined" ? file.type.match(/^image\/(gif|png|jpeg)$/) : file.name.match(/\.(gif|png|jpe?g)$/i)) && typeof FileReader !== "undefined") {
                var reader = new FileReader()
                var preview = this.$preview
                var element = this.$element

                reader.onload = function(re) {
                    var $img = $('<img>')
                    $img[0].src = re.target.result
                    files[0].result = re.target.result

                    element.find('.fileinput-filename').text(file.name)

                    // if parent has max-height, using `(max-)height: 100%` on child doesn't take padding and border into account
                    if (preview.css('max-height') != 'none') $img.css('max-height', parseInt(preview.css('max-height'), 10) - parseInt(preview.css('padding-top'), 10) - parseInt(preview.css('padding-bottom'), 10)  - parseInt(preview.css('border-top'), 10) - parseInt(preview.css('border-bottom'), 10))

                    preview.html($img)
                    element.addClass('fileinput-exists').removeClass('fileinput-new')

                    element.trigger('change.bs.fileinput', files)
                }

                reader.readAsDataURL(file)
            } else {
                this.$element.find('.fileinput-filename').text(file.name)
                this.$preview.text(file.name)

                this.$element.addClass('fileinput-exists').removeClass('fileinput-new')

                this.$element.trigger('change.bs.fileinput')
            }
        },

        Fileinput.prototype.clear = function(e) {
            if (e) e.preventDefault()

            this.$hidden.val('')
            this.$hidden.attr('name', this.name)
            this.$input.attr('name', '')

            //ie8+ doesn't support changing the value of input with type=file so clone instead
            if (isIE) {
                var inputClone = this.$input.clone(true);
                this.$input.after(inputClone);
                this.$input.remove();
                this.$input = inputClone;
            } else {
                this.$input.val('')
            }

            this.$preview.html('')
            this.$element.find('.fileinput-filename').text('')
            this.$element.addClass('fileinput-new').removeClass('fileinput-exists')

            if (e !== undefined) {
                this.$input.trigger('change')
                this.$element.trigger('clear.bs.fileinput')
            }
        },

        Fileinput.prototype.reset = function() {
            this.clear()

            this.$hidden.val(this.original.hiddenVal)
            this.$preview.html(this.original.preview)
            this.$element.find('.fileinput-filename').text('')

            if (this.original.exists) this.$element.addClass('fileinput-exists').removeClass('fileinput-new')
            else this.$element.addClass('fileinput-new').removeClass('fileinput-exists')

            this.$element.trigger('reset.bs.fileinput')
        },

        Fileinput.prototype.trigger = function(e) {
            this.$input.trigger('click')
            e.preventDefault()
        }


    // FILEUPLOAD PLUGIN DEFINITION
    // ===========================

    var old = $.fn.fileinput

    $.fn.fileinput = function (options) {
        return this.each(function () {
            var $this = $(this),
                data = $this.data('bs.fileinput')
            if (!data) $this.data('bs.fileinput', (data = new Fileinput(this, options)))
            if (typeof options == 'string') data[options]()
        })
    }

    $.fn.fileinput.Constructor = Fileinput


    // FILEINPUT NO CONFLICT
    // ====================

    $.fn.fileinput.noConflict = function () {
        $.fn.fileinput = old
        return this
    }


    // FILEUPLOAD DATA-API
    // ==================

    $(document).on('click.fileinput.data-api', '[data-provides="fileinput"]', function (e) {
        var $this = $(this)
        if ($this.data('bs.fileinput')) return
        $this.fileinput($this.data())

        var $target = $(e.target).closest('[data-dismiss="fileinput"],[data-trigger="fileinput"]');
        if ($target.length > 0) {
            e.preventDefault()
            $target.trigger('click.bs.fileinput')
        }
    })

}(window.jQuery);