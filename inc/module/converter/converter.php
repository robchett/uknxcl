<?php

class converter extends core_module {
    public function __controller(array $path) {
        core::$page_config->title_tag = 'Converter';
        core::$css = array('/inc/module/cms/css/cms.css');
        core::$js = array('/js/jquery/jquery.js', '/js/_ajax.js', '/js/jquery/colorbox.js');
        $this->view = 'dashboard';
        if (isset($path[1]) && !empty($path[1])) {
            $this->view = $path[1];
        }
        parent::__controller($path);
    }

}
 