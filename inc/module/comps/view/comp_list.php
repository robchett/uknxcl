<?php
namespace module\comps\view;

use module\comps\object\comp;

class comp_list extends \template\html {
    use \traits\twig_view;

    function get_template_data() {
        $comps = comp::get_all([
            'type',
            'round',
            'task',
            'comp.title AS title',
            'date',
            'cid',
            'comp_group.title',
            'file'
        ], [
            'join' => [
                'comp_group' => 'comp.cgid = comp_group.cgid'
            ],
            'order' => 'date DESC, round DESC, task DESC, comp.cgid ASC'
        ]);
        return ['comps' => $comps->get_template_data()];
    }
}
