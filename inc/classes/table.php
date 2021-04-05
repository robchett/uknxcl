<?php

namespace classes;

use classes\ajax as _ajax;
use classes\collection as _collection;
use classes\db as _db;
use classes\get as _get;
use classes\interfaces\model_interface;
use classes\session as _session;
use classes\table_array as _table_array;
use core;
use db\insert;
use db\update;
use Exception;
use form\field;
use form\field_collection as field_collection;
use form\field_file;
use form\field_fn;
use form\field_image;
use form\field_link;
use form\field_mlink;
use form\field_textarea;
use html\node;
use JetBrains\PhpStorm\Pure;
use model\filter;
use model\image_size;
use module\cms\model\_cms_field;
use module\cms\model\_cms_module;
use module\cms\model\_cms_table_list;
use RuntimeException;

/**
 * @property string table_key
 */
class table implements model_interface {

    public static array $default_fields = [];
    /**
     * @var collection
     */
    protected static collection $cms_modules;
    protected static $cms_modules_id;
    private static $table_name;
    public static $retrieve_unlive = false;
    public static $retrieve_deleted = false;
    public $live;
    public $deleted;
    public $ts;
    public string $primary_key;
    /**
     * @var int
     */
    public int $mid;
    /**
     * @var bool
     */
    public bool $raw = false;
    public $position;
    /**
     * @var false
     */
    public bool $_is_expanded;
    public $_has_child;
    public $linked_id;

    /**
     * @param array $fields
     * @param int $id
     */
    public function __construct($fields = [], $id = 0) {
        if ($id) {
            $this->do_retrieve_from_id($fields, $id);
        }
    }

    /**
     * @param array $fields
     * @param $id
     *
     * @return false
     */
    public function do_retrieve_from_id(array $fields, $id): bool {
        return $this->do_retrieve($fields, ['limit' => '1', 'where_equals' => [$this->get_primary_key_name() => $id]]);
    }

    /**
     * @param array $fields
     * @param array $options
     *
     * @return false
     */
    public function do_retrieve(array $fields, array $options): bool {
        self::set_cms_modules();
        $options['limit'] = 1;
        $this->set_default_retrieve($fields, $options);
        $links = $mlinks = [];
        table::organise_links($this, $fields, $links, $mlinks);
        foreach ($links as $module => $link_info) {
            $field = $link_info['field'];
            $retrieves = $link_info['retrieve'];
            $options['join'][$module] = $module . '.' . $field->field_name . '=' . $this->class_name() . '.' . $field->field_name;
            foreach ($retrieves as $retrieve) {
                $fields[] = $module . '.' . $retrieve;
            }
        }
        $query = _db::get_query(get_class($this), $fields, $options);
        $res = $query->execute();
        if (_db::num($res)) {
            $row = _db::fetch($res);
            $this->set_from_row($row, $links, $this->get_field_mappings(array_keys($row)));
        }
        /** @var field_link $field */
        foreach ($mlinks as $module => $fields) {
            $this->retrieve_link($fields['field'], $fields['retrieve']);
        }
        return $this->get_primary_key();
    }

    private static function set_cms_modules() {
        if (!isset(self::$cms_modules)) {
            if (!file_exists(root . '/.conf/.modules.json')) {
                static::rebuild_modules();
            }
            $data = json_decode(file_get_contents(root . '/.conf/.modules.json'));
            self::$cms_modules = new _collection();
            self::$cms_modules_id = new _collection();
            foreach ($data as $module_data) {
                $module = new _cms_module();
                foreach (_cms_module::$default_fields as $field) {
                    $module->$field = $module_data->$field;
                }
                foreach ($module_data->fields as $field_data) {
                    $cms_field = new _cms_field();
                    foreach (_cms_field::$default_fields as $field) {
                        $cms_field->$field = $field_data->$field;
                    }
                    $module->_field_elements[$cms_field->field_name] = $cms_field;
                    $module->_field_elements[$cms_field->field_name] = $cms_field->get_field();
                }
                self::$cms_modules[trim($module->get_class_name(), '\\')] = $module;
                self::$cms_modules_id[$module->mid] = $module;
            }
        }
    }

