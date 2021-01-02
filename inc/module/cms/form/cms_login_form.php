<?php

namespace module\cms\form;

use classes\ajax as _ajax;
use classes\ini;
use classes\session;
use form\form;
use html\node;
use module\cms\controller;
use module\cms\model\_cms_user;

class cms_login_form extends form {

    public $password;
    public $username;

    public array $bootstrap = [0, 12, 'form-horizontal'];
    public array $submit_attributes = ['class' => ['btn-block', 'btn-lg']];
    /**
     * @var node
     */
    public node $pre_fields_text;

    public function __construct() {
        $fields = [form::create('field_string', 'username')->set_attr('label', 'Username'), form::create('field_password', 'password')->set_attr('label', 'Password')];
        parent::__construct($fields);
        $this->pre_fields_text = node::create('h2.form-signin-heading.text-center', [], "<small>Admin Login<br/><small>" . ini::get('site', 'title_tag'));
        $this->submit = 'Login';
        $this->id = 'cms_login';
    }

    public function do_validate(): bool {
        parent::do_validate();
        $user = new _cms_user();
        $user->do_retrieve([], ['where_equals' => ['title' => $this->username, 'password' => md5($this->password)]]);
        if ($user->get_primary_key()) {
            $user->last_login = time();
            $user->last_login_ip = ip;
        } else {
            $this->validation_errors['username'] = 'Username and password combination does not match.';
        }
        return !count($this->validation_errors);
    }

    public function do_form_submit(): bool {
        controller::do_database_repair();
        return parent::do_form_submit();
    }

    public function do_submit(): bool {
        session::set(true, 'admin');
        _ajax::current()->redirect = '/cms/dashboard';
        return true;
    }
}
