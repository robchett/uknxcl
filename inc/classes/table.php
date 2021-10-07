<?php

namespace classes;

use classes\interfaces\model_interface;
use core;
use db\insert;
use db\update;
use Exception;
use form\field;
use form\field_file;
use form\field_fn;
use form\field_link;
use form\field_textarea;
use form\schema;
use module\cms\model\_cms_table_list;
use RuntimeException;

trait table
{
    public function __construct(
        public bool $live,
        public bool $deleted,
        public int $created,
        public int $ts,
        public int $position
    ) {
    }

    public function get_tablename(): string
    {
        return $this::get_schema()->table_name;
    }

    public static function getFromId(int $id): self|false
    {
        return static::get(new tableOptions(limit: '1', where_equals: [schema::getFromClass(get_called_class())->primary_key => $id]));
    }

    /** @psalm-suppress MoreSpecificReturnType */
    public static function get(tableOptions $options): self|false
    {
        $module = schema::getFromClass(get_called_class());
        $options->limit = '1';
        [$res, $aliases] = $module->getFetchQuery($options);
        if (db::num($res) && ($row = db::fetch($res))) {
            return $module->processRow($row, $aliases);
        }
        return false;
    }

    /**
     * @return table_array<self>
     * @psalm-suppress InvalidReturnType, ImplementedReturnTypeMismatch
     */
    public static function get_all(tableOptions $options): table_array
    {
        return table_array::get_all(get_called_class(), $options);
    }

    public function get_primary_key_name(): string
    {
        return $this::get_schema()->primary_key;
    }

    public static function get_schema(): schema
    {
        return schema::getFromClass(get_called_class());
    }

    /**
     * @return field[]
     */
    public static function get_fields(): array
    {
        return self::get_schema()->fields;
    }

    public function class_name(): bool|string
    {
        return get::__class_name($this);
    }

    public function get_primary_key(): int
    {
        if (isset($this->{$this->get_primary_key_name()}) && $this->{$this->get_primary_key_name()}) {
            return (int) $this->{$this->get_primary_key_name()};
        }
        return 0;
    }

    protected function set_primary_key(int $i): void
    {
        $this->{$this->get_primary_key_name()} = $i;
    }

    public function get_parent_primary_key(): int
    {
        if (isset($this->{'parent_' . $this->get_primary_key_name()}) && $this->{'parent_' . $this->get_primary_key_name()}) {
            return (int) $this->{'parent_' . $this->get_primary_key_name()};
        }
        return 0;
    }

    public static function do_cms_update(): int
    {
        if (core::is_admin() && is_scalar($_REQUEST['value'])) {
            $module = self::get_schema();
            return (int) db::update($module->table_name)
                ->add_value((string) $_REQUEST['field'], $_REQUEST['value'])
                ->filter_field($module->primary_key, (int) $_REQUEST['id'])
                ->execute();
        }
        return 1;
    }

    /**
     * @throws Exception
     */
    public static function get_form(): table_form
    {
        $form = new table_form(get_called_class());
        $form->id = str_replace('\\', '_', get_called_class() . '_form');
        if (!isset($form->attributes->target)) {
            $form->attributes->target = 'form_target_' . $form->id;
        }
        $form->get_field_from_name(self::get_schema()->primary_key)->hidden = true;
        return $form;
    }

    public static function do_save(array $data): int
    {
        $module = schema::getFromClass(get_called_class());
        if (isset($data[$module->primary_key]) && is_int($data[$module->primary_key]) && $data[$module->primary_key]) {
            $query = new update($module->table_name);
            $query->filter_field($module->primary_key, $data[$module->primary_key]);
        } else {
            $query = new insert($module->table_name);
            $top_pos = (int) db::select($module->table_name)->add_field_to_retrieve('max(position) as pos')->execute()->fetchObject()->pos;
            $query->add_value('position', $top_pos ?: 1);
        }
        foreach ($module->fields as $field) {
            if ($field->field_name == $module->primary_key || $field instanceof field_file || !isset($data[$field->field_name])) {
                continue;
            }
            if (!$data[$field->field_name] && $field instanceof field_fn && isset($data['title']) && is_string($data['title'])) {
                $data[$field->field_name] = get::unique_fn($module->table_name, $field->field_name, $data['title']);
            }
            try {
                $val = $field->get_save_sql($data[$field->field_name]);
                unset($data[$field->field_name]);
                $query->add_value($field->field_name, $val);
            } catch (RuntimeException) {
            }
        }

        $query->add_value('live', (int) ($data['live'] ?? true));
        $query->add_value('deleted', (int) ($data['deleted'] ?? false));
        $query->add_value('ts', date('Y-m-d H:i:s'));
        if ($key = (int) $query->execute()) {
            foreach ($module->fields as $field) {
                if ($field instanceof field_file) {
                    $field->do_upload_file($module, $key);
                }
            }
        }
        return $key;
    }

