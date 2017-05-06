<?php
namespace module\comps\view;

use object\flight;
use object\pilot;
use traits\twig_view;

class create extends \template\html {
    use twig_view;

    /** @vat \module\comps\controller */
    public $module;


    public function get_template_data() {
        $root = root . '/uploads/comp/' . $this->module->current->get_primary_key();
        $files = glob($root . '/*.igc');

        $flights = [];

        foreach ($files as $file) {
            $name = str_replace($root, '', $file);
            $name = str_replace('.igc', '', $name);
            $name = preg_replace('/[0-9.\-_\/]/', ' ', $name);
            $name = preg_replace('/\s+/', ' ', $name);
            $name = trim($name);
            $pilot = new pilot();
            $parts = explode(' ', $name);
            $match = false;
            if ($pilot->do_retrieve([], ['where_equals' => ['name' => $name]]) || $pilot->do_retrieve([], ['where_equals' => ['name' => implode(' ', array_reverse($parts))]])) {
                $flight = new flight();
                $match = $flight->do_retrieve([], ['where_equals' => ['pid' => $pilot->get_primary_key(), 'date' => date('Y-m-d', $this->module->current->date)]]);
            }
            $flights[] = [
                'data' => ['path' => $file, 'name' => $name, 'cid' => $this->module->current->get_primary_key()],
                'name' => $name,
                'matched' => $match
            ];
        }
        return [
            'rows' => $flights
        ];
    }
}
 