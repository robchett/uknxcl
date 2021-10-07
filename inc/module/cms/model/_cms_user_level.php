<?php

namespace module\cms\model;

use classes\table;
use classes\interfaces\model_interface;

class _cms_user_level implements model_interface {
    use table;

    public string $title;
    private int $ulid;

}
 