    public static function rebuild_modules() {
        $modules = _cms_module::get_all(_cms_module::$default_fields);
        $fields = _cms_field::get_all(_cms_field::$default_fields);
        $json = [];
        $modules->iterate(function (_cms_module $row) use (&$json) {
            $result = [];
            foreach (_cms_module::$default_fields as $field) {
                $result[$field] = $row->$field;
            }
            $json[$row->mid] = $result;
        });
        $fields->iterate(function (_cms_field $row) use (&$json) {
            $result = [];
            foreach (_cms_field::$default_fields as $field) {
                $result[$field] = $row->$field;
            }
            $json[$row->mid]['fields'][$row->field_name] = $result;
        });

        file_put_contents(root . '/.conf/.modules.json', json_encode($json));
    }

    public static function get_all(array $fields, array $options = []): table_array {
        $array = new _table_array();
        $array->get_all(get_called_class(), $fields, $options);
        return $array;
    }

    public function set_default_retrieve(&$fields, &$options) {
        if ($fields) {
            $fields = array_unique(array_merge($fields, ['live', 'deleted', 'position', 'ts', $this->get_primary_key_name()]));
        }
        if (!static::$retrieve_unlive) {
            $options['where_equals'][_get::__class_name($this) . '.live'] = 1;
        }
        if (!static::$retrieve_deleted) {
            $options['where_equals'][_get::__class_name($this) . '.deleted'] = 0;
        }
    }

    public function get_primary_key_name() {
        self::set_cms_modules();
        $class = isset(static::$table_name) ? static::$table_name : get_called_class();
        if (isset(self::$cms_modules[$class])) {
            return self::$cms_modules[$class]->primary_key;
        } else {
            trigger_error('Attempting to get a primary key for a table that doesn\'t exist - ' . $class);
            return null;
        }
    }

    public static function organise_links(table $object, array &$fields, &$links = [], &$mlinks = []) {
        $new_fields = [];
        foreach ($fields as $field) {
            if (strstr($field, ' AS ') === false) {
                if (strstr($field, '.') === false) {
                    foreach ($object->get_fields() as $object_field) {
                        if ($object_field instanceof field_link && get::__class_name($object_field->get_link_module()) == $field) {
                            $sub_object = $object_field->get_link_object();
                            $sub_fields = [$sub_object->get_primary_key_name()];
                            if ($sub_object->has_field('title')) {
                                $sub_fields[] = 'title';
                            }
                            if ($object_field instanceof field_mlink) {
                                $mlinks[$field] = ['field' => $object_field, 'retrieve' => $sub_fields];
                            } else {
                                $links[$field] = ['field' => $object_field, 'retrieve' => $sub_fields];
                            }
                            continue 2;
                        }
                    }
                    $new_fields[$field] = $field;
                } else {
                    $field = explode('.', $field);
                    if ($field[0] != $object->class_name()) {
                        foreach ($object->get_fields() as $object_field) {
                            if ($object_field instanceof field_link && get::__class_name($object_field->get_link_module()) == $field[0]) {
                                if ($object_field instanceof field_mlink) {
                                    if (isset($mlinks[$field[0]])) {
                                        $mlinks[$field[0]]['retrieve'][] = $field[1];
                                    } else {
                                        $sub_object = $object_field->get_link_object();
                                        $sub_fields = [$sub_object->get_primary_key_name(), $field[1]];
                                        if ($sub_object->has_field('title')) {
                                            $sub_fields[] = 'title';
                                        }
                                        $mlinks[$field[0]] = ['field' => $object_field, 'retrieve' => $sub_fields];
                                    }
                                } else {
                                    if (isset($links[$field[0]])) {
                                        $links[$field[0]]['retrieve'][] = $field[1];
                                    } else {
                                        $sub_object = $object_field->get_link_object();
                                        $sub_fields = [$sub_object->get_primary_key_name(), $field[1]];
                                        if ($sub_object->has_field('title')) {
                                            $sub_fields[] = 'title';
                                        }
                                        $links[$field[0]] = ['field' => $object_field, 'retrieve' => $sub_fields];
                                    }
                                }
                                continue 2;
                            }
                        }
                    }
                    $new_fields[] = implode('.', $field);
                }
            } else {
                $new_fields[] = $field;
            }
        }
        $fields = $new_fields;
    }

