<?php

class _default_view extends view {

    public function get_view() {
        $table = new league_table();
        $table->use_preset(0);
        $table->set_year(date('Y'));
        $table->type = 0;
        $html = '';
        $form = new table_gen_form_basic();
        $form->post_submit_text = $this->get_key();
        $html .= $form->get_html()->get();
        $form = new table_gen_form();
        $form->post_submit_text = $this->get_key();
        $html .= $form->get_html()->get();
        $html .= '<div id="generated_tables">' . $table->get_table() . '</div>';

        $inline_script = '
        $("body").on("click", ".form_toggle", function () {
            $("#basic_tables_form_wrapper").hide();
            $("#advanced_tables_wrapper").hide();
            $("#" + $(this).data("show")).show();
        });
        $("#basic_tables_form_wrapper h2,#advanced_tables_wrapper h2").click(function () {
            var $parent = $(this).parents("div").eq(0);
            if($parent.hasClass("visible")) {
                $parent.removeClass("visible").children("form").stop(true, true).slideUp(function () {
                    $(".key_switch").hide();
                });
            } else {
                $parent.addClass("visible").children("form").stop(true, true).slideDown(function () {
                    $(".key_switch").show();
                });
            }
        });
        $("#basic_tables_form_wrapper form,#advanced_tables_wrapper form").slideUp();';
        if (ajax) {
            ajax::add_script($inline_script);
        } else {
            core::$inline_script[] = $inline_script;
        }
        return $html;
    }


    protected function get_key() {
        return '
        <div class="key_switch">
            <span class="">Key</span>
            <div id="key2">
                <b>KML prefixes:</b>- is no trace, = shows 2D, &#8801; shows 3D <br/>
                <b>Launch prefixes: </b> A = Aerotow, W = Winch, else Foot <br/>
                <b>Colours:</b>
                <a class="od"> Open Dist</a>
                <a class="or">Out & Return</a>
                <a class="goal">Goal</a>
                <a class="tr">Triangle</a>
                <a class="ft">Flat Triangle</a>
            </div>
        </div>';
    }
}
