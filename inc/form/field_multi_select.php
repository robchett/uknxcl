<?php

namespace form;

use JetBrains\PhpStorm\Pure;

class field_multi_select extends field {

    public string $default = 'Please Choose';
    public array $options = [];
    public int|array|string $value = [];

    #[Pure]
    public function get_html(): string {
        $html = '';
        $html .= '<select id="' . $this->field_name . '" name="' . $this->field_name . '" class="picker' . ($this->required ? ' false' : '') . '" multiple="multiple">';
        if (!empty($this->default)) {
            $html .= '<option value="default">' . $this->default . '</option>';
        }
        foreach ($this->options as $k => $v) {
            $html .= '<option value="' . $k . '" ' . (in_array($k, $this->value) ? 'selected="selected"' : '') . '>' . $v . '</option>';

        }
        $html .= '</select>';
        return $html;
    }

    public function set_from_request() {
        $this->parent_form->{$this->field_name} = (isset($_REQUEST[$this->field_name]) ? $_REQUEST[$this->field_name] : []);
    }
}
