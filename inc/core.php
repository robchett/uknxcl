<?php
class core extends \core\core {

    public function __construct() {
        self::$js[] = 'http://maps.google.com/maps/api/js?libraries=geometry';
        self::$js[] = 'https://www.google.com/jsapi';

        parent::__construct();
    }
}