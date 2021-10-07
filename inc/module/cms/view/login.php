<?php

namespace module\cms\view;
use module\cms\form\cms_login_form;

class login extends cms_view {

    public function get(): string {
        return $this->get_view();
    }

    public function get_view(): string {
        $form = new cms_login_form();
        $form->wrapper_class[] = 'container';
        $form->wrapper_class[] = 'form-signin';
        return $form->get_html();
    }

    protected function get_nav(): string {
        return '';
    }
}
