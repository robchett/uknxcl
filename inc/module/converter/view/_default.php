<?php

namespace module\converter\view;

use module\converter\form;
use template\html;

class _default extends html {

    function get_view(): string {
        $form1 = new form\coordinate_conversion_form();
        return "
<div>
    <h1 class='page-header'>UKNXCL Conversion Tools</h1>
    <p>Enter lat/lng values as decimal or space separated for seconds</p>
    <div class='callout callout-primary'>
        {$form1->get_html()}
    </div>
</div>";
    }
}
