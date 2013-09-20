<?php
namespace cms;
use form\form;

class cms_login_form extends form {

    public function __construct() {
        $fields = array(
            form::create('field_string', 'username')->set_attr('label', 'Username'),
            form::create('field_password', 'password')->set_attr('label', 'Password')
        );
        parent::__construct($fields);
        $this->h2 = 'Admin Login - UKNXCL';
        $this->submit = 'Login';
    }

    public function do_validate() {
        parent::do_validate();
        if (!($this->username == 'eacommsc' && $this->password == '***REMOVED***')) {
            $this->validation_errors['username'] = 'Username and password combination does not match.';
        }

    }

    public function do_submit() {
        if (parent::do_submit()) {
            $_SESSION['admin'] = true;
            \ajax::$redirect = '/cms/dashboard';
        }
    }

}
