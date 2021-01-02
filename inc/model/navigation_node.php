<?php

namespace model;

use classes\table;
use html\node;
use model\navigation_node as _navigation_node;


class navigation_node extends table {


    public $link;
    public bool $title;

    public static function get_list($nnid = 0): string {
        $nodes = _navigation_node::get_all([], ['where_equals' => ['parent_nnid' => $nnid]]);
        if ($nodes->count()) {
            return node::create('ul', [], $nodes->iterate_return(function (_navigation_node $node) {
                return node::create('li', [], node::create('a', ['href' => $node->get_url()], $node->get_title()) . static::get_list($node->get_primary_key()));
            }));
        } else {
            return '';
        }
    }

    public function get_url(): string {
        return $this->link;
    }

    public function get_title(): bool {
        return $this->title;
    }
}
 