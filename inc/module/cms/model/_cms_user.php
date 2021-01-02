<?php

namespace module\cms\model;

use classes\table;


class _cms_user extends table {


    public int $last_login;
    public string $last_login_ip;
    public string $title;
    public string $password;
    public int $ulid;

}
 