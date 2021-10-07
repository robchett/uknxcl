<?php

namespace form;

use classes\ajax;
use classes\jquery;
use classes\tableOptions;
use html\node;
use model\pilot;
use module\add_flight\form\igc_form;

class add_pilot_form extends form
{
    public string $name;
    public int $bhpa_no;
    public string $email;
    public int $gid;
    public int $prid;

    public function __construct()
    {
        parent::__construct([
            new field_string('name', label: 'Name'),
            new field_int('bhpa_no', label: 'BHPA Number'),
            new field_email('email', label: 'Email'),
            new field_select('gid', options: [1 => 'Male', 2 => 'Female'], label: 'Gender'),
            new field_select('prid', options: [1 => 'Club', 2 => 'Advanced'], label: 'Rating'),
        ]);

        $this->id = 'new_pilot_form';
        $this->h2 = 'Create a new pilot';
        $this->attributes->class[] = 'form-compact';
        $this->wrapper_class[] = 'callout';
        $this->wrapper_class[] = 'callout-primary';
    }

    public static function get_form(): void
    {
        $t = new self();
        jquery::colorbox(['html' => $t->get_html()]);
    }

    public function do_submit(): bool
    {
        if($id = pilot::do_save([
            'name' => ucwords($this->name),
            'gid' => $this->gid,
            'prid' => $this->prid,
            'bhpa_no' => $this->bhpa_no,
            'email' => $this->email,
        ])) {
            $field = new \form\field_link('pid', link_module: pilot::class, link_field: 'name', options: new tableOptions(order: 'name'));
            $form = new igc_form;
            $form->pid = $id;
            ajax::update($field->get_html($form));
            jquery::colorbox(['html' => node::create('div.callout.callout-primary', [], node::create('h2.page-header', [], $this->name) . "<p>Added to the database<p>")]);
        }
        return true;
    }
}