    /**
     * @param bool $clone whether to return a cloned copy of the fields our the singleton set.
     * @return field_collection
     */
    public function get_fields($clone = false): field_collection {
        return static::_get_fields($clone);
    }

    /**
     * @param $clone
     * @return field_collection
     */
    private static function _get_fields($clone): field_collection {
        self::set_cms_modules();
        $class = get_called_class();
        if (isset(self::$cms_modules[$class])) {
            $fields = self::$cms_modules[$class]->_field_elements;
        } else {
            trigger_error('Attempting to get a fields for a table that doesn\'t exist - ' . $class);
            $fields = new field_collection();
        }
        if ($clone) {
            $clone = new field_collection();
            foreach ($fields as $key => $field) {
                $clone[$key] = clone $field;
            }
            return $clone;
        } else {
            return $fields;
        }
    }

    #[Pure]
    public function class_name(): bool|string {
        return get::__class_name($this);
    }

    /**
     * @param array $row
     * @param       $links
     * @param array $mappings array mapping retrieved row to field name
     */
    public function set_from_row(array $row, $links, $mappings = []) {
        /**
         * @var string $key
         * @var field $field
         */
        foreach ($mappings as $key => $field) {
            $this->$key = $field::sanitise_from_db($row[$key]);
            unset ($row[$key]);
        }
        foreach ($row as $key => $val) {
            if (strstr($key, '@')) {
                [$module, $field] = explode('@', $key);
                if (!isset($this->$module)) {
                    foreach ($links as $link_module => $link) {
                        if ($link_module == $module) {
                            $this->$module = $link['field']->get_link_object();
                            break;
                        }
                    }
                }
                if (isset($this->$module)) {
                    $this->$module->$field = $val;
                } else {
                    $this->$key = $val;
                }
            } else {
                $this->$key = $val;
            }
        }
    }

    public function get_field_mappings($keys = []): array {
        $mappings = [];
        $fields = $this->get_fields();
        foreach ($keys as $key) {
            if ($fields->has_field($key)) {
                $mappings[$key] = $fields->get_field($key);
            }
        }
        return $mappings;
    }

    public function retrieve_link($field, $fields = []) {
        $full_class = $field->get_link_module();
        $class = get::__class_name($full_class);
        $object = new $full_class();
        $retrieve = array_merge($fields, [$object->get_primary_key_name()]);
        if ($object->has_field('title')) {
            $retrieve[] = 'title';
        }
        if ($field instanceof field_mlink) {
            $link_table = $this->class_name() . '_link_' . $class;
            $this->$class = [];
            $this->{$class . '_elements'} = $full_class::get_all($retrieve, ['join' => [$link_table => $object->class_name() . '.' . $object->get_primary_key_name() . '=' . $link_table . '.link_' . $object->get_primary_key_name()], 'where_equals' => [$link_table . '.' . $this->get_primary_key_name() => $this->get_primary_key()]]);
            $this->{$class . '_elements'}->iterate(function (table $object) use ($class) {
                $this->{$class}[] = $object->get_primary_key();
            });
        } else {
            if (!isset($this->{$field->field_name})) {
                $this->lazy_load($field->field_name);
            }
            $object->do_retrieve_from_id($retrieve, $this->{$field->field_name});
        }
    }

    /**
     * @return int
     */
    public function get_primary_key(): int {
        if (isset($this->{$this->get_primary_key_name()}) && $this->{$this->get_primary_key_name()}) {
            return $this->{$this->get_primary_key_name()};
        }
        return 0;
    }

    protected function set_primary_key($i) {
        $this->{$this->get_primary_key_name()} = $i;
    }

    /**
     * @param $fields
     */
    public function lazy_load($fields) {
        $this->do_retrieve_from_id($fields, $this->get_primary_key());
    }

