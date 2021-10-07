<?php

namespace classes;

use classes\ajax as _ajax;

class jquery {

    public static array $colourbox_defaults = ['opacity' => 0.7, 'static' => 1, 'maxWidth' => '85%', 'maxHeight' => '85%'];

    public static function colorbox(array $options = []): void {
        $options = array_merge($options, self::$colourbox_defaults);
        _ajax::add_script('$.colorbox(' . json_encode($options) . ')');
    }
}
