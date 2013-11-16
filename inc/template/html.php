<?php
namespace template;

use classes\module;

class html extends \core\template\html {

    public function __construct(module $module) {
        \core::$global_script[] = 'var map';
        \core::$inline_script[] = '
map = new UKNXCL_Map($("#map_wrapper"));
if (typeof google != \'undefined\') {
    map.load_map();
} else {
    $(\'#map\').children(\'p.loading\').html(\'Failed to load Google resources\');
}
';
        parent::__construct($module);
    }

}
 