<?php
namespace module\pages\view;

use html\node;
use module\pages\object\page;

/** @property \module\pages\controller $module */
class home extends \core\module\pages\view\_default {

    public function get_view() {
        $html = node::create('div.content', [], $this->module->current->body);
        $pages = page::get_all(['title', 'info', 'module_name', 'fn'], ['order' => 'position', 'where' => 'pid != 12']);
        if ($pages) {
            $html .= node::create('div#page_list', [],
                $pages->iterate_return(
                    function (page $page) {
                        return node::create('div.page', [],
                            node::create('a', ['href' => $page->get_url()],
                                node::create('h3', [], $page->title) .
                                ($page->info ? node::create('p', [], $page->info) : '')
                            )
                        );
                    }
                )
            );
        }
        return $html;
    }

}
