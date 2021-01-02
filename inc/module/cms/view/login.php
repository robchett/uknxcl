<?php

namespace module\cms\view;

use classes\ini;
use Exception;
use module\cms\form\cms_builder_form;
use module\cms\form\cms_login_form;

class login extends cms_view {

    public function get(): string {
        return (string)$this->get_view();
    }

    public function get_view(): string {
        try {
            ini::get('mysql', 'server');
        } catch (Exception) {
            $form = new cms_builder_form();
            return $form->get_html();
        }
        $form = new cms_login_form();
        $form->wrapper_class[] = 'container';
        $form->wrapper_class[] = 'form-signin';
        return $form->get_html();
    }

    protected function get_nav(): string {
        return '';
    }
}
