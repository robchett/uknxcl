<?php

namespace form;

use classes\attribute_callable;
use classes\icon;
use classes\interfaces\model_interface;
use classes\table;
use html\node;
use RuntimeException;

/**
 * @extends field<null>
 */
class field_button extends field {

    public string $title; 

    public function get_cms_list_wrapper(model_interface $form, mixed $value, string $object_class, int $id): string {
        $this->attributes->dataAjaxClick = attribute_callable::create([$object_class, $this->field_name]); 
        $this->attributes->dataAjaxPost = '{"id":' . $id . '}';
        $this->attributes->dataAjaxShroud = '#button' . $this->field_name . $id;
        return node::create('a#button_' . $this->field_name . $id . '.btn.btn-default', $this->attributes, icon::get($this->field_name));
    }

    public function get_save_sql(mixed $val): string {
        throw new RuntimeException('Can\t save this field type');
    }

    public function get_html_wrapper(form $form): string {
        return '';
    }
}
