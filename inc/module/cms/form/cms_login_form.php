<?php

namespace module\cms\form;

use classes\ajax as _ajax;
use classes\session;
use form\form;
use html\node;
use module\cms\controller;
use module\cms\model\_cms_user;

class cms_login_form extends form {

    public string $password;
    public string $username;

    /** @var array{int, int, string} */
    public array $bootstrap = [0, 12, 'form-horizontal'];

    public function __construct() {
        $fields = [
            new \form\field_string('username',label: 'Username'),
            new \form\field_password('password',label: 'Password'),
        ];
        parent::__construct($fields);
        $this->submit_attributes->class = ['btn-block', 'btn-lg'];
        $this->pre_fields_text = node::create('h2.form-signin-heading.text-center', [], "<small>Admin Login<br/><small>UKNXCL National Cross Country League");
        $this->submit = 'Login';
        $this->id = 'cms_login';
    }

    public function do_validate(): bool {
        parent::do_validate();
        $user = _cms_user::get(new \classes\tableOptions(where_equals: ['title' => $this->username, 'password' => md5($this->password)]));
        if ($user && $user->get_primary_key()) {
            $user->last_login = time();
            $user->last_login_ip = ip;
        } else {
            $this->validation_errors['username'] = 'Username and password combination does not match.';
        }
        return !count($this->validation_errors);
    }

    public function do_submit(): bool {
        session::set(true, 'admin');
        _ajax::current()->redirect = '/cms/dashboard';
        return true;
    }
}
