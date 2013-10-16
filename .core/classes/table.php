<?php

namespace core\classes;

use classes\ajax as _ajax;
use classes\ajax;
use classes\collection;
use classes\get as _get;
use db\insert;
use db\update;
use form\field;
use form\field_file;
use form\form;
use html\node;
use module\cms\object\_cms_field;
use module\cms\object\_cms_module;

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
    public function  __construct($fields = [], $id = 0) {
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

    public function get_cms_pre_list() {
        return '';
    }

    public function get_cms_post_list() {
        return '';
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

    public function set_default_retieve(&$fields, &$options) {
        if ($fields) {
            $fields = array_merge($fields, ['live', 'deleted', 'position', 'ts', $this->table_key]);
        }
        if (!static::$retrieve_unlive) {
            $options['where_equals'][_get::__class_name($this) . '.live'] = 1;
        }
        if (!static::$retrieve_deleted) {
            $options['where_equals'][_get::__class_name($this) . '.deleted'] = 0;
        }
    }

    /**
     * @param array $fields
     * @param array $options
     */
    public function do_retrieve(array $fields, array $options) {
        $options['limit'] = 1;
        $parameters = (isset($options['parameters']) ? $options['parameters'] : array());
        $this->set_default_retieve($fields, $options);
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
            _ajax::inject('#' . $_REQUEST['ajax_origin'], 'before', node::create('p.success.boxed.' . strtolower($type), [], $type . ' successfully'));
        } else {
            _ajax::update($form->get_html()->get());
        }
        return $ok;
    }

    /**
     *
     */
    public function set_from_request() {
        /** @var field $field */
        $this->get_fields()->iterate(function ($field) {
                if ($this->raw) {
                    $field->raw = true;
                }
                $field->set_from_request();
            }
        );
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
        $this->get_fields()->iterate(function ($field) use ($query) {
                if ($field->field_name != $this->table_key) {
                    if (get_class($field) != 'form\\field_file') {
                        if (!$this->{$field->field_name} && get_class($field) == 'form\\field_fn' && isset($this->title)) {
                            $this->{$field->field_name} = _get::unique_fn(_get::__class_name($this), $field->field_name, $this->title);
                        }
                        if (isset($this->{$field->field_name}) && get_class($field) != 'form\\field_mlink') {
                            try {
                                $data = $field->get_save_sql();
                                $query->add_value($field->field_name, $data);
                            } catch (\RuntimeException $e) {

                            }
                        }
                    }
                }
            }
        );
        if (isset($this->{$this->table_key}) && $this->{$this->table_key}) {
            $query->filter_field($this->table_key, $this->{$this->table_key});
        }
        $res = $query->execute();

        if (!$this->get_primary_key()) {
            $this->{$this->table_key} = $res;
        }

        $this->get_fields()->iterate(function ($field) {
                if ($field->field_name != $this->table_key) {
                    if (isset($this->{$field->field_name}) && get_class($field) == 'form\\field_mlink') {
                        /** @var \form\field_mlink $field */
                        $source_module = new _cms_module(['table_name', 'primary_key'], $field->get_link_mid());
                        $module = new _cms_module(['table_name', 'primary_key'], static::$module_id);
                        db::query('DELETE FROM ' . $module->table_name . '_link_' . $source_module->table_name . ' WHERE ' . $module->primary_key . '=:key', ['key' => $this->{$this->table_key}]);
                        if ($this->{$field->field_name}) {
                            foreach ($this->{$field->field_name} as $value) {
                                db::insert($module->table_name . '_link_' . $source_module->table_name)
                                    ->add_value($module->primary_key, $this->{$this->table_key})
                                    ->add_value('link_' . $source_module->primary_key, $value)
                                    ->add_value('fid', $field->fid)
                                    ->execute();
                            }
                        }
                    }
                }
            }
        );
        if (!(isset($this->{$this->table_key}) && $this->{$this->table_key})) {
            $this->{$this->table_key} = db::insert_id();
        }
        if ($this->{$this->table_key}) {
            $this->get_fields()->iterate(function ($field) {
                    if (get_class($field) == 'form\field_file') {
                        $this->do_upload_file($field);
                    }
                }
            );
        }
        return $this->{$this->table_key};
    }

    public function get_file($fid, $size = '', $extensions = ['png', 'gif', 'jpg', 'jpeg'], $fallback = '/.core/images/no_image.png') {
        $file = root . '/uploads/' . get::__class_name($this) . '/' . $fid . '/' . $this->get_primary_key() . ($size ? '_' . $size : '') . '.';
        foreach ($extensions as $extension) {
            if (file_exists($file . $extension)) {
                return str_replace(root, '', $file) . $extension;
            }
        }
        return $fallback;;
    }

    /**
     * @param field_file $field
     * @return string file path
     */
    protected function do_upload_file(field_file $field) {
        if (isset($_FILES[$field->field_name]) && !$_FILES[$field->field_name]['error']) {
            $tmp_name = $_FILES[$field->field_name]['tmp_name'];
            $name = $_FILES[$field->field_name]['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if (!is_dir(root . '/uploads/' . _get::__class_name($this))) {
                mkdir(root . '/uploads/' . _get::__class_name($this));
            }
            if (!is_dir(root . '/uploads/' . _get::__class_name($this) . '/' . $field->fid)) {
                mkdir(root . '/uploads/' . _get::__class_name($this) . '/' . $field->fid);
            }
            move_uploaded_file($tmp_name, root . '/uploads/' . _get::__class_name($this) . '/' . $field->fid . '/' . $this->get_primary_key() . '.' . $ext);
        }
        return root . '/uploads/' . _get::__class_name($this) . '/' . $field->fid . '/' . $this->get_primary_key() . '.' . $ext;
    }

    /**
     * @return node
     */
    public function get_cms_edit() {
        $form = $this->get_form();
        $form->set_from_request();
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
        $form = new form($this->get_fields()->getArrayCopy());
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
            ) .
            $this->get_fields()->iterate_return(function ($field) {
                    return (node::create('tr', [], $field->get_cms_admin_edit()));
                }
            )
        );
        return $list;
    }

    /**
     * @return array
     */
    public function get_cms_list() {
        $fields = $this->get_fields();
        return node::create('td.edit a.edit', ['href' => '/cms/edit/' . static::$module_id . '/' . $this->get_primary_key()]) .
        node::create('td.position', [],
            node::create('a.up.reorder', ['data-ajax-click' => get_class($this) . ':do_reorder', 'data-ajax-post' => '{"mid":' . $this::$module_id . ',"id":' . $this->get_primary_key() . ',"dir":"up"}'], 'Up') .
            node::create('a.down.reorder', ['data-ajax-click' => get_class($this) . ':do_reorder', 'data-ajax-post' => '{"mid":' . $this::$module_id . ',"id":' . $this->get_primary_key() . ',"dir":"down"}'], 'Down')
        ) .
        $fields->iterate_return(function ($field) {
                if ($field->list) {
                    return node::create('td.' . get_class($field), [], $field->get_cms_list_wrapper(isset($this->{$field->field_name}) ? $this->{$field->field_name} : '', get_class($this), $this->get_primary_key()));
                }
            }
        ) .
        node::create('td.delete a.delete', ['href' => '#', 'data-ajax-click' => 'cms:do_delete', 'data-ajax-post' => '{"id":"' . $this->get_primary_key() . '","object":"' . str_replace('\\', '\\\\', get_class($this)) . '"}'], 'delete');
    }

    /**
     * @return collection
     */
    public function get_fields() {
        $fields = static::_get_fields();
        $fields->iterate(function ($field) {
                $field->parent_form = $this;
            }
        );
        return $fields;
    }

    /**
     *
     */
    public static function _set_fields() {
        $final_fields = static::$fields = [];
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
     * @return collection
     */
    private static function _get_fields() {
        if (!isset(static::$fields)) {
            static::_set_fields();
        }
        $clone = new collection();
        foreach (static::$fields as $key => $field) {
            $clone[$key] = clone $field;
        }
        return $clone;
    }

    /**
     *
     */
    public function do_reorder() {
        if (isset($_REQUEST['id'])) {
            /** @var table $object */
            $object = new static(['position'], $_REQUEST['id']);
            if (isset($_REQUEST['dir']) && $_REQUEST['dir'] == 'down') {
                db::query('UPDATE ' . _get::__class_name($object) . ' SET position =' . $object->position . ' WHERE position=' . ($object->position + 1));
                db::query('UPDATE ' . _get::__class_name($object) . ' SET position =' . ($object->position + 1) . ' WHERE ' . $object->table_key . '=' . $object->get_primary_key());
            } else {
                db::query('UPDATE ' . _get::__class_name($object) . ' SET position =' . $object->position . ' WHERE position=' . ($object->position - 1));
                db::query('UPDATE ' . _get::__class_name($object) . ' SET position =' . ($object->position - 1) . ' WHERE ' . $object->table_key . '=' . $object->get_primary_key());
            }
            ajax::add_script('document.location = document.location#' . _get::__class_name($object) . ($_REQUEST['id'] - 1));
        }
    }
}