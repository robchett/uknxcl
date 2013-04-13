<?php
class table {
    public static $fields;
    public static $define_table = array();
    public static $module_id = 0;
    public $table_key;

    public function  __construct($fields = array(), $id = 0) {
        if ($id) {
            $this->do_retrieve_from_id($fields, $id);
        }
    }

    public function do_retrieve_from_id(array $fields, $id) {
        $parameters = array();
        $sql = db::get_query(get_class($this), $fields, array('limit' => '1', 'where_equals' => array($this->table_key => $id)), $parameters);
        $res = db::query($sql, $parameters);
        if (db::num($res)) {
            $row = db::fetch($res);
            foreach ($row as $key => $val) {
                $this->$key = $val;
            }

        }
    }

    public static function get_count(array $options = array()) {
        $class = get_called_class();
        $return = new $class();
        $sql = 'SELECT count(' . $return->table_key . ') AS count FROM ' . $class . ' WHERE live = 1 and deleted = 0';
        $res = db::result($sql);
        return $res->count;
    }

    public function do_cms_update() {
        if (admin) {
            db::query('UPDATE ' . get_class($this) . ' SET ' . $_REQUEST['field'] . '=:value WHERE ' . $this->table_key . '=:id', array(
                    'value' => $_REQUEST['value'],
                    'id' => $_REQUEST['id'],
                )
            );
        }
        return 1;
    }

    public function do_retrieve(array $fields, array $options) {
        $options['limit'] = 1;
        $parameters = (isset($options['parameters']) ? $options['parameters'] : array());
        $sql = db::get_query(get_class($this), $fields, $options, $parameters);
        $res = db::query($sql, $parameters);
        if (db::num($res)) {
            $row = db::fetch($res);
            foreach ($row as $key => $val) {
                $this->$key = $val;
            }

        }
    }

    public function do_submit() {
        $this->set_from_request();
        $form = $this->get_form();
        $form->action = get_class($this) . ':do_submit';
        $ok = $form->do_submit();
        if ($ok) {
            $type = (!isset($this->{$this->table_key}) || !$this->{$this->table_key} ? 'Added' : 'Updated');
            $this->do_save();
            ajax::inject($_REQUEST['ajax_origin'], 'before', '<p class="success ' . strtolower($type) . '">' . $type . ' successfully</p>');
        } else {
            ajax::update($form->get_html()->get());
        }
        return $ok;
    }

    public function set_from_request() {
        foreach ($this->get_fields() as $field) {
            if (isset($_REQUEST[$field->field_name])) {
                $this->{$field->field_name} = $_REQUEST[$field->field_name];
            }
        }
    }

    public function do_save() {
        $class = get_class($this);
        $elements = array();
        $parameters = array();
        $sql = (isset($this->{$this->table_key}) && $this->{$this->table_key} ? 'UPDATE ' : 'INSERT INTO ') . $class . ' SET ';
        foreach ($this->get_fields() as $field) {
            if ($field->field_name != $this->table_key) {
                if (isset($this->{$field->field_name})) {
                    $field->get_save_sql($elements, $parameters);
                }
            }
        }
        $sql .= implode(', ', $elements);
        if (isset($this->{$this->table_key}) && $this->{$this->table_key}) {
            $sql .= ' WHERE ' . $this->table_key . '=:' . $this->table_key;
            $parameters[$this->table_key] = $this->{$this->table_key};
        }
        db::query($sql, $parameters);
        if (!(isset($this->{$this->table_key}) && $this->{$this->table_key})) {
            $this->{$this->table_key} = db::insert_id();
        }
        if ($this->{$this->table_key}) {
            foreach ($this->get_fields() as $field) {
                if (get_class($field) == 'field_file') {
                    $this->do_upload_file($field);
                }
            }
        }
        return $this->{$this->table_key};
    }

    protected function do_upload_file(field $field) {
        if (isset($_FILES[$field->field_name]) && !$_FILES[$field->field_name]['error']) {
            $tmp_name = $_FILES[$field->field_name]['tmp_name'];
            $name = $_FILES[$field->field_name]['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if (!is_dir(root . 'uploads/' . get_class($this) . '/' . $this->{$this->table_key})) {
                mkdir(root . 'uploads/' . get_class($this) . '/' . $this->{$this->table_key});
            }
            move_uploaded_file($tmp_name, root . 'uploads/' . get_class($this) . '/' . $this->{$this->table_key} . '/' . $field->fid . '.' . $ext);
        }
    }

    public function get_cms_edit() {
        $form = $this->get_form();
        $form->set_from_object($this);
        foreach ($this->get_fields() as $field) {
            if (get_class($field) == 'field_file') {
                $form->action = '/index.php?module=' . get_class($this) . '&act=do_submit&no_ajax=on&ajax_origin=' . $form->id;
                break;
            }
        }
        if (!isset($this->{$this->table_key}) || !$this->{$this->table_key}) {
            $form->get_field_from_name($this->table_key)->set_attr('hidden', true);
            $form->{'parent_' . $this->table_key} = 0;
        }
        return $form->get_html();
    }

