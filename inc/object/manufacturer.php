<?php

namespace object;

class manufacturer extends club {

    public $Name;
    public $Score = 0;
    public $Total = 0;
    public $Number = 1;
    public $Glider;
    public $Num;
    public $title;


    /**
     * @param $pos
     * @return string
     */
    function writeClubSemiHead($pos) {
        return '
            <div class="table_wrapper inner"><h3>
            <span class="pos">' . $pos . '</span>
            <span class="score">' . $this->score . '</span>
            <span class="name">' . $this->name . '</span>
            </h3>';
    }

}
