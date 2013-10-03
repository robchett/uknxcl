<?php
namespace module\comps\view;

use classes\ajax;
use classes\view;
use html\node;
use module\comps\object\comp;

class _default extends view {

    public function get_view() {
        $comps = comp::get_all(['type', 'round', 'task', 'comp.title AS title', 'date', 'cid', 'comp_group.title AS class', 'file'], ['join' => ['comp_group' => 'comp.class = comp_group.cgid'], 'order' => 'date DESC, round DESC, task DESC, class ASC']);
        $html = node::create('div#comp_wrapper div#comp_inner', [],
            node::create('div#comp_list', [],
                node::create('h2', [], 'Select a Competition') .
                node::create('table', [],
                    node::create('thead', [],
                        node::create('th', [], 'Comp') .
                        node::create('th', [], 'Round') .
                        node::create('th', [], 'Task') .
                        node::create('th', [], 'Class') .
                        node::create('th', [], 'Title') .
                        node::create('th', [], 'Date') .
                        node::create('th', [])
                    ) .
                    node::create('tbody', [],
                        $comps->iterate_return(
                            function (comp $comp) use (&$body) {
                                return node::create('tr', [],
                                    node::create('td', [], $comp->type) .
                                    node::create('td', [], (int) $comp->round) .
                                    node::create('td', [], (int) $comp->task) .
                                    node::create('td', [], $comp->class) .
                                    node::create('td', [], $comp->title) .
                                    node::create('td', [], date('d/m/Y', strtotime($comp->date))) .
                                    node::create('td a.button', ['href' => $comp->get_url()], 'View')
                                );
                            }
                        )
                    )
                ) .
                node::create('div#comp_view')
            )
        );

        $script =
            "$('#comp').on('click','#comp_list ul li a',function () {
                cpid = $(this).attr('data-click');
                page('/comp/' + cpid);
            });";

        if (ajax) {
            ajax::add_script($script);
        } else {
            \core::$inline_script[] = $script;
        }

        return $html;
    }
}
