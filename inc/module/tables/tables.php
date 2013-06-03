<?php

class tables extends core_module {

    public $page = 'tables';


    public function get() {
        $table = new league_table();
        $table->use_preset(0);
        $table->set_year(date('Y'));
        $table->type = 0;
        $html = '';
        $form = new table_gen_form_basic();
        $html .= $form->get_html()->get();
        $form = new table_gen_form();
        $html .= $form->get_html()->get();
        $html .= '
        <div class="key_switch">
            <span class="">Key</span>
            <div id="key2">
                <b>KML prefixes:</b>- is no trace, = shows 2D, &#8801; shows 3D <br/>
                <b>Launch prefixes: </b> A = Aerotow, W = Winch, else Foot <br/>
                <b>Colours:</b>
                <a style=\'color:black\'> Open Dist</a>
                <a style=\'color:green\'>Out & Return</a>
                <a style=\'color:red\'>Goal</a> <a style=\'color:blue\'>Triangle</a>
            </div>
        </div>
<div id="generated_tables">' . $table->get_table() . '</div>';

        return $html;
    }

}
