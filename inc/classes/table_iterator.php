<?php

namespace classes;

use ArrayIterator;

class table_iterator extends ArrayIterator {

    /**
     * @var int
     */
    public int $index = -1;

    /**
     *
     */
    public function rewind() {
        $this->index = -1;
        parent::rewind();
    }

    /**
     *
     */
    public function reset() {
        $this->index = -1;
        parent::rewind();
    }
}
