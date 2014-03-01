<?php
namespace module\tables\view;

use classes\ajax;
use classes\view;
use html\node;
use module\tables\form\table_gen_form;
use module\tables\form\table_gen_form_basic;

class _default extends \template\html {

    /** @var  \module\tables\controller $module */
    public $module;


    public function get_view() {
        /** @var \module\tables\object\league_table $table */
        $table = $this->module->current;
        //$table->use_preset(0);
        //$table->type = 0;
        $html = '';
        $form = new table_gen_form_basic();
        $form->set_from_options($table->options);
        $form->post_submit_text = $this->get_key();
        $html .= $form->get_html()
                      ->get();
        $form = new table_gen_form();
        $form->set_from_options($table->options);
        $form->post_submit_text = $this->get_key();
        $html .= $form->get_html()
                      ->get();
        $html .= node::create('div#generated_tables', [], $table->get_table() . node::create('a.show_all.button', [
                'href' => $table->get_show_all_url(),
                'target' => '_blank'], 'Show all on map'));

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
            \core::$inline_script[] = $inline_script;
        }
        return $html;
    }


    protected function get_key() {
        return node::create('div.key_switch', [],
            node::create('span', [], 'Key') .
            node::create('div#key2', [],
                node::create('b', [], 'KML prefixes:') .
                '-is no trace, = shows 2D, &#8801; shows 3D <br/>' .
                node::create('b', [], 'Launch prefixes:') .
                'A = Aerotow, W = Winch, else Foot <br/>' .
                node::create('b', [], 'Colours:') .
                node::create('span.od', [], 'Open Dist') .
                node::create('span.or', [], 'Out & Return') .
                node::create('span.goal', [], 'Goal') .
                node::create('span.tr', [], 'Triangle') .
                node::create('span.ft', [], 'Flat Triangle')
            )
        );
    }
}
