<?php

namespace object;

use core\classes\table;
use html\node;
use traits\table_trait;

/**
 * Class club
 */
class club extends table {

    use table_trait;

    public $name;
    public $score = 0;
    public $total = 0;
    public $number = 1;
    public $content;
    public $max_pilots;


    /**
     * @param pilot|pilot_official $pilot
     */
    public function AddSub($pilot) {
        if ($this->number < $this->max_pilots) {
            $this->score += $pilot->score;
            $this->number++;
            $this->content .= $pilot->output($this->number);
        }
    }

    /**
     * @param pilot|pilot_official $pilot
     * @param $num
     */
    function set_from_pilot($pilot, $num) {
        $this->max_pilots = $num;
        $this->name = $pilot->club;
        $this->score = $pilot->score;
        $this->total = $this->score;
        $this->content = $pilot->output(1);
    }

    /**
     * @param $pos
     * @return string
     */
    function writeClubSemiHead($pos) {
        return node::create('h3', [],
            node::create('span.pos', [], $pos) .
            node::create('span.score', [], $this->score) .
            node::create('span.name', [], $this->name)

        );
    }
}