    /**
     * @return int
     */
    public static function get_count(): int {
        $class = get_called_class();
        $return = new $class();
        return _db::count($class, $return->get_primary_key_name())->execute();
    }

    /**
     * @param $mid
     * @return string
     */
    public static function get_class_from_mid($mid): string {
        self::set_cms_modules();
        $module = false;
        /** @var _cms_module $_module */
        foreach (self::$cms_modules as $_module) {
            if ($_module->mid == $mid) {
                $module = $_module;
            }
        }
        if ($module) {
            return $module->get_class_name();
        } else {
            return '';
        }
    }

    public static function reset_module_fields($mid) {
        $fields = _cms_field::get_all(_cms_field::$default_fields, ['where_equals' => ['mid' => $mid]]);
        $module = new _cms_module([], $mid);
        $module = self::$cms_modules[trim($module->get_class_name(), '\\')];
        $module->_field_elements = new field_collection();
        $fields->iterate(function (_cms_field $row) use ($module) {
            $class = 'form\field_' . $row->type;
            /** @var field $field */
            $field = new $class($row->field_name, []);
            $field->label = $row->title;
            $field->set_from_row($row);
            $module->_field_elements[] = $field;
        });
    }

    public static function reload_table_definitions() {
        self::$cms_modules = null;
        self::set_cms_modules();
    }

    #[Pure]
    public function get_table_class(): bool|string {
        return get::__class_name($this);
    }

    public function get_filters(): field_collection {
        $filters = filter::get_all(['title', 'link_mid AS link_mid', 'link_fid AS link_fid', 'order'], ['where_equals' => ['link_mid' => static::get_module_id()]]);
        $filters->iterate(function (filter $filter) {
            foreach ($this->get_fields() as $field) {
                if ($field->fid == $filter->link_fid) {
                    $filter->set_field($field);
                    return;
                }
            }
            throw new RuntimeException('Filter field ' . $filter->fid . ' is linked to a field that doesn\'t belong to its module');
        });
        return $filters;
    }

    public static function get_module_id() {
        self::set_cms_modules();
        $class = get_called_class();
        if (isset(self::$cms_modules[$class])) {
            return self::$cms_modules[$class]->mid;
        } else {
            trigger_error('Attempting to get a module ID for a table that doesn\'t exist - ' . $class);
            return null;
        }
    }

    /**
     * @return int
     */
    public function get_parent_primary_key(): int {
        if (isset($this->{'parent_' . $this->get_primary_key_name()}) && $this->{'parent_' . $this->get_primary_key_name()}) {
            return $this->{'parent_' . $this->get_primary_key_name()};
        }
        return 0;
    }

    /**
     * @return int
     */
    public static function do_cms_update(): int {
        $t = new static;
        if (core::is_admin()) {
            _db::update(_get::__class_name($t))->add_value($_REQUEST['field'], $_REQUEST['value'])->filter_field($t->get_primary_key_name(), $_REQUEST['id'])->execute();
        }
        return 1;
    }

    public function get_cms_pre_list(): string {
        return '';
    }

    public function get_cms_post_list(): string {
        return '';
    }

