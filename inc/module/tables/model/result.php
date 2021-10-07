<?php

namespace module\tables\model;

abstract class result {

    abstract public function make_table(league_table $data): string;
    
}
