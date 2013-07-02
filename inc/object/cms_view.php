<?php

abstract class cms_view extends core_view {

    public function get() {
        return $this->get_view()->get();
    }

}
