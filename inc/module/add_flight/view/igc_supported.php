<?php
namespace module\add_flight\view;

use classes\ajax;
use classes\get;
use classes\view;
use html\node;
use module\add_flight\form\igc_form;
use module\add_flight\form\igc_upload_form;

class igc_supported extends \template\html {

    public function get_page_selector() {
        return get::__namespace($this->module, 0) . '-igc';
    }

    public function get_view() {
        $form = new igc_form();
        $form2 = new igc_upload_form();
        $html = node::create('div.add_flight_section.upload', [],
            $form2->get_html()->get() .
            $form->get_html()->get() .
            node::create('a.back.button', ['href' => '/add_flight'], 'Back')
        );
        $script = '
    $("body").on("change", "input#kml", function () {
        if ($(this).val().slice(-3) == "igc") {
            $("#kml_calc").fadeIn(1000);
            $("#kml_calc").html("<div id=\'console\'><a class=\'calc\'>Calculate</a></div>");
            $("#kml_calc a").show();
            $("#kml_wrapper").addClass("pass").removeClass("hoverFail");
            $("#kml_wrapper p.text").html("Click calculate to continue or choose another file");
        } else {
            $("#kml_wrapper p.text").html("Please us an IGC file");
            $("#kml_wrapper").addClass("hoverFail");
            $("#kml_calc").hide();
        }
    });
    $("body").on("change", "input, select, textarea", function (e) {
        $("#" + $(this).attr("id") + "_hidden").val($(this).val());
        return true;
    });
    $("body").on("click", "#kml_calc a.calc", function () {
        $("#igc_upload_form").submit();
        $("#kml_wrapper").removeClass("pass");
    });
    $("body").on("change","input#coords",function () {
        var arr = $(this).val().split(";");
        var coord_array = new Planner();
        var str = "";
        arr.each(function(arg, i) {
            var coord = new Coordinate();
            coord.set_from_OS(arg);
            if (coord.is_valid_gridref()) {
                coord_array.push(coord);
            } else {
                str = "Coordinate " + (i * 1 + 1) + " is not valid";
            }
        });
        if (!str.length) {
        switch (coord_array.count) {
            case 0:
                str = "";
                break;
            case 1:
                str = "Please enter at least two coordinates";
                break;
            case 2:
                str = "Flight Type: Open Distance, Score: " + coord_array.get_total_distance().round(2) + " before multipliers";
                break;
            case 3:
                if(coord_array[0] == coord_array[2]) {
                    str = "Flight Type: Out & Return, Score: " + coord_array.get_total_distance().round(2) + " before multipliers";
                } else {
                    str = "Flight Type: Open Distance, Score: " + coord_array.get_total_distance().round(2) + " before multipliers";
                }
                break;
            case 4:
                if(coord_array[0] == coord_array[4]) {
                    str = "Flight Type: Triangle, Score: " + coord_array.get_total_distance().round(2) + " before multipliers";
                } else {
                    str = "Flight Type: Open Distance, Score: " + coord_array.get_total_distance().round(2) + " before multipliers";
                }
                break;
            case 5:
                str = "Flight Type: Open Distance, Score: " + coord_array.get_total_distance().round(2) + " before multipliers";
                break;
            default :
                str = "you can only enter 5 coordinates";
            }
        }
        $(this).parents("form").find(".defined_info").html(str);
    });
    $("body").on("click","a.score_select",function () {
        var data = $(this).data("post");
        $("#igc_form #temp_id").val(data.track);
        $("#igc_form #type").val(data.type);
        $("#igc_upload_form").html("<p class=\"restart\">Your flight details have been saved, please complete the form below, \"Additional Details\", to finalise your sumbission.<br/><a data-ajax-click=\"igc_upload_form:reset\" href=\"#\" >Restart</a></p>");
        $("#igc_form input.submit").removeAttr("disabled");
    });';
        if (ajax) {
            ajax::add_script($script);
        } else {
            \core::$inline_script[] = $script;
        };
        return $html;
    }
}
