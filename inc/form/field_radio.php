<?php

namespace form;

use classes\table;

/**
 * @extends field<string>
 */
class field_radio extends field {

    /** @var list<string> */
    public array $options = [];

    public function get_html(form $form): string {
        $html = '';
        foreach ($this->options as $name => $val) {
            $html .= $name . '<input type="radio" name="' . $this->field_name . '" value="' . $val . '" ' . ($val == $this->get_value($form) ? 'checked="checked"' : '') . "/>\n";
        }
        return $html;
    }
}
