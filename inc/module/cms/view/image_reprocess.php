<?php

namespace module\cms\view;

use html\node;
use model\image_size;

class image_reprocess extends cms_view {

    /**
     * @return string
     */
    public function get_view(): string {
        $images = image_size::get_all([]);
        if ($images) {
            return node::create('div', [], node::create('table.module', [], node::create('thead', [], "<th>Field ID</th><th>Title<th>" . node::create('th', [])) . $images->iterate_return(function (image_size $image_size) {
                    return node::create('tr', [], "<td>{$image_size->fid}</td><td>{$image_size->title}<td>" . node::create('td a.button', ['href' => '?module=cms&act=image_reprocess&fid=' . $image_size->isid], 'Reprocess'));
                })));
        }
        return '';
    }
}
 