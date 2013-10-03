<?php
namespace core\module\cms\view;

use module\cms\form\cms_login_form;

abstract class login extends cms_view {

    public function get_view() {
        $form = new cms_login_form();
        return $form->get_html();
    }
}
