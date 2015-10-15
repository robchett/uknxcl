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
        $form1 = new table_gen_form_basic();
        $form1->set_from_options($table->options);
        $form1->post_submit_text = $this->get_key();
        $form2 = new table_gen_form();
        $form2->set_from_options($table->options);
        $form2->post_submit_text = $this->get_key();
        $html .= 
            node::create('h1.page-header', [], 'Results') . 
            node::create('a#options.glyphicon.glyphicon-filter', [], '') .
            node::create('div.forms_wrapper.callout.callout-primary', [], [
                $form1->get_html(), 
                $form2->get_html()
            ]) .
            node::create('div#generated_tables', [], $table->get_table() . 
            node::create('a.show_all.button', ['href' => $table->get_show_all_url(), 'target' => '_blank'], 'Show all on map'));

        $inline_script = '
        $("body").on("click", ".form_toggle", function () {
            $(".basic_tables_wrapper").hide();
            $(".advanced_tables_wrapper").hide();
            $("." + $(this).data("show")).show();
            $("." + $(this).data("show")).find("form").show();
        });
        $("body").off("click", "#options");
        $("body").on("click", "#options", function() {
            $(".forms_wrapper").stop(true, true).toggle();
        });
        $(".forms_wrapper").slideUp();
        $(".advanced_tables form").hide();';
        if(isset($_REQUEST['form'])) {
            $form = $_REQUEST['form'];
            if($form == 'advanced_tables' || $form == 'basic_tables') {
                $inline_script .= '
                $(".basic_tables_wrapper").hide();
                $(".advanced_tables_wrapper").hide();
                $(".' . $form . '_wrapper").show();
                $(".' . $form . '_wrapper").find("form").show();
                ';
            }
        }

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
