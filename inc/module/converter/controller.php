<?php
namespace module\converter;

use classes\module;

class controller extends module {

    public function __controller(array $path) {
        \core::$page_config->title_tag = 'Converter';
        \core::$css = array('/.core/module/cms/css/cms.css');
        \core::$js = array('/.core/js/jquery.js', '/.core/js/_ajax.js', '/.core/js/colorbox.js');
        $this->view = 'dashboard';
        if (isset($path[1]) && !empty($path[1])) {
            $this->view = $path[1];
        }
        parent::__controller($path);
    }

}
 