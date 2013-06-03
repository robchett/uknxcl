recreate_checkboxes = function () {
    $(".checkbox_replace").each(function () {
        var $this = $(this);
        var $input = $this.children("input");
        if ($input.prop('checked')) {
            $this.addClass("checked");
        }
    });
    $(".checkbox_replace").click(function () {
        var $this = $(this);
        var $input = $this.children("input");
        if (!$input.prop('checked')) {
            $this.addClass("checked");
            $input.prop('checked', true);
        } else {
            $this.removeClass("checked");
            $input.prop('checked', false);
        }
    });
}
$(document).ready(function () {
    recreate_checkboxes();
    $.fn.ajax_factory.defaults.complete = function () {
        recreate_checkboxes();
    };
});