    /**
     * @param $name
     * @return mixed
     */
    public function has_field($name): mixed {
        $fields = $this->get_fields();
        foreach ($fields as $field) {
            if ($field->field_name == $name) {
                return $field;
            }
        }
        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function do_form_submit(): bool {
        $this->raw = true;
        $this->set_from_request();
        $form = $this->get_form();
        foreach ($form->fields as $field) {
            $field->raw = true;
        }
        $form->action = get_class($this) . ':do_form_submit';
        $ok = $form->do_form_submit();
        if ($ok) {
            $this->do_save();
            $this->do_submit();
        } else {
            _ajax::update((string)$form->get_html());
        }
        return $ok;
    }

    /**
     *
     */
    public function set_from_request() {
        /** @var field $field */
        $this->get_fields()->iterate(function ($field) {
            $field->parent_form = $this;
            if ($this->raw) {
                $field->raw = true;
            }
            $field->set_from_request();
        });
    }

    /**
     * @return table_form
     * @throws Exception
     */
    public function get_form(): table_form {
        $form = new table_form($this);
        $form->id = str_replace('\\', '_', get_class($this) . '_form');
        if (isset($form->attributes['target'])) {
            $form->attributes['target'] = 'form_target_' . $form->id;
        }
        $form->get_field_from_name($this->get_primary_key_name())->hidden = true;
        return $form;
    }

    /**
     * @return false
     */
    public function do_save(): bool {
        $class = _get::__class_name($this);
        if ($this->get_primary_key()) {
            $query = new update($class);
        } else {
            $query = new insert($class);
            $top_pos = _db::select($class)->add_field_to_retrieve('max(position) as pos')->execute()->fetchObject()->pos;
            $query->add_value('position', $top_pos ?: 1);
        }
        /** @var field $field */
        $this->get_fields()->iterate(function ($field) use ($query) {
            $field->parent_form = $this;
            if ($field->field_name != $this->get_primary_key_name()) {
                if ($this->{$field->field_name} && !($field instanceof field_file) && !($field instanceof field_mlink)) {
                    if (!$this->{$field->field_name} && $field instanceof field_fn && isset($this->title)) {
                        $this->{$field->field_name} = _get::unique_fn(_get::__class_name($this), $field->field_name, $this->title);
                    }
                    try {
                        $data = $field->get_save_sql();
                        $query->add_value($field->field_name, $data);
                    } catch (RuntimeException) {

                    }
                }
            }
        });
        $query->add_value('live', (int) (isset($this->live) ? $this->live : true));
        $query->add_value('deleted', (int) (isset($this->deleted) ? $this->deleted : false));
        $query->add_value('ts', date('Y-m-d H:i:s'));
        if ($this->get_primary_key()) {
            $query->filter_field($this->get_primary_key_name(), $this->get_primary_key());
        }

        $key = $query->execute();
        if (!$this->get_primary_key()) {
            $this->set_primary_key($key);
        }

        $this->get_fields()->iterate(function ($field) {
            if ($field->field_name != $this->get_primary_key_name()) {
                if (isset($this->{$field->field_name}) && $field instanceof field_mlink) {
                    $source_module = new _cms_module(['table_name', 'primary_key'], $field->get_link_mid());
                    $module = new _cms_module(['table_name', 'primary_key'], static::get_module_id());
                    _db::delete($module->table_name . '_link_' . $source_module->table_name)->filter_field($module->primary_key, $this->get_primary_key())->execute();
                    if ($this->{$field->field_name}) {
                        foreach ($this->{$field->field_name} as $value) {
                            _db::insert($module->table_name . '_link_' . $source_module->table_name)->add_value($module->primary_key, $this->get_primary_key())->add_value('link_' . $source_module->primary_key, $value)->add_value('fid', $field->fid)->execute();
                        }
                    }
                }
            }
        });
        if ($this->get_primary_key()) {
            $this->get_fields()->iterate(function ($field) {
                if ($field instanceof field_file) {
                    $this->do_upload_file($field);
                }
            });
        }
        return $this->get_primary_key();
    }

    /**
     * @param field_file $field
     * @return false|string file path
     */
    protected function do_upload_file(field_file $field): bool|string {
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
            $file_name = root . '/uploads/' . _get::__class_name($this) . '/' . $field->fid . '/' . $this->get_primary_key() . '.' . $ext;
            move_uploaded_file($tmp_name, $file_name);

            if ($field instanceof field_image && $ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
                $image_sizes = $field->get_image_sizes();
                $image_sizes->iterate(function (image_size $image) use ($file_name) {
                    $this->do_process_image($file_name, $image);
                });
            }
            return root . '/uploads/' . _get::__class_name($this) . '/' . $field->fid . '/' . $this->get_primary_key() . '.' . $ext;
        }
        return false;
    }

    protected function do_process_image($source, image_size $size) {
        $ext = pathinfo($source, PATHINFO_EXTENSION);
        $resize = new image_resizer($source);
        $resize->resizeImage($size->max_width, $size->max_height, $size->icid == 1);
        $resize->saveImage(str_replace('.' . $ext, '', $source) . '_' . $size->reference . '.' . $size->get_format());
    }