    public function  get_form() {
        return new form($this->get_fields());
    }

    public function lazy_load($fields) {
        $this->do_retrieve_from_id($fields, $this->{$this->table_key});
    }

    /** @return html_node */
    public function get_cms_edit_module() {
        $list = html_node::create('table#module_def');
        $list->nest(html_node::create('thead')->nest(array(
                    html_node::create('th', 'Field id'),
                    html_node::create('th', 'Pos'),
                    html_node::create('th', 'Title'),
                    html_node::create('th', 'Database Title'),
                    html_node::create('th', 'Type'),
                    html_node::create('th', 'List'),
                    html_node::create('th', 'Required'),
                    html_node::create('th', 'Filter'),
                )
            )
        );

        foreach ($this->get_fields() as $field) {
            $list->add_child(html_node::create('tr')->nest($field->get_cms_admin_edit()));
        }
        return $list;
    }

    public function get_cms_list() {
        foreach ($this->get_fields() as $field) {
            if ($field->list) {
                $nodes[] = html_node::create('td.' . get_class($field), $field->get_cms_list_wrapper(isset($this->{$field->field_name}) ? $this->{$field->field_name} : '', get_class($this), $this->{$this->table_key}));
            }
        }
        return $nodes;
    }

    public function get_fields() {
        $fields = self::_get_fields();
        foreach ($fields as $field) {
            $field->parent_form = $this;
        }
        return $fields;
    }

    private static function _get_fields() {
        if (!isset(static::$fields)) {
            $fields = [];
            $res = db::query('SELECT * FROM _cms_fields WHERE mid=:mid ORDER BY `position` ASC', array('mid' => static::$module_id,));
            while ($row = db::fetch($res)) {
                $class = 'field_' . $row->type;
                $field = new $class($row->title, array());
                $field->set_from_row($row);
                $fields[] = $field;
            }
            static::$fields = $fields;
        }
        $clone = array();
        foreach (static::$fields as $key => $field) {
            $clone[$key] = clone $field;
        }
        return $clone;
    }
}

class table_array extends ArrayObject {

    protected static $statics_set = false;
    /* @var table_iterator */
    public $iterator;
    protected $retrieved_fields = array();
    protected $original_retrieve_options = array();

    public function __construct() {
        if (!self::set_statics()) {
            $this->set_statics();
        }
    }

    protected function set_statics() {
        self::$statics_set = true;
    }

    public function remove_first($int = 1) {
        parent::__construct($this->subset($int));
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

    public function has_field($name) {
        return (isset($this->retrieved_fields[$name]) ? $this->retrieved_fields[$name] : false);
    }

    public function do_retrieve($fields, $options) {
        $new_list = self::get_all($fields, $options);

    }

    static function get_all(array $fields_to_retrieve, $options = array(), $log_sql = 0) {
        $class = get_called_class();
        $return = new $class();
        $parameters = (isset($options['parameters']) ? $options['parameters'] : array());
        $sql = db::get_query($return->get_class(), $fields_to_retrieve, $options, $parameters);
        $res = db::query($sql, $parameters);
        if (db::num($res)) {
            while ($row = db::fetch($res, $return->get_class())) {
                $return[] = $row;
            }
        }
        $return->reset_iterator();
        return $return;
    }

    public function reset_iterator() {
        $iterator_class = $this->get_class() . '_iterator';
        $iterator = new $iterator_class($this->subset());
        $this->iterator = $iterator;
    }

    public function get_class() {
        return str_replace('_array', '', get_class($this));

    }

    public function iterate($function) {
        $cnt = 0;
        while ($obj = $this->next()) {
            $cnt++;
            call_user_func_array($function, array($obj, $cnt));
        }
    }

    public function next() {
        if ($this->iterator->index == -1) {
            $this->iterator->index = 0;
            if ($this->iterator->valid())
                return $this->iterator->current();
            else return false;
        } else {
            $this->iterator->next();
            $this->iterator->index++;
            if ($this->iterator->valid())
                return $this->iterator->current();
            else return false;
        }
    }

    public function remove_last($int = 0) {
        if ($int) {
            for ($i = 0; $i < $int; $i++)
                $this->remove_last();
        } else {
            $this->offsetUnset($this->count() - 1);
        }
    }

    public function last() {
        return $this[$this->count() - 1];
    }

    public function first() {
        return $this[0];
    }

    public function inject($index, $object) {
        $start = $this->subset(0, $index - 1);
        $end = $this->subset($index);
        $this->exchangeArray(array_merge($start, $object, $end));
    }

    public function iterator_cnt() {
        return $this->iterator->index;
    }

    public function rewind() {
        $this->iterator->rewind();
    }
}

class table_iterator extends ArrayIterator {
    public $index = -1;

    public function rewind() {
        $this->index = -1;
        parent::rewind();
    }

    public function reset() {

    }
}
