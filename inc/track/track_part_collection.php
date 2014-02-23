<?php
namespace track;

use classes\collection;

class track_part_collection extends collection {

    /**
     * @return track_part
     */
    public function first() {
        return parent::first();
    }

    /**
     * @return track_part
     */
    public function last() {
        return parent::last();
    }

    public function reduce_index($int) {
        for ($i = 0; $i < $this->count(); $i++)
            $this->offsetGet($i)->reduce_index($int);
    }
}