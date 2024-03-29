<?php

namespace module\add_flight\view;

use classes\module;
use template\html;
use classes\attribute_callable;
use classes\get;
use core;
use module\add_flight\form\igc_form;
use module\add_flight\form\igc_upload_form;

/** @extends html<\module\add_flight\controller, false> */
class _default extends html {

    function get_view(): string {
        $form1 = new igc_form();
        $form2 = new igc_upload_form();
        $hash =  attribute_callable::create([igc_upload_form::class, 'reset']);
        core::$inline_script[] = <<<JS
var \$body = $("body");
    \$body.on("change.bs.fileinput,clear.bs.fileinput,reset.bs.fileinput", ".fileinput", function (e) {
        var \$ths = $(this);
        var \$input = \$ths.find("input[type=file]");
        var \$kml = $("#kml_calc");
        var \$kml_wrapper = $("#kml_wrapper");
        if (\$input.val().toLowerCase().slice(-3) == "igc") {
            \$kml.fadeIn(1000).html("<div id=\'console\'><a class=\'calc\'>Calculate</a></div>");
            \$kml_wrapper.find("a").show();
            \$kml_wrapper.addClass("pass").removeClass("hoverFail");
            \$kml_wrapper.find("p.text").html("Click calculate to continue or choose another file");
        } else {
            \$kml_wrapper.find("p.text").html("Please use an IGC file");
            \$kml_wrapper.addClass("hoverFail");
            \$kml.hide();
        }
    });
    \$body.on("change", "input, select, textarea", function (e) {
        $("#" + $(this).attr("id") + "_hidden").val($(this).val());
        return true;
    });
    \$body.on("click", "#kml_calc a.calc", function () {
        $("#igc_upload_form").submit();
        $("#kml_wrapper").removeClass("pass");
    });
    \$body.on("change","input#coords",function () {
        var arr = $(this).val().split(";");
        var coord_array = new Planner();
        var str = [];
        arr.each(function(arg, i) {
            var coord = new Coordinate();
            coord.set_from_OS(arg);
            if (coord.is_valid_gridref()) {
                coord_array.push(coord);
            } else {
                str.push("Coordinate " + (parseInt(i) + 1) + " is not valid");
            }
        });
        if (!str.length) {
        switch (coord_array.count) {
            case 0:
                str = [""];
                break;
            case 1:
                str = ["Please enter at least two coordinates"];
                break;
            case 2:
                str = ["Flight Type: Open Distance, Score: " + coord_array.get_total_distance().round(2) + " before multipliers"];
                break;
            case 3:
                if(coord_array[0] == coord_array[2]) {
                    str = ["Flight Type: Out & Return, Score: " + coord_array.get_total_distance().round(2) + " before multipliers"];
                } else {
                    str = ["Flight Type: Open Distance, Score: " + coord_array.get_total_distance().round(2) + " before multipliers"];
                }
                break;
            case 4:
                if(coord_array[0] == coord_array[4]) {
                    str = ["Flight Type: Triangle, Score: " + coord_array.get_total_distance().round(2) + " before multipliers"];
                } else {
                    str = ["Flight Type: Open Distance, Score: " + coord_array.get_total_distance().round(2) + " before multipliers"];
                }
                break;
            case 5:
                str = ["Flight Type: Open Distance, Score: " + coord_array.get_total_distance().round(2) + " before multipliers"];
                break;
            default :
                str = ["you can only enter 5 coordinates"];
            }
        }
        $(this).parents("form").find(".defined_info").html(str.join("<br/>"));
    });
    \$body.on("click","a.score_select",function () {
        var data = $(this).data("post");
        $("#temp_id").val(data.track);
        $("#type").val(data.type);
        $("#igc_upload_form").html("<p class='restart'>Your flight details have been saved, please complete the form below, 'Additional Details', to finalise your sumbission.<br/><a data-ajaxclick='$hash' href='#' class='button'>Restart</a></p>");
        $("#igc_form ").find("input.submit").removeAttr("disabled");
    });
JS;

        return "
<div class='editable_content'>
    {$this->module->page_object->body}
</div>
<div class='add_flight_section upload'>
    <div class='callout callout-primary'>
        {$form2->get_html()}
    </div>
    <div class='callout callout-primary'>
        {$form1->get_html()}
    </div>
    <a class='back button' href='/add_flight'>Back</a>
</div>";
    }
}
