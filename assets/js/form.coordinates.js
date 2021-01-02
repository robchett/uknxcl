function form_coordinates_edit() {
    var val = $(this).val().trim("; ");
    $(this).val(val);
    var arr = val.split(";");
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
                str = "You can only enter 5 coordinates";
        }
    }
    $(this).parents("form").find(".defined_info").html(str);
}

function form_coordinates_init() {
    $(".form-group.coordinates input").change(form_coordinates_edit);
}

$(document).ready(function () {
    form_coordinates_init();
    $.fn.ajax_factory.defaults.complete.push('form_coordinates_init');
});

