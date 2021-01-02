<?php
/**
 * Class _cms_group
 */

namespace module\cms\model;

use classes\table;
use module\cms\model\_cms_group as __cms_group;


class _cms_group extends table {


    public int $gid;

    /**
     * @var string
     */
    public string $title;

    public static function create($title): _cms_group {
        $group = new __cms_group();
        $group->do_retrieve(['title'], ['where_equals' => ['title' => $title]]);
        if (!$group->get_primary_key()) {
            $group->title = $title;
            $group->do_save();
        }
        return $group;
    }

}
