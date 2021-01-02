<?php

namespace form;

class field_radio extends field {

    public array $options = [];

    public function get_html(): string {
        $html = '';
        foreach ($this->options as $name => $val) {
            $html .= $name . '<input type="radio" name="' . $this->field_name . '" value="' . $val . '" ' . ($val == $this->parent_form->{$this->field_name} ? 'checked="checked"' : '') . '/>\n";
        }
        return $html;
    }
}
