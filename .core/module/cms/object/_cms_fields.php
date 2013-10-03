<?php
namespace core\module\cms\object;

use core\classes\table;

abstract class _cms_fields extends table {

    public $fid;
    public $field_name;
    public $table_key = 'fid';
    public $title;
    public $type;

    public static $module_id = '';
    public static $fields = [];
}
