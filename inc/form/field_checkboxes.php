<?php

namespace form;

use JetBrains\PhpStorm\Pure;

class field_checkboxes extends field {

    public array $options = [];
    public int|array|string $value = [];

    public function __construct($name, $options) {
        $this->options = $options;
        parent::__construct($name);
    }

    #[Pure]
    public function get_html(): string {
        $html = '';
        foreach ($this->options as $key => $value) {
            if (is_array($value)) {
                $html .= '<div class="checkboxes_wrapper">';
                $html .= '<span class="legend">' . $key . '</span>';
                foreach ($value as $_key => $_value) {
                    $html .= $this->get_inner_html($_key, $_value);
                }
                $html .= '</div>';
            } else {
                $html .= $this->get_inner_html($key, $value);
            }
        }
        return $html;
    }

    #[Pure]
    protected function get_inner_html($key, $value): string {
        return '
        <label class="checkbox">
            <input type="checkbox" name="' . $this->field_name . '[]" value="' . $key . '" ' . (in_array($key, $this->parent_form->{$this->field_name}) ? 'checked="checked"' : '') . '>' . $value . '
        </label>';
    }

    public function set_from_request() {
        $this->parent_form->{$this->field_name} = (isset($_REQUEST[$this->field_name]) ? $_REQUEST[$this->field_name] : []);
    }
}
