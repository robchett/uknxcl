<?php

class add_glider_form extends form {

    public function __construct() {
        $this->glider = new glider();
        parent::__construct($this->glider->get_fields());
        $this->get_field_from_name('gid')
            ->set_attr('hidden', true)
            ->set_attr('required', false);
        $this->get_field_from_name('name')->set_attr('label', 'Name');
        $this->get_field_from_name('mid')->set_attr('label', 'Manufacturer');
        $this->get_field_from_name('class')
            ->set_attr('label', 'Class')
            ->set_attr('options', array(1 => 1, 5 => 5));
        $this->get_field_from_name('kingpost')->set_attr('label', 'Has kingpost?');
        $this->get_field_from_name('single_surface')->set_attr('label', 'Is single surface?');
        $this->id = 'new_glider_form';
        $this->h2 = 'Add a new glider';
    }

    public function get_form() {
        ajax::inject('body', 'after', '<script>$.colorbox({html:' . json_encode($this->get_html()->get()) . '})</script>');
    }

    public function do_submit() {
        parent::do_submit();
        $this->glider->name = ucwords($this->name);

        $res = db::query('SELECT mid, title FROM manufacturer WHERE title LIKE :title', array(
                'title' => '%' . ucwords($this->manufacturer) . '%'
            )
        );
        if (db::num($res)) {
            $row = db::fetch($res);
            $this->glider->mid = $row->mid;
            $this->manufacturer = $row->title;
        } else {
            db::query('INSERT INTO manufacturer SET title=:title', array(
                    'title' => ucwords($this->manufacturer)
                )
            );
            $this->glider->manufacturer = db::insert_id();
        }
        $this->glider->class = $this->class;
        $this->glider->kingpost = $this->kingpost;
        $this->glider->single_surface = $this->single_surface;
        $this->glider->do_save();

        if ($this->glider->gid) {
            $this->glider->do_update_selector();
            ajax::inject('body', 'after', '<script>$.colorbox({html:"' . $this->manufacturer . ' - ' . $this->glider->name . ' has been added to the database and should now be selectable from the list."})</script>');
        }
    }
}