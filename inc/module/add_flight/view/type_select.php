<?php

namespace module\add_flight\view;

use classes\module;
use template\html;

class type_select extends html {
    /** @vat \module\comps\controller */
    public module $module;

    function get_view(): string {
        return "
<div class='editable_content'>
    {$this->module->page_object->body}
</div>";
    }
}
