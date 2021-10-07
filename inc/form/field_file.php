<?php

namespace form;

use classes\attribute_list;
use classes\get;
use classes\icon;
use classes\interfaces\model_interface;
use classes\table;
use html\node;
use model\flight;
use RuntimeException;

/**
 * @extends field<string>
 */
class field_file extends field
{

    /**
     * @param string[] $class
     * @param string[] $wrapper_class
     * @param string $default
     */
    public function __construct(
        string $field_name,
        int $fid = 0,
        bool $filter = false,
        string $label = '',
        bool $list = true,
        bool $live = true,
        string $pre_text = '',
        string $post_text = '',
        bool $raw = false,
        bool $required = true,
        ?attribute_list $attributes = null,
        bool $hidden =  false,
        bool $disabled = false,
        array $class = ['form-control'],
        array $wrapper_class = [],
        mixed $default = ''
    ) {
        $class = [];
        $attributes ??= new attribute_list();
        $attributes->type = 'file';
        parent::__construct(
            $field_name,
            $fid,
            $filter,
            $label,
            $list,
            $live,
            $pre_text,
            $post_text,
            $raw,
            $required,
            $attributes,
            $hidden,
            $disabled,
            $class,
            $wrapper_class,
            $default
        );
    }

    public function get_cms_list_wrapper(model_interface $form, mixed $value, string $object_class, int $id): string
    {
        $this->attributes->href = 'http://uknxcl.co.uk/?module=\model\flight&act=download&id=' . $form->get_primary_key() . '&type=igc';
        return node::create('a.btn.btn-success', $this->attributes, icon::get('save'));
    }

    public function get_html_wrapper(form $form): string
    {
        $html = $this->pre_text;

        if (!$this->hidden && $this->label) {
            $html .= node::create('label.control-label.col-md-' . $form->bootstrap[0], ['for' => $this->field_name, 'id' => $this->field_name . '_wrapper'], $this->label);
        }
        $html .= node::create(
            'div.col-md-' . $form->bootstrap[1] . ' div.fileinput.fileinput-new.input-group',
            ['dataProvides' => 'fileinput'],
            node::create('div.form-control', ['dataTrigger' => 'fileinput'], icon::get('file', 'i', ['class' => ['fileinput-exists']]) . node::create('span.fileinput-filename', [])) . node::create('span.input-group-addon.btn.btn-default.btn-file', [], node::create('span.fileinput-exists', [], 'Change') . node::create('span.fileinput-new', [], 'Select File') . $this->get_html($form)) . node::create('a.input-group-addon.btn.btn-default.fileinput-exists.fileremove', ['dataDismiss' => 'fileinput'], 'Remove')
        );
        $html .= $this->post_text;
        return $html;
    }

    public function get_save_sql(mixed $val): string
    {
        throw new RuntimeException('Can\t save this field type');
    }

    public function set_from_request(form $form): void
    {
    }

    public function do_validate(form $form): array
    {
        return [true, ''];
    }

    /**
     * @return false|string file path
     */
    public function do_upload_file(schema $form, int $key): bool|string
    {
        /** @psalm-suppress MixedArrayAccess */
        if (isset($_FILES[$this->field_name]) && !$_FILES[$this->field_name]['error']) {
            $class = $form->table_name;
            $tmp_name = (string) $_FILES[$this->field_name]['tmp_name'];
            $name = (string) $_FILES[$this->field_name]['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if (!is_dir(root . '/uploads/' . $class)) {
                mkdir(root . '/uploads/' . $class);
            }
            if (!is_dir(root . '/uploads/' . $class . '/' . $this->fid)) {
                mkdir(root . '/uploads/' . $class . '/' . $this->fid);
            }
            $file_name = root . '/uploads/' . $class . '/' . $this->fid . '/' . $key . '.' . $ext;
            move_uploaded_file($tmp_name, $file_name);
            return root . '/uploads/' . $class . '/' . $this->fid . '/' . $key . '.' . $ext;
        }
        return false;
    }
}
