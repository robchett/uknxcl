<?php

namespace module\tables\view;

use classes\module;
use html\node;
use module\tables\controller;
use module\tables\form\table_gen_form;
use module\tables\form\table_gen_form_basic;
use module\tables\model\league_table;
use template\html;

/** @extends html<\module\tables\controller, league_table> */
class _default extends html {

    public function get_view(): string {
        $table = $this->current;
        $form1 = new table_gen_form_basic();
        $form1->set_from_options($table);
        $form1->post_submit_text = $this->get_key();
        $form2 = new table_gen_form();
        $form2->set_from_options($table);
        $form2->post_submit_text = $this->get_key();

        return "
<h1 class='page-header'>Results</h1>
<a id='options' class='glyphicon glyphicon-filter'></a>
<div class='forms_wrapper callout callout-primary'>
    {$form1->get_html()}
    {$form2->get_html()}
</div>
<div id='generated_tables'>
    {$table->get_table()}
    <a class='show_all button' href='{$table->get_show_all_url()}' target='_blank'>Show all on map</a>
</div>

<script>
    var load_callback = load_callback || [];
    load_callback.push(function () {
        \$body.on('click', '.form_toggle', function () {
            $('.basic_tables_wrapper').hide();
            $('.advanced_tables_wrapper').hide();
            $('.' + $(this).data('show')).show().find('form').show();
        });
        \$body.off('click', '#options');
        \$body.on('click', '#options', function () {
            $('.forms_wrapper').stop(true, true).toggle();
        });
        $('.forms_wrapper').slideUp();
        $('.advanced_tables form').hide();
    });
</script>";
    }


    protected function get_key(): string {
        return node::create('div.key_switch', [],
            "<span>Key<span>" .
            node::create('div#key2', [],
                "<b>KML prefixes:<b>-is no trace, = shows 2D, &#8801; shows 3D <br/><b>Launch prefixes:<b>A = Aerotow, W = Winch, else Foot <br/><b>Colours:<b><span class='od'>Open Dist<span><span class='or'>Out & Return<span><span class='goal'>Goal<span><span class='tr'>Triangle<span><span class='ft'>Flat Triangle<span>"
            )
        );
    }
}
