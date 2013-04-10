<?php

class html_element {
    public function get_attributes() {
        $this->set_standard_attributes();
        $html = '';
        foreach ($this->attributes as $key => $val) {
            $html .= $key . ' = \'' . $val . '\' ';

        }
        return $html;
    }


    public function set_standard_attributes() {
    }
}
