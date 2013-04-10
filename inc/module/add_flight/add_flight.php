<?php

class add_flight extends core_module {

    public $page = 'add_flight';

    public function __controller(array $path) {
        parent::__controller($path);

    }


    public function get() {
        $this->page = new page();
        $this->page->do_retrieve(array(),array('where_equals'=>array('module_name'=>'add_flight')));
        $form = new igc_form();
        $form2 = new igc_upload_form();
        $html = '
<div id="add_flight_box">
    <div id="add_flight_wrapper">
        <div id="add_flight_inner">
            ' . $this->page->body . '
            <div id="add_flight_section">';
        $html .= $form2->get_html()->get();
        $html .= $form->get_html()->get();
        $html .= '
            </div>
        </div>
    </div>
</div>';
        $script = '
        
$(document).ready(function () {
    $("body").on("change", "input#defined", function () {
        if ($(this).attr("checked"))
            $(".fieldset_1").show(); else
            $(".fieldset_1").hide();
    })
    $("body").on("change", "input#kml", function () {
        if ($(this).val().slice(-3) == "igc") {
            $("#kml_calc").fadeIn(1000);
            $("#kml_calc a").show();
            $("#kml_wrapper").addClass("pass").removeClass("hoverFail");
        } else {
            $("#kml_wrapper p.text").html("Please us an IGC file");
            $("#kml_wrapper").addClass("hoverFail");
            $("#kml_calc").hide();
        }
    });
    $("body").on("click", ".lightbox", function (e) {
        e.preventDefault();
        $.colorbox({href: $(this).attr("data-href")});

           return false;
        });

    $("body").on("change", "input, select, textarea", function (e) {
        $("#" + $(this).attr("id") + "_hidden").val($(this).val());
        return true;
    });
});';
        if(ajax) {
            ajax::add_script($script);
        } else {
            core::$inline_script[] = $script;
        }
        ;
        return $html;
    }

}
