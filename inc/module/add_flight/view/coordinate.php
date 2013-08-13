<?php

class coordinate_view extends view {

    public function get_page_selector() {
        return get_class($this->module) . '-coordinate';
    }

    public function get_view() {
        $form = new coordinates_form();
        $html = '
        <div class="add_flight_section coordinate">
            ' . $form->get_html()->get() . '
            <a href="/add_flight" class="back button">Back</a>
        </div>';
        $script = '
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
    });';
        if (ajax) {
            ajax::add_script($script);
        } else {
            core::$inline_script[] = $script;
        };
        return $html;
    }
}
