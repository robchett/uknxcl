<?php

namespace module\cms\view;

use html\node;
use module\cms\controller;

class admin_edit extends cms_view {

    /** @var controller */
    public \classes\module $module;

    public function get_view(): string {
        return node::create('div.container', [], [$this->module->module->get_cms_edit_module(), $this->module->module->get_fields_list(), node::create('button.btn.btn-default.btn-block', ['href' => '/?module=' . get_class($this->module->module) . '&act=get_new_field_form&mid=' . $this->module->module->mid, 'data-target' => '#modal', 'data-toggle' => 'modal'], 'Add another field'), node::create('div#modal.modal.fade div.modal-dialog div.modal-content', ['role' => 'dialog', 'aria-hidden' => 'true'])]);
    }
}
