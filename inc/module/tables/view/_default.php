<?php
namespace module\tables\view;

use classes\ajax;
use html\node;
use module\tables\form\table_gen_form;
use module\tables\form\table_gen_form_basic;
use traits\twig_view;

class _default extends \template\html {
    use twig_view;

    /** @var  \module\tables\controller $module */
    public $module;


    public function get_template_data() {
        /** @var \module\tables\object\league_table $table */
        $table = $this->module->current;
        $form1 = new table_gen_form_basic();
        $form1->set_from_options($table->options);
        $form1->post_submit_text = $this->get_key();
        $form2 = new table_gen_form();
        $form2->set_from_options($table->options);
        $form2->post_submit_text = $this->get_key();

        return [
            'form1' => $form1->get_html(),
            'form2' => $form2->get_html(),
            'table' => $table->get_table(),
            'url' => $table->get_show_all_url()
        ];
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