    public function do_submit(): bool
    {
        $type = (!$this->get_primary_key() ? 'Added' : 'Updated');

        ajax::add_script('$(".bs-callout-info").remove()', true);
        $lower = strtolower($type);
        ajax::inject('#' . ((string)$_REQUEST['ajax_origin']), 'before', "<div class='bs-callout bs-callout-info {$lower}'><p>{$type} successfully</p></div>");
        return true;
    }

    /**
     * @throws Exception
     */
    public function get_cms_edit(): string
    {
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
            } else if ($field instanceof field_link) {
                $field->order = 'title';
            }
            $field->label .= ' <small class="field_name">(' . $field->field_name . ')</small>';
            $field->raw = true;
        }
        if (!$this->get_primary_key()) {
            $form->get_field_from_name($this->get_primary_key_name())->hidden = true;
            $form->{'parent_' . $this->get_primary_key_name()} = 0;
        }
        return $form->get_html();
    }

    public function get_form_ajax(): void
    {
        $html = utf8_encode($this->get_form()->get_html());
        jquery::colorbox(['html' => $html]);
    }

    public function get_cms_list(): string
    {
        $fields = $this->get_fields();
        $json = ["mid" => static::get_schema()->table_name, "id" => $this->get_primary_key()];
        $live_attributes = new attribute_list(href: '#', dataAjaxClick: attribute_callable::create([$this, 'do_toggle_live']), dataAjaxPost: json_encode($json));
        $up_attributes = new attribute_list(dataAjaxClick: attribute_callable::create([$this, 'do_reorder']), dataAjaxPost: json_encode($json + ["dir" => "up"]));
        $down_attributes = new attribute_list(dataAjaxClick: attribute_callable::create([$this, 'do_reorder']), dataAjaxPost: json_encode($json + ["dir" => "down"]));
        $delete_attributes = $undelete_attributes = $true_delete_attributes = new attribute_list(dataAjaxPost: json_encode($json), dataToggle: 'modal', dataTarget: '#delete_modal');
        $undelete_attributes->dataTarget = '#undelete_modal';
        $true_delete_attributes->dataTarget = '#true_delete_modal';
        return "
        <td class='btn-col'><a class='btn btn-primary' href='/cms/edit/" . static::get_schema()->table_name . "/{$this->get_primary_key()}'>" . icon::get('pencil') . "</a></td>
        <td class='bnt-col'><a class='btn btn-primary' $live_attributes>" . icon::get($this->live ? 'ok' : 'remove') . "</a></td>
        <td class='btn-col2'><a class='btn btn-primary' $up_attributes>" . icon::get('arrow-up')  . "</a><a class='btn btn-primary' $down_attributes>" . icon::get('arrow-down') . "</a></td>
        " . array_reduce(array_filter($fields, fn ($field) => $field->list), fn (string $a, field $field) => $a . "<td class='" . get_class($field) . "'>{$field->get_cms_list_wrapper($this,$this->{$field->field_name} ?? '', get_class($this),$this->get_primary_key())}</td>", "") . " 
        <td class='btn-col'>" . ($this->deleted ? "<button class='delete btn btn-info' $undelete_attributes><s>" . icon::get('trash') . "</s></button><button class='delete btn btn-warning' $true_delete_attributes><s>" . icon::get('fire') . "</s></button>" : "<button class='delete btn btn-warning' $delete_attributes>" . icon::get('trash') . "</button");
    }

    public static function do_reorder(): void
    {
        if (isset($_REQUEST['id']) && ($object = static::getFromId((int) $_REQUEST['id']))) {
            if (isset($_REQUEST['dir']) && $_REQUEST['dir'] == 'down') {
                db::update(get::__class_name($object))->add_value('position', $object->position)->filter_field('position', $object->position + 1)->execute();
                db::update(get::__class_name($object))->add_value('position', $object->position + 1)->filter_field($object->get_primary_key_name(), $object->get_primary_key())->execute();
            } else {
                db::update(get::__class_name($object))->add_value('position', $object->position)->filter_field('position', $object->position - 1)->execute();
                db::update(get::__class_name($object))->add_value('position', $object->position - 1)->filter_field($object->get_primary_key_name(), $object->get_primary_key())->execute();
            }
            $list = new _cms_table_list(self::get_schema(), 1);
            ajax::update($list->get_table());
        }
    }

    public static function do_toggle_live(): void
    {
        if (isset($_REQUEST['id']) && ($object = static::getFromId((int) $_REQUEST['id']))) {
            static::do_save(['live' => !$object->live, $object->get_primary_key_name() => $object->get_primary_key()]);
            $module = schema::getFromClass((string) $_REQUEST['mid']);
            $list = new _cms_table_list($module, 1);
            ajax::update($list->get_table());
        }
    }

    public function get_title(): string
    {
        /** @psalm-suppress all */
        return (string) ($this->title ?? '');
    }

    public function is_live(): bool
    {
        return $this->live;
    }

    public function is_deleted(): bool
    {
        return $this->deleted;
    }

    public function get_url(): string
    {
        return '';
    }

    public function format_date(string|int $date, string $format = 'Y-m-d'): bool|string
    {
        return date($format, is_numeric($date) ? (int) $date : (strtotime($date) ?: 0));
    }
}