    public function do_submit(): bool {
        $type = (!$this->get_primary_key() ? 'Added' : 'Updated');

        _ajax::add_script('$(".bs-callout-info").remove()', true);
        $lower = strtolower($type);
        _ajax::inject('#' . $_REQUEST['ajax_origin'], 'before', "<div class='bs-callout bs-callout-info {$lower}'><p>{$type} successfully</p></div>");
        return true;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function get_cms_edit(): string {
        $form = $this->get_form();
        $form->wrapper_class[] = 'container';
        $form->wrapper_class[] = 'panel';
        $form->wrapper_class[] = 'panel-body';
        $form->id = 'cms_edit';
        $form->set_from_request();
        $form->set_from_object($this);
        foreach ($form->fields as $field) {
            if ($field instanceof field_file) {
                $form->action = '/index.php?module=' . get_class($this) . '&act=do_form_submit&no_ajax=on&ajax_origin=' . $form->id;
            } else if ($field instanceof field_textarea) {
                $options = [];
                if (file_exists(root . '/js/ckeditor.js')) {
                    $options['customConfig'] = '/js/ckeditor.js';
                }
                core::$inline_script[] = 'CKEDITOR.replace("' . $field->field_name . '"' . ($options ? ', ' . json_encode($options) : '') . ');';
            } else if ($field instanceof field_mlink) {
                $class = $field->get_link_object();
                $class_name = get::__class_name($class);
                $this->do_retrieve_from_id([$class_name . '.' . $class->get_primary_key_name()], $this->get_primary_key());
            } else if ($field instanceof field_link) {
                $field->order = 'title';
            }
            $field->label .= ' <small class="field_name">(' . $field->field_name . ')</small>';
            $field->raw = true;
        }
        if (!$this->get_primary_key()) {
            $form->get_field_from_name($this->get_primary_key_name())->set_attr('hidden', true);
            $form->{'parent_' . $this->get_primary_key_name()} = 0;
        }
        return $form->get_html();
    }

    public function get_form_ajax() {
        $html = utf8_encode((string)$this->get_form()->get_html());
        jquery::colorbox(['html' => $html]);
    }

    /**
     * @return string
     */
    public function get_cms_list(): string {
        $fields = $this->get_fields(true);
        array_walk($fields, fn($f) => $f && $f->parent_form = $this);
        $json = ["mid" => static::get_module_id(),"id" => $this->get_primary_key()];
        $live_attributes = attribute_list::create(['href' => '#', 'data-ajax-click' => attribute_callable::create([$this, 'do_toggle_live']), 'data-ajax-post' => json_encode($json)]);
        $up_attributes = attribute_list::create(['data-ajax-click' => attribute_callable::create([$this,'do_reorder']), 'data-ajax-post' => json_encode($json + ["dir"=> "up"])]);
        $down_attributes = attribute_list::create(['data-ajax-click' => attribute_callable::create([$this, 'do_reorder']), 'data-ajax-post' => json_encode($json + ["dir"=> "down"])]);
        $delete_attributes = $undelete_attributes = $true_delete_attributes = attribute_list::create(['data-ajax-post' => json_encode($json), 'data-toggle' => 'modal', 'data-target' => '#delete_modal']);
        $undelete_attributes['data-target'] = '#undelete_modal';
        $true_delete_attributes['data-target'] = '#true_delete_modal';
        $expand_attributes = attribute_list::create(['href' => '#', 'data-ajax-click' => attribute_callable::create([$this, 'do_toggle_expand']), 'data-ajax-post' => $json]);
        $nestable = static::$cms_modules[get_class($this)]->nestable;
        return "
        <td class='btn-col'><a class='btn btn-primary' href='/cms/edit/" . static::get_module_id() . "/{$this->get_primary_key()}'>" . icon::get('pencil') . "</a></td>
        <td class='bnt-col'><a class='btn btn-primary' $live_attributes>" . icon::get($this->live ? 'ok' : 'remove') . "</a></td>
        " . ($nestable ? "<td class='edit " . ($this->_has_child ? '' : '.no_expand') . "'>" . ($this->_has_child ? "<a class='expand btn btn-primary' $expand_attributes>" . icon::get(!$this->_is_expanded ? 'plus' : 'minus') . "</a>": '') . "</td>" : '') . "
        <td class='btn-col2'><a class='btn btn-primary' $up_attributes>" . icon::get('arrow-up')  . "</a><a class='btn btn-primary' $down_attributes>" . icon::get('arrow-down') . "</a></td>
        " . array_reduce(array_filter($fields->getArrayCopy(), fn($field) => $field->list), fn($a, $field) => $a . "<td class='" . get_class($field) . "'>{$field->get_cms_list_wrapper(isset($this->{$field->field_name}) ? $this->{$field->field_name} : '', get_class($this), $this->get_primary_key())}</td>") . " 
        <td class='btn-col'>" . ($this->deleted ? "<button class='delete btn btn-info' $undelete_attributes><s>" . icon::get('trash') . "</s></button><button class='delete btn btn-warning' $true_delete_attributes><s>" . icon::get('fire') . "</s></button>" : "<button class='delete btn btn-warning' $delete_attributes>" . icon::get('trash') . "</button");
    }

    /**
     *
     */
    public static function do_reorder() {
        if (isset($_REQUEST['id'])) {
            /** @var table $object */
            static::$retrieve_unlive = true;
            static::$retrieve_deleted = true;
            $object = new static(['position'], $_REQUEST['id']);
            if (isset($_REQUEST['dir']) && $_REQUEST['dir'] == 'down') {
                _db::update(_get::__class_name($object))->add_value('position', $object->position)->filter_field('position', $object->position + 1)->execute();
                _db::update(_get::__class_name($object))->add_value('position', $object->position + 1)->filter_field($object->get_primary_key_name(), $object->get_primary_key())->execute();
            } else {
                _db::update(_get::__class_name($object))->add_value('position', $object->position)->filter_field('position', $object->position - 1)->execute();
                _db::update(_get::__class_name($object))->add_value('position', $object->position - 1)->filter_field($object->get_primary_key_name(), $object->get_primary_key())->execute();
            }
            $list = new _cms_table_list(self::$cms_modules[get_called_class()], 1);
            _ajax::update($list->get_table());
        }
    }

    public static function do_toggle_live() {
        if (isset($_REQUEST['id'])) {
            static::$retrieve_unlive = true;
            $object = new static(['live'], $_REQUEST['id']);
            $object->live = !$object->live;
            $object->do_save();

            $module = new _cms_module();
            $module->do_retrieve([], ['where_equals' => ['mid' => $_REQUEST['mid']]]);
            $list = new _cms_table_list($module, 1);
            _ajax::update($list->get_table());
        }
    }

    public static function do_toggle_expand() {
        if (isset($_REQUEST['id'])) {
            $module = new _cms_module();
            $module->do_retrieve([], ['where_equals' => ['mid' => $_REQUEST['mid']]]);
            if (_session::is_set('cms', 'expand', $module->mid)) {
                $value = _session::get('cms', 'expand', $module->mid);
                if (($key = array_search($_REQUEST['id'], $value)) !== false) {
                    unset($value[$key]);
                } else {
                    $value[] = $_REQUEST['id'];
                }
                _session::set($value, 'cms', 'expand', $module->mid);
            } else {
                _session::set([$_REQUEST['id']], 'cms', 'expand', $module->mid);
            }

            $list = new _cms_table_list($module, 1);
            _ajax::update($list->get_table());
        }
    }

    public function get_title(): bool {
        return (isset($this->title) ? $this->title : false);
    }

    public function is_live() {
        return $this->live;
    }

    public function is_deleted() {
        return $this->deleted;
    }

    /**
     * @return string
     */
    public function get_url(): string {
        return '';
    }

    #[Pure]
    public function format_date($date, $format = 'Y-m-d'): bool|string {
        return date($format, is_numeric($date) ? $date : strtotime($date));
    }
}
