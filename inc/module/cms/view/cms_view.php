<?php
namespace cms;
/**
 * Class cms_view
 * @package cms
 */
abstract class cms_view extends \core_view {

    /**
     * @return \html\node
     */
    public function get() {
        return $this->get_view()->get();
    }

}
