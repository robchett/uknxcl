<?php
use html\node;

/** @property string table_key */
abstract class table {
    /**
     * @var array
     */
    public static $define_table = array();
    //public $table_key;
    /** @var  int $module_id */
    //public static $module_id = 0;
    /**
     * @var int
     */
    public $mid;
    /**
     * @var bool
     */
    public $raw = false;
    public $position;

    /**
     * @param array $fields
     * @param int $id
     */
    public function  __construct($fields = array(), $id = 0) {
        if ($id) {
            $this->do_retrieve_from_id($fields, $id);
        }
        $class = get_class($this);
        if (!isset($class::$fields)) {
            /** @var table $class */
            $class::_set_fields();
        }
    }

    /**
     * @return string
     */
    public function get_url() {
        return '';
    }

    /**
     * @param array $fields
     * @param $id
     */
    public function do_retrieve_from_id(array $fields, $id) {
        $this->do_retrieve($fields, array('limit' => '1', 'where_equals' => array($this->table_key => $id)));
    }

    /**
     * @return bool
     */
    public function get_primary_key() {
        if (isset($this->{$this->table_key}) && $this->{$this->table_key}) {
            return $this->{$this->table_key};
        }
        return false;
    }

    /**
     * @return mixed
     */
    public static function get_count() {
        $class = get_called_class();
        $return = new $class();
        $sql = 'SELECT count(' . $return->table_key . ') AS count FROM ' . $class . ' WHERE live = 1 and deleted = 0';
        $res = \db::result($sql);
        return $res->count;
    }

    /**
     * @return int
     */
    public function do_cms_update() {
        if (admin) {
            \db::update(get::__class_name($this))->add_value($_REQUEST['field'], $_REQUEST['value'])->filter_field($this->table_key, $_REQUEST['id'])->execute();
        }
        return 1;
    }

    /**
     * @param $row
     */
    public function set_from_row($row) {
        foreach ($row as $key => $val) {
            if (isset(static::$fields[$key])) {
                $class = get_class(static::$fields[$key]);
                /** @var \form\field $class */
                $this->$key = $class::sanitise_from_db($val);
            } else {
                $this->$key = $val;
            }
        }
    }

    /**
     * @param array $fields
     * @param array $options
     */
    public function do_retrieve(array $fields, array $options) {
        $options['limit'] = 1;
        $parameters = (isset($options['parameters']) ? $options['parameters'] : array());
        $sql = \db::get_query(get_class($this), $fields, $options, $parameters);
        $res = \db::query($sql, $parameters);
        if (\db::num($res)) {
            $this->set_from_row(\db::fetch($res));
        }
    }

    /**
     * @return bool
     */
    public function do_submit() {
        $this->raw = true;
        $this->set_from_request();
        $form = $this->get_form();
        foreach ($form->fields as $field) {
            $field->raw = true;
        }
        $form->action = get_class($this) . ':do_submit';
        $ok = $form->do_submit();
        if ($ok) {
            $type = (!isset($this->{$this->table_key}) || !$this->{$this->table_key} ? 'Added' : 'Updated');
            $this->do_save();
            ajax::inject('#' . $_REQUEST['ajax_origin'], 'before', node::create('p.success.boxed.' . strtolower($type), [], $type . ' successfully'));
        } else {
            ajax::update($form->get_html()->get());
        }
        return $ok;
    }

    /**
     *
     */
    public function set_from_request() {
        /** @var \form\field $field */
        foreach ($this->get_fields() as $field) {
            if ($this->raw) {
                $field->raw = true;
            }
            $field->set_from_request();
        }
    }

    /**
     * @return string
     */
    public function do_save() {
        $class = get::__class_name($this);
        if (isset($this->{$this->table_key}) && $this->{$this->table_key}) {
            $query = new db\update($class);
        } else {
            $query = new db\insert($class);
        }
        /** @var form\field $field */
        foreach ($this->get_fields() as $field) {
            if ($field->field_name != $this->table_key) {
                if (isset($this->{$field->field_name})) {
                    try {
                        $data = $field->get_save_sql();
                        $query->add_value($field->field_name, $data);
                    } catch (RuntimeException $e) {

                    }
                }
            }
        }
        if (isset($this->{$this->table_key}) && $this->{$this->table_key}) {
            $query->filter_field($this->table_key, $this->{$this->table_key});
        }
        $query->execute();
        if (!(isset($this->{$this->table_key}) && $this->{$this->table_key})) {
            $this->{$this->table_key} = \db::insert_id();
        }
        if ($this->{$this->table_key}) {
            foreach ($this->get_fields() as $field) {
                if (get_class($field) == 'form\field_file') {
                    $this->do_upload_file($field);
                }
            }
        }
        return $this->{$this->table_key};
    }

    /**
     * @param \form\field $field
     */
    protected function do_upload_file(\form\field $field) {
        if (isset($_FILES[$field->field_name]) && !$_FILES[$field->field_name]['error']) {
            $tmp_name = $_FILES[$field->field_name]['tmp_name'];
            $name = $_FILES[$field->field_name]['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if (!is_dir(root . '/uploads/' . get::__class_name($this) . '/' . $this->{$this->table_key})) {
                mkdir(root . '/uploads/' . get::__class_name($this) . '/' . $this->{$this->table_key});
            }
            move_uploaded_file($tmp_name, root . '/uploads/' . get::__class_name($this) . '/' . $this->{$this->table_key} . '/' . $field->fid . '.' . $ext);
        }
    }

