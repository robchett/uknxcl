<?php
namespace core\classes;

abstract class collection extends \ArrayObject {

    private $first_index = 0;
    /** @var  \arrayIterator */
    public $iterator;

    public function first() {
        return $this[0];
    }

    public function first_index() {
        return $this->first_index;
    }

    /**
     * @param string $key
     * @return bool|mixed
     */
    public function next(&$key = '') {
        if ($this->iterator->valid()) {
            $key = $this->iterator->key();
            $value =  $this->iterator->current();
        } else {
            return false;
        }
        $this->iterator->next();
        return $value;
    }

    /**
     *
     */
    public function reset_iterator() {
        $this->iterator = $this->getIterator();
        //$this->iterator->rewind();
    }

    /**
     * @param $function
     * @param int $cnt
     */
    public function iterate($function, $cnt = 0) {
        $this->reset_iterator();
        while ($obj = $this->next()) {
            $cnt++;
            call_user_func($function, $obj, $cnt);
        }
    }

    public function iterate_return($function, $cnt = 0) {
        $res = '';
        $this->reset_iterator();
        while ($obj = $this->next()) {
            $cnt++;
            $res .= call_user_func($function, $obj, $cnt);
        }
        return $res;
    }

    public function last() {
        return $this[$this->count() - 1];
    }

    public function remove_first($int = 1) {
        parent::__construct($this->subset($int));
    }

    public function remove_last($int = 0) {
        if ($int) {
            for ($i = 0; $i < $int; $i++)
                $this->remove_last();
        } else {
            $this->offsetUnset($this->count() - 1);
        }
    }

    public function subset($start = 0, $end = null) {
        $sub = array();
        if ($end == null || $end < $start)
            $end = $this->count();
        for ($i = $start; $i < $end; $i++) {
            $sub[] = $this[$i];
        }
        return $sub;
    }
}
 