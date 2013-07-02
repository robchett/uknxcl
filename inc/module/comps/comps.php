<?php

class comps extends core_module {

    public $page = 'comp';

    public function do_generate_all() {
        $comps = comp::get_all(array());
        /** @var $comp comp */
        foreach ($comps as $comp) {
            $comp->do_zip_to_comp();
        }
        //});
    }

}
