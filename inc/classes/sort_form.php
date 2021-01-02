<?php

namespace classes;

use form\form;

class sort_form extends form {

    public $identifier;
    public $calling_class;
    public $sort;

    public function __construct(array $sort_options, $calling_class) {
        $this->calling_class = $calling_class;
        $final_fields[] = form::create('field_select', 'sort')->set_attr('label', 'Sort By')->set_attr('options', $sort_options);
        $final_fields[] = form::create('field_string', 'identifier')->set_attr('hidden', true);
        parent::__construct($final_fields);
    }

    public function do_submit(): bool {
        return false;
    }

    public function set_from_request() {
        parent::set_from_request();
        if (!$this->identifier) {
            $this->identifier = clean_uri;
        }
        if (ajax && $_REQUEST['act'] == 'do_sort_submit') {
            if (isset($this->identifier)) {
                session::set($this->sort, $this->calling_class, $this->identifier, 'sort');
            }
        }
    }
}