    /**
     * @return node
     */
    public function get_cms_edit() {
        $form = $this->get_form();
        $form->set_from_object($this);
        foreach ($form->fields as $field) {
            if (get_class($field) == 'form\field_file') {
                $form->action = '/index.php?module=' . get_class($this) . '&act=do_submit&no_ajax=on&ajax_origin=' . $form->id;
            } else if (get_class($field) == 'form\field_textarea') {
                \core::$inline_script[] = 'CKEDITOR.replace("' . $field->field_name . '");';
            }
            $field->label .= '<span class="field_name">' . $field->field_name . '</span>';
            $field->raw = true;
        }
        if (!isset($this->{$this->table_key}) || !$this->{$this->table_key}) {
            $form->get_field_from_name($this->table_key)->set_attr('hidden', true);
            $form->{'parent_' . $this->table_key} = 0;
        }
        return $form->get_html();
    }

    /**
     * @return \form\form
     */
    public function get_form() {
        $form = new form\form($this->get_fields());
        $form->id = get_class($this) . '_form';
        if (isset($form->attributes['target'])) {
            $form->attributes['target'] = 'form_target_' . $form->id;
        }
        return $form;
    }

    /**
     * @param $fields
     */
    public function lazy_load($fields) {
        $this->do_retrieve_from_id($fields, $this->{$this->table_key});
    }

    /** @return html\node */
    public function get_cms_edit_module() {
        $list = node::create('table#module_def', [],
            node::create('thead', [],
                node::create('th', [], 'Field id') .
                node::create('th', [], 'Pos') .
                node::create('th', [], 'Title') .
                node::create('th', [], 'Database Title') .
                node::create('th', [], 'Type') .
                node::create('th', [], 'List') .
                node::create('th', [], 'Required') .
                node::create('th', [], 'Filter')
            )
        );

        /** @var \form\field $field */
        foreach ($this->get_fields() as $field) {
            $list->add_child(node::create('tr', [], $field->get_cms_admin_edit()));
        }
        return $list;
    }

    /**
     * @return array
     */
    public function get_cms_list() {
        $nodes = array();
        /** @var \form\field $field */
        foreach ($this->get_fields() as $field) {
            if ($field->list) {
                $nodes[] = node::create('td.' . get_class($field), [], $field->get_cms_list_wrapper(isset($this->{$field->field_name}) ? $this->{$field->field_name} : '', get_class($this), $this->{$this->table_key}));
            }
        }
        return $nodes;
    }

    /**
     * @return array
     */
    public function get_fields() {
        $fields = static::_get_fields();
        foreach ($fields as $field) {
            $field->parent_form = $this;
        }
        return $fields;
    }

    /**
     *
     */
    public static function _set_fields() {
        $fields = array();
        $res = \db::query('SELECT * FROM _cms_fields WHERE mid=:mid ORDER BY `position` ASC', array('mid' => static::$module_id,));
        while ($row = \db::fetch($res)) {
            $class = 'form\field_' . $row->type;
            /** @var form\field $field */
            $field = new $class($row->field_name, array());
            $field->label = $row->title;
            $field->set_from_row($row);
            $fields[$row->field_name] = $field;
        }
        static::$fields = $fields;
    }

    /**
     * @return array
     */
    private static function _get_fields() {
        if (!isset(static::$fields)) {
            static::_set_fields();
        }
        $clone = array();
        foreach (static::$fields as $key => $field) {
            $clone[$key] = clone $field;
        }
        return $clone;
    }
}

/**
 * Class table_array
 */
class table_array extends ArrayObject {

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
    public function __construct($input = [], $flags = 0, $iterator_class = "table_iterator") {
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
     * @param int $int
     */
    public function remove_first($int = 1) {
        parent::__construct($this->subset($int));
    }

    /**
     * @param int $start
     * @param null $end
     * @return array
     */
    public function subset($start = 0, $end = null) {
        $sub = array();
        if ($end == null || $end < $start)
            $end = $this->count();
        for ($i = $start; $i < $end; $i++) {
            $sub[] = $this[$i];
        }
        return $sub;
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
     * @param array $fields_to_retrieve
     * @param array $options
     * @return table_array
     */
    static function get_all(array $fields_to_retrieve, $options = array()) {
        $class = get_called_class();
        /** @var $return table_array */
        $return = new $class();
        $parameters = (isset($options['parameters']) ? $options['parameters'] : array());
        $sql = \db::get_query($return->get_class(), $fields_to_retrieve, $options, $parameters);
        $res = \db::query($sql, $parameters);
        if (\db::num($res)) {
            while ($row = \db::fetch($res, $return->get_class())) {
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
     *
     */
    public function reset_iterator() {
        $this->iterator = $this->getIterator();
        //$this->iterator->rewind();
    }

    /**
     * @return mixed
     */
    public function get_class() {
        return str_replace('_array', '', get_class($this));

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


    /**
     * @return bool|mixed
     */
    public function next() {
        if ($this->iterator->index == -1) {
            $this->iterator->index = 0;
            if ($this->iterator->valid()) {
                return $this->iterator->current();
            } else return false;
        } else {
            $this->iterator->next();
            $this->iterator->index++;
            if ($this->iterator->valid()) {
                return $this->iterator->current();
            } else return false;
        }
    }

    /**
     * @param int $int
     */
    public function remove_last($int = 0) {
        if ($int) {
            for ($i = 0; $i < $int; $i++)
                $this->remove_last();
        } else {
            $this->offsetUnset($this->count() - 1);
        }
    }

    /**
     * @return mixed
     */
    public function last() {
        return $this[$this->count() - 1];
    }

    /**
     * @return mixed
     */
    public function first() {
        return $this[0];
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

/**
 * Class table_iterator
 */
class table_iterator extends ArrayIterator {
    /**
     * @var int
     */
    public $index = -1;

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
