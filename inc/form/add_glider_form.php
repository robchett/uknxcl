<?php

namespace form;

use classes\ajax;
use classes\jquery;
use classes\tableOptions;
use html\node;
use model\glider;
use model\manufacturer;
use module\add_flight\form\igc_form;
use module\add_flight\form\igc_upload_form;

class add_glider_form extends form
{

    public int $mid;
    public string $name;
    public int $class;
    public bool $kingpost;
    public bool $single_surface;
    public glider $glider;

    public function __construct()
    {
        parent::__construct([
            new field_string('name', label: 'Name'),
            new field_link('mid', label: 'Manufacturer', link_module: manufacturer::class, link_field: 'title', options: new tableOptions(order: 'title ASC')),
            new field_select('class', options: [1 => 'Class 1', 5 => 'Class 5'], label: 'Class'),
            new field_boolean('kingpost', label: 'Has kingpost?'),
            new field_boolean('single_surface', label: 'Is single surface?'),
        ]);

        $this->id = 'new_glider_form';
        $this->h2 = 'Add a new glider';
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
        $manu = manufacturer::getFromId($this->mid);
        if($manu && $id = glider::do_save([
            'name' => ucwords($this->name),
            'mid' => $this->mid,
            'class' => $this->class,
            'kingpost' => $this->kingpost,
            'single_surface' => $this->single_surface,
        ])) {
            $field = new \form\field_link('gid', link_module: glider::class,link_field: ['manufacturer.title', 'name'],options: new tableOptions(join: ['manufacturer' => 'manufacturer.mid = glider.mid'], order: 'manufacturer.title, glider.name'));
            $form = new igc_form;
            $form->gid = $id;
            ajax::update($field->get_html($form));
            jquery::colorbox(["html" => node::create('div.callout.callout-primary', [], node::create('h2.page-header', [], $manu->title . ' - ' . ucwords($this->name)) . "<p>Has been added to the database and should now be selectable from the list.<p>")]);
        }
        return true;
    }
}
