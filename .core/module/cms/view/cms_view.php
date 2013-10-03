<?php
namespace core\module\cms\view;

use core\classes\view;

/**
 * Class cms_view
 * @package cms
 */
abstract class cms_view extends view {

    /**
     * @return \html\node
     */
    public function get() {
        return $this->get_view()->get();
    }

}
