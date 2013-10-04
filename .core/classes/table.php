<?php

namespace core\classes;

use classes\ajax;
use classes\get as _get;
use db\insert;
use db\update;
use form\field;
use form\field_file;
use form\form;
use html\node;
use module\cms\object\_cms_field;

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

    public static function get_all(array $fields, array $options = array()) {
        return \classes\table_array::get_all(get_called_class(), $fields, $options);
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
        $this->do_retrieve($fields, array('limit' => '1', 'where_equals' => [$this->table_key => $id]));
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
        return db::count($class, $return->table_key)->execute();
    }

    /**
     * @return int
     */
    public function do_cms_update() {
        if (admin) {
            db::update(_get::__class_name($this))->add_value($_REQUEST['field'], $_REQUEST['value'])->filter_field($this->table_key, $_REQUEST['id'])->execute();
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
                /** @var field $class */
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
        $sql = db::get_query(get_class($this), $fields, $options, $parameters);
        $res = db::query($sql, $parameters);
        if (db::num($res)) {
            $this->set_from_row(db::fetch($res));
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
        /** @var field $field */
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
        $class = _get::__class_name($this);
        if (isset($this->{$this->table_key}) && $this->{$this->table_key}) {
            $query = new update($class);
        } else {
            $query = new insert($class);
        }
        /** @var field $field */
        foreach ($this->get_fields() as $field) {
            if ($field->field_name != $this->table_key) {
                if (isset($this->{$field->field_name})) {
                    try {
                        $data = $field->get_save_sql();
                        $query->add_value($field->field_name, $data);
                    } catch (\RuntimeException $e) {

                    }
                }
            }
        }
        if (isset($this->{$this->table_key}) && $this->{$this->table_key}) {
            $query->filter_field($this->table_key, $this->{$this->table_key});
        }
        $query->execute();
        if (!(isset($this->{$this->table_key}) && $this->{$this->table_key})) {
            $this->{$this->table_key} = db::insert_id();
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
     * @param field_file $field
     */
    protected function do_upload_file(field_file $field) {
        if (isset($_FILES[$field->field_name]) && !$_FILES[$field->field_name]['error']) {
            $tmp_name = $_FILES[$field->field_name]['tmp_name'];
            $name = $_FILES[$field->field_name]['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if (!is_dir(root . '/uploads/' . _get::__class_name($this) . '/' . $this->{$this->table_key})) {
                mkdir(root . '/uploads/' . _get::__class_name($this) . '/' . $this->{$this->table_key});
            }
            move_uploaded_file($tmp_name, root . '/uploads/' . _get::__class_name($this) . '/' . $this->{$this->table_key} . '/' . $field->fid . '.' . $ext);
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
     * @return form
     */
    public function get_form() {
        $form = new form($this->get_fields());
        $form->id = str_replace('\\', '_', get_class($this) . '_form');
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

    /** @return \html\node */
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

        /** @var field $field */
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
        /** @var field $field */
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
        $final_fields = [];
        $fields = _cms_field::get_all([], ['where_equals' => ['mid' => static::$module_id], 'order' => '`position` ASC']);
        $fields->iterate(function (_cms_field $row) use (&$final_fields) {
                $class = 'form\field_' . $row->type;
                /** @var field $field */
                $field = new $class($row->field_name, array());
                $field->label = $row->title;
                $field->set_from_row($row);
                $final_fields[$row->field_name] = $field;
            }
        );
        static::$fields = $final_fields;
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