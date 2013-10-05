<?php

namespace core\classes;

use classes\db as _db;

/**
 * Class table_array
 */
abstract class
table_array extends \classes\collection {

    /**
     * @var bool
     */
    protected static $statics_set = false;
    /* @var table_iterator */
    public $iterator;
    /**
     * @var array
     */
    protected $retrieved_fields = array();
    /**
     * @var array
     */
    protected $original_retrieve_options = array();

    /**
     *
     */
    public function __construct($input = [], $flags = 0, $iterator_class = "\\classes\\table_iterator") {
        parent::__construct($input, $flags, $iterator_class);
        if (!self::$statics_set) {
            $this->set_statics();
        }
    }

    /**
     *
     */
    protected function set_statics() {
        self::$statics_set = true;
    }


    /**
     * @param array $keys
     */
    public function lazy_load(array $keys) {
        $fields_to_retrieve = array();
        foreach ($keys as $key) {
            if (!$this->has_field($key)) {
                $fields_to_retrieve[] = $key;
            }
        }
        if (!empty($fields_to_retrieve)) {
            $this->do_retrieve($fields_to_retrieve, $this->original_retrieve_options);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function has_field($name) {
        return (isset($this->retrieved_fields[$name]) ? $this->retrieved_fields[$name] : false);
    }

    /**
     * @param $fields
     * @param $options
     */
    public function do_retrieve($fields, $options) {
        self::get_all($fields, $options);
    }

    /**
     * @param string $class
     * @param array $fields_to_retrieve
     * @param array $options
     * @return table_array
     */
    static function get_all($class, array $fields_to_retrieve, $options = array()) {
        /** @var $return table_array */
        $return = new static();
        $parameters = (isset($options['parameters']) ? $options['parameters'] : array());
        $sql = _db::get_query($class, $fields_to_retrieve, $options, $parameters);
        $res = db::query($sql, $parameters);
        if (_db::num($res)) {
            while ($row = _db::fetch($res, $class)) {
                $return[] = $row;
            }
        }
        $return->reset_iterator();
        return $return;
    }

    public function reverse() {
        $this->exchangeArray(array_reverse($this->getArrayCopy()));
    }

    /**
     * @return mixed
     */
    public function get_class() {
        return str_replace('_array', '', get_class($this));

    }

    /**
     * @param $index
     * @param $object
     */
    public function inject($index, $object) {
        $start = $this->subset(0, $index - 1);
        $end = $this->subset($index);
        $this->exchangeArray(array_merge($start, $object, $end));
    }

    /**
     * @return int
     */
    public function iterator_cnt() {
        return $this->iterator->index;
    }

    /**
     *
     */
    public function rewind() {
        $this->iterator->rewind();
    